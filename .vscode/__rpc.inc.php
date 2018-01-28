<?

	class JSONRPCException extends Exception {
		//
	}

	class JSONRPC {

		private $url;
		private $username;
		private $password;

		function __construct($url) {
			$data = @parse_url($url);
			
			if(($data === false) || !isset($data['scheme']) || !isset($data['host']))
				throw new Exception("Invalid URL");
				
			$this->url = $data['scheme']."://".$data['host'];
			if(isset($data['port']))
				$this->url .= ":".$data['port'];
			if(isset($data['path']))
				$this->url .= $data['path'];
			
			if(isset($data['user']))
				$this->username = $data['user'];
			else
				$this->username = "";
				
			if(isset($data['pass']))
				$this->password = $data['pass'];
			else
				$this->password = "";
		}

		public function __call($method, $arguments) {
			return self::makeRequest($this->url, $this->username, $this->password, $method, $arguments, false);
		}

		static public function __callStatic($method, $params) {
			return self::makeRequest("http://127.0.0.1:3777/api/", "", "", $method, $params, true);
		}

		static private function makeRequest($url, $username, $password, $method, $params, $customErrorHandler) {
				
			try {
			
				if (!is_scalar($method)) {
					throw new Exception('Method name has no scalar value');
				}
				
				if (!is_array($params)) {
					throw new Exception('Params must be given as array');
				}

				$id = round(fmod(microtime(true)*1000, 10000));
				$params = array_values($params);
				$strencode = function(&$item, $key) {
					if ( is_string($item) )
						$item = utf8_encode($item);
					else if ( is_array($item) )
						array_walk_recursive($item, $strencode);
				};
				array_walk_recursive($params, $strencode);
				
				$request = Array(
									"jsonrpc" => "2.0",
									"method" => $method,
									"params" => $params,
									"id" => $id
								);
								
				$request = json_encode($request);
				
				$header = "Content-type: application/json"."\r\n";
				if(($username != "") || ($password != "")) {
					$header .= "Authorization: Basic ".base64_encode($username.":".$password)."\r\n";
				}
			
				$options = Array(
									"http" => array (
														"method"  => 'POST',
														"header"  => $header,
														"content" => $request
													)
								);
				
				$context  = stream_context_create($options);		
				$response = file_get_contents($url, false, $context);

				if($response === false)
				{
					throw new Exception('Unable to connect');
				}

				if((strpos($http_response_header[0], "200") === false)) {
					throw new Exception('Server did not respond successfully');
				}
					
				$response = json_decode($response, true);
							
				if (is_null($response)) {
					throw new Exception('Request error: No response');
				}
				
				$strdecode = function(&$item, $key) {
					if ( is_string($item) )
						$item = utf8_decode($item);
					else if ( is_array($item) )
						array_walk_recursive($item, $strdecode);
				};
				array_walk_recursive($response, $strdecode);
				
				if (isset($response['error'])) {
					throw new JSONRPCException($response['error']['message']);
				}

				if (!isset($response['id'])) {
					throw new Exception('No response id');
				} elseif ($response['id'] != $id) {
						throw new Exception('Incorrect response id (request id: ' . $id . ', response id: ' . $response['id'] . ')');
				}		
					
				return $response['result'];
				
			} catch (Exception $e) {
				
				if($customErrorHandler) {
					set_error_handler(array('JSONRPC', '__errorHandler'));
					$trace = $e->getTrace();
					trigger_error($e->getMessage().' in '.$trace[3]['file'].' on line '.$trace[3]['line'], E_USER_WARNING);
					restore_error_handler();
					return false;
				} else {
					throw $e;
				}
				
			}			
				
		}		
		
		static private function __errorHandler($errno, $errstr, $errfile, $errline)
		{
			if (!(error_reporting() & $errno)) {
				return;
			}
			echo $errstr;
			return true;
		}		
		
	}

?>