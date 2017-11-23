<?
<<<<<<< HEAD
    include("websocket_client.php");

    
    // Klassendefinition
=======
 // Klassendefinition
>>>>>>> Development
    class SamsungTizen extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }

        public function Create() {
            parent::Create();

            // Modul-Eigenschaftserstellung
            $this->RegisterPropertyString("IPAddress", "192.168.178.1"); 
            $this->RegisterPropertyString("MACAddress", "aa:bb:cc:00:11:22"); 
            $this->RegisterPropertyInteger("Interval", 10);

            $this->RegisterPropertyString("SIPAddress", "127.0.0.1");
            $this->RegisterPropertyString("SPort", "8001");
            $this->RegisterPropertyInteger("Timeout", 3);

            $this->RegisterPropertyInteger("VariableOnline", 0);
<<<<<<< HEAD
            

            //event erstellen
=======

            $this->ConnectParent("{3AB77A94-3467-4E66-8A73-840B4AD89582}");  
            $this->GetConfigurationForParent();

            //event erstellen 
>>>>>>> Development
            $this->RegisterTimer("CheckOnline", $this->ReadPropertyInteger("Interval"), 'SamsungTizen_CheckOnline($_IPS[\'TARGET\']);');
            $this->SetStatus(102);
        }

        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();

            $this->SetStatus(102);
            $this->SetTimerInterval("CheckOnline", $this->ReadPropertyInteger("Interval")*1000);
<<<<<<< HEAD
=======

            $this->ConnectParent("{3AB77A94-3467-4E66-8A73-840B4AD89582}"); 
            $this->GetConfigurationForParent();
>>>>>>> Development
        }

        public function WakeUp(){
            $broadcast = $this->ReadPropertyString("IPAddress");
            $mac_addr = $this->ReadPropertyString("MACAddress");
            $timeout = $this->ReadPropertyInteger("Timeout");

            if (!$fp = fsockopen('udp://' . $broadcast, 2304, $errno, $errstr, $timeout)) 
                return false; 

            $mac_hex = preg_replace('=[^a-f0-9]=i', '', $mac_addr); 

            $mac_bin = pack('H12', $mac_hex); 

            $data = str_repeat("\xFF", 6) . str_repeat($mac_bin, 16); 

            fputs($fp, $data); 
            fclose($fp); 
            return true; 

        }

