<?

include_once (__DIR__ . '/libs/WebHookModule.php');

class SymconJSLive extends WebHookModule {

    public function __construct($InstanceID)
    {
        parent::__construct($InstanceID, "JSLive");
    }

    public function Create() {
        //Never delete this line!
        parent::Create();

        $this->RegisterPropertyString("Password", $this->GenerateRandomPassword());
        $this->RegisterPropertyInteger("WebfrontInstanceID", 0);
        $this->RegisterPropertyInteger("TemplateCategoryID", 0);

        $this->RegisterPropertyString("LocalAddress", "");
        $this->RegisterPropertyString("RemoteAddress", $this->GetConnectAddress());

        $this->RegisterPropertyString("LocalIps", "[]");

    }

    public function ApplyChanges() {
        //Never delete this line!
        parent::ApplyChanges();
    }

    public function UpdateTemplates(){
        $templates = glob(__DIR__ ."/templates/*.html");
        $category = $this->ReadPropertyInteger("TemplateCategoryID");

        if($category == 0){
            echo "It is not allowed to use the standard directory as the template directory!";
        }

        if (!IPS_CategoryExists($category)){
            echo "The template directory does not seem to exist!";
        }

        foreach($templates as $template_path){
            $path_parts = pathinfo($template_path);
            $template_name = "(Default)" .$path_parts['filename'];

            $ScriptID = @IPS_GetScriptIDByName($template_name, $category);
            if ($ScriptID === false){
                $ScriptID = IPS_CreateScript(0);
                IPS_SetParent($ScriptID, $category);
                IPS_SetName($ScriptID, $template_name);
            }

            IPS_SetScriptContent($ScriptID, file_get_contents ($template_path));

            $this->SendDebug('UpdateTemplates', 'FileName: ' . $path_parts['filename'], 0);
        }


    }

    private function SendToAllChildren(String $buffer){
        $dataIDs = ["{79D59629-E9C5-44F1-0F34-0FBC5C88F307}"];

        foreach ($dataIDs as $id){
            $this->SendDataToChildren(json_encode([
                'DataID' => $id,
                'Buffer' => utf8_encode($buffer),
            ]));
        }
    }

    /**
     * This function will be called by the hook control. Visibility should be protected!
     */
    protected function ProcessHookData() {
        $pos = strpos($_SERVER['SCRIPT_NAME'], "/hook/JSLive/js");

        if ($pos === false) {
            //$instanceID = substr($_SERVER['SCRIPT_NAME'], strlen("/hook/JSLive/"));
            $queryData = array();
            foreach (explode("&" , $_SERVER['QUERY_STRING']) as $item){
                $pos2 = strpos($item, '=');
                if ($pos2 !== false) {
                    $p_arr = explode("=" , $item);
                    if(count($p_arr) > 2) continue;
                    $queryData[strtolower($p_arr[0])] = strtolower($p_arr[1]);
                }
            }


            if(!key_exists("instance", $queryData)) return ""; //wenn instance Parameter nicht gefunden

            //reduce any relative paths. this also checks for file existence
            $this->SendDebug('WebHook', 'Instance => ' . $queryData["instance"], 0);
            $this->SendDebug('WebHook', 'Array QUERY_STRING: ' . print_r($queryData, true), 0);
            $this->SendDebug('WebHook', 'Array Server: ' . print_r($_SERVER, true), 0);
            $path = __DIR__ . "/templates/test.html";

            header("Content-Type: text/html");


            $sendData = array("cmd" =>"getContend", "instance" => $queryData["instance"]);
            $contend = $this->SendDataToChildren(json_encode([
                'DataID' => "{79D59629-E9C5-44F1-0F34-0FBC5C88F307}",
                'Buffer' => utf8_encode(json_encode($sendData))
            ]));

            if(count($contend) == 0)  return ""; //wenn instance nicht gefunden

            //check if local
            $isLocal = $this->CheckIfLocal($_SERVER["REMOTE_ADDR"]);
            $contend = $this->ReplacePlaceholder($contend[0], $isLocal);

                //Add caching support
            $etag = md5($contend);
            header("ETag: " . $etag);
            if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && (trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag)) {
                http_response_code(304);
                return;
            }

            $compressed = gzencode($contend);
            header("Content-Encoding: gzip");
            header("Content-Length: " . strlen($compressed));
            echo $compressed;

        }else{
            $subpath = substr($_SERVER['SCRIPT_NAME'], strlen("/hook/JSLive/"));
            $path = __DIR__ . "/" .$subpath;
            if(!file_exists($path)) {
                echo "";
                return;
            }


            header("Content-Type: text/html");

            //Add caching support
            $etag = md5_file($path);
            header("ETag: " . $etag);
            if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && (trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag)) {
                http_response_code(304);
                return;
            }

            $compressed = gzencode(file_get_contents($path));
            header("Content-Encoding: gzip");
            header("Content-Length: " . strlen($compressed));
            echo $compressed;
        }
    }

    public function ReceiveData($JSONString) {
        $jsonData = json_decode($JSONString, true);
        $sceneData = json_decode($jsonData['Buffer'], true);

        $this->SendDebug("ReceiveData", $jsonData['Buffer'] . "", 0);
    }

    private function CheckIfLocal(string $ipAdress){
        $arr_localIps = json_decode($this->ReadPropertyString("LocalIps"), true);
        if(count($arr_localIps) == 0) return false;

        foreach($arr_localIps as $localIp){
            $netID = $localIp['NetID'];
            $mask = $localIp['Subnetmask'];

            //$this->SendDebug('CheckIfLocal', 'NETID: ' . $localIp['NetID']. ' MASK: ' . $localIp['Subnetmask'], 0);

            if(empty($netID) || empty($mask)) continue;

            $long = ip2long($mask);
            $base = ip2long('255.255.255.255');
            $cidr = 32-log(($long ^ $base)+1,2);

            $netID_decimal = ip2long($netID);
            $ip_decimal = ip2long($ipAdress);
            $wildcard_decimal = pow( 2, ( 32 - $cidr ) ) - 1;
            $netmask_decimal = ~ $wildcard_decimal;
            $isLocal =  (( $ip_decimal & $netmask_decimal ) == ( $netID_decimal & $netmask_decimal ));


            if($isLocal) return true;
        }

        return false;
    }

    private function ReplacePlaceholder(string $htmlData, bool $isLocal){
        $webfrontid = $this->ReadPropertyInteger("WebfrontInstanceID");
        $address = $this->ReadPropertyString("RemoteAddress");

        if($isLocal || empty($address)){
            $address = $this->ReadPropertyString("LocalAddress");
        }
        $wsaddress = $this->GetWebsocket($address, $webfrontid);


        $htmlData = str_replace("{ADDRESS}", $address, $htmlData);
        $htmlData = str_replace("{WSADDRESS}", $wsaddress , $htmlData);
        $htmlData = str_replace("{REMOTE_ADDRESS}", $this->ReadPropertyString("RemoteAddress"), $htmlData);
        $htmlData = str_replace("{REMOTE_WSADDRESS}", $this->GetWebsocket($this->ReadPropertyString("RemoteAddress"), $webfrontid), $htmlData);
        $htmlData = str_replace("{LOCAL_ADDRESS}", $this->ReadPropertyString("LocalAddress"), $htmlData);
        $htmlData = str_replace("{LOCAL_WSADDRESS}", $this->GetWebsocket($this->ReadPropertyString("LocalAddress"), $webfrontid), $htmlData);
        $htmlData = str_replace("{PASSWORD}", $this->ReadPropertyString("Password"), $htmlData);
        $htmlData = str_replace("{WEBFRONTPASSWORD}", $this->GetWebfrontPassword($webfrontid), $htmlData);
        $htmlData = str_replace("{WEBFRONTID}", $webfrontid, $htmlData);
        $htmlData = str_replace("{ISLOCAL}", ($isLocal ? 'true' : 'false'), $htmlData);

        return $htmlData;
    }

    private function GetConnectAddress(){
        $connectID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}');
        if(count($connectID) == 0){} return "";
        return CC_GetUrl($connectID);
    }

    private function GetWebsocket(string $url_str, int $webfrontid){
        if(empty($url_str)) return;

        $url_str = strtolower($url_str);
        $url = parse_url($url_str);

        $port = "";
        if(key_exists("port", $url)) $port = ":" . $url["port"];

        if($url['scheme'] == 'https'){
            return "wss://" . $url["host"]. $port . "/wfc/". $webfrontid ."/api/";
        }else{
            return "ws://" . $url["host"]. $port .  "/wfc/". $webfrontid ."/api/";
        }
    }

    private function GetWebfrontPassword(int $webfrontid){
        if(!IPS_InstanceExists($webfrontid)) return "";
        $data = json_decode(IPS_GetConfiguration($webfrontid), true);

        if(is_array($data) && key_exists("Password", $data)){
            return $data["Password"];
        }else{
            return "";
        }

    }

    private function GenerateRandomPassword(){
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}

?>