<<<<<<< HEAD
        public function SendKey(string $key, $WaitforStart = false){
            $Intid = $this->InstanceID;
            $rdata = SamsungTizen_SendData($Intid, $key, $WaitforStart);

            if($rdata == "OK"){
                return true;
            }else{
                return false;
            }
            

            //$send_data = '{"method":"ms.remote.control","params":{"Cmd":"Click","DataOfCmd":"'.$key.'","Option":"false","TypeOfRemote":"SendRemoteKey"}}';
            // $sp = websocket_open($broadcast,8001, "/api/v2/channels/samsung.remote.control", $headers,$errstr,$timeout);
            // if($sp){
            //     if(is_null($key)) return true;
            //     sleep(0.2);
            //     echo $result = websocket_read($sp,$errstr);
            //     $output = json_decode($result, true);
            //     if ($output['event'] == 'ms.channel.connect') {
            //         sleep(0.2);
            //         $bytes_written = websocket_write($sp,$send_data, true);
            //         if(is_numeric($bytes_written)){
            //             $data = websocket_read($sp,$errstr);
            //             //echo "Server responed with: " . $errstr ? $errstr : $data;
            //             $this->SetStatus(102);
            //             return true;
            //         }else{
            //             return false;
            //         }
            //     }
            // }else{
            //     return false;
            // }
        }

        public function SendKeys($Intid, $keys, $WaitforStart = false){
            $Intid = $this->InstanceID;
            $key_str = "";
            $first = true;
            reset($keys);
            while (list(, $value) = each($keys)) {
                if($first){
                    $first = false;
                    $key_str = $value;
                }else{
                    $key_str = $key_str . ";" . $value;
                }
            }
            $rdata = SamsungTizen_SendData($key_str, $WaitforStart, false);

            if($rdata == "OK"){
                return true;
            }else{
                return false;
            }
        }

        public function SendData(string $keys, $Wait = false, $SendOnlyData = false){
            $ip = $this->ReadPropertyString("SIPAddress");
            $port = $this->ReadPropertyString("SPort");
            $timeout = $this->ReadPropertyInteger("Timeout");
            if(!($sock = socket_create(AF_INET, SOCK_STREAM, 0)))
            {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
                $this->SetStatus(206);
                return "ERROR";
            }

            //Connect socket to remote server
            if(!socket_connect($sock , $ip , $port))
            {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
                socket_close($sock);
                $this->SetStatus(207);
                return "ERROR";
            }
            $converted_Wait = ($Wait) ? 'true' : 'false';

            if($SendOnlyData == TRUE){
                $message = $keys;
            }else{
                $message = "WAIT=".$converted_Wait."&KEYS=".$keys;
            }

            if( ! socket_send ( $sock , $message , strlen($message) , 0))
            {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
                socket_close($sock);
                $this->SetStatus(208);
                return "ERROR";
            }

            socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>$timeout, "usec"=>0));
            //Now receive reply from server
            if(socket_recv ( $sock , $buf , 2048 , MSG_WAITALL) === FALSE)
            {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
                // socket_close($sock);
                $this->SetStatus(102);
                return $buf;
            }

            return $buf;
        }

        public function CheckOnline(){
            $Intid = $this->InstanceID;
            $varonline = $this->ReadPropertyInteger("VariableOnline");
            if(IPS_VariableExists($varonline) && IPS_GetVariable($varonline)["VariableType"] == 0){
                $rdata = SamsungTizen_SendData($Intid, "STATUS", FALSE, TRUE);
                
                switch($rdata){
                    case "TRUE" || "True" || "true":
                        SetValueBoolean($varonline, TRUE);
                        break;
                    case "FALSE" || "False" || "false":
                        SetValueBoolean($varonline, FALSE);
                        break;
                    default:
                        SetValueBoolean($varonline, FALSE);
                        break;
                }
            }
=======
        public function SendKeys($keys){
            $sleep = $this->ReadPropertyString("Sleep");
            if (strpos($keys, ';') !== false) {
                $keys_data = explode(";", $keys);
                foreach ($keys_data as $value) {
                    $send_str = '{"method":"ms.remote.control","params":{"Cmd":"Click","DataOfCmd":"'.$value.'","Option":"false","TypeOfRemote":"SendRemoteKey"}}';
                    $resultat = $this->SendDataToParent(json_encode(Array("DataID" => "{BC49DE11-24CA-484D-85AE-9B6F24D89321}", "FrameTyp" => 1, "Fin" => true, "Buffer" => $send_str))); 
                    sleep($sleep);
                }
            }else{
                $send_str = '{"method":"ms.remote.control","params":{"Cmd":"Click","DataOfCmd":"'.$keys.'","Option":"false","TypeOfRemote":"SendRemoteKey"}}';
                $resultat = $this->SendDataToParent(json_encode(Array("DataID" => "{BC49DE11-24CA-484D-85AE-9B6F24D89321}", "FrameTyp" => 1, "Fin" => true, "Buffer" => $send_str))); 
            }
        }

        public function CheckOnline(){
            $Intid = $this->InstanceID;
            $varonline = $this->ReadPropertyInteger("VariableOnline");
            if(IPS_VariableExists($varonline) && IPS_GetVariable($varonline)["VariableType"] == 0){
                $resultat = @$this->SendDataToParent(json_encode(Array("DataID" => "{BC49DE11-24CA-484D-85AE-9B6F24D89321}", "FrameTyp" => 1, "Fin" => true, "Buffer" => ""))); 
                IPS_LogMessage("ReceiveData", $resultat);    
                if($resultat == 1 || $resultat == true){
                    $resultat = true;
                }else{
                    $resultat = false;
                }
                SetValueBoolean($varonline, $resultat);
            }
        }

        public function ReceiveData($JSONString) {
               $data = json_decode($JSONString);
               IPS_LogMessage("ReceiveData", utf8_decode($data->Buffer));          
        }

        public function GetConfigurationForParent() {
            $ipAdress = $this->ReadPropertyString("IPAddress");

            $TizenAdress = "ws://".$ipAdress.":8001/api/v2/channels/samsung.remote.control";
            return "{\"URL\": \"".$TizenAdress."\"}";
>>>>>>> Development
        }
    }
?>