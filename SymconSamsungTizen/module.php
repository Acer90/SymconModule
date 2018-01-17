<?
 // Klassendefinition
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
            $this->RegisterPropertyInteger("Sleep", 1000);

            $this->RegisterPropertyInteger("VariableOnline", 0);
            $this->RegisterPropertyInteger("InstanceWebSocket", 0); 

            $this->ConnectParent("{3AB77A94-3467-4E66-8A73-840B4AD89582}");  
            $this->GetConfigurationForParent();

            //event erstellen 
            $this->RegisterTimer("CheckOnline", $this->ReadPropertyInteger("Interval"), 'SamsungTizen_CheckOnline($_IPS[\'TARGET\']);');
            $this->SetStatus(102);
        }

        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();

            $this->SetStatus(102);
            $this->SetTimerInterval("CheckOnline", $this->ReadPropertyInteger("Interval")*1000);

            $this->ConnectParent("{3AB77A94-3467-4E66-8A73-840B4AD89582}"); 
            $this->GetConfigurationForParent();
        }

        public function WakeUp(){
            $broadcast = $this->ReadPropertyString("IPAddress");
            $mac_addr = $this->ReadPropertyString("MACAddress");

            $addr_byte = explode(':', $mac_addr);  
            $hw_addr = '';  
            
            for ($a=0; $a < 6; $a++) $hw_addr .= chr(hexdec($addr_byte[$a]));  
            
            $msg = chr(255).chr(255).chr(255).chr(255).chr(255).chr(255);  
            
            for ($a = 1; $a <= 16; $a++) $msg .= $hw_addr;  
            
            // send it to the broadcast address using UDP  
            // SQL_BROADCAST option isn't help!!  
            $s = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);  
            if ($s == false)  
            {  
                //echo "Error creating socket!\n";  
                //echo "Error code is '".socket_last_error($s)."' - " . socket_strerror(socket_last_error($s));  
                return false;
            }  
            else  
            {  
                // setting a broadcast option to socket:  
                $opt_ret = socket_set_option($s, SOL_SOCKET, SO_BROADCAST, true);
                if($opt_ret < 0)  
                {  
                    //echo "setsockopt() failed, error: " . strerror($opt_ret) . "\n";  
                    return false;
                }  
                $e = socket_sendto($s, $msg, strlen($msg), 0, $broadcast, 2050);  
                echo $e; 
                socket_close($s);
                //echo "Magic Packet sent (".$e.") to ".$broadcast.", MAC=".$mac_addr; 

                return true; 
            } 

        }

        public function SendKeys(String $keys){
            $sleep = $this->ReadPropertyInteger("Sleep");
            $sleep = $sleep / 1000;
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
            $int_websocket = $this->ReadPropertyInteger("InstanceWebSocket"); 

            if(IPS_VariableExists($varonline) && IPS_GetVariable($varonline)["VariableType"] == 0){
                //old Fucntion
                //$resultat = @$this->SendDataToParent(json_encode(Array("DataID" => "{BC49DE11-24CA-484D-85AE-9B6F24D89321}", "FrameTyp" => 1, "Fin" => true, "Buffer" => ""))); 
                $send_str = '{"method":"ms.remote.control","params":{"Cmd":"Click","DataOfCmd":"","Option":"false","TypeOfRemote":"SendRemoteKey"}}';
                $resultat = WSC_SendPing($int_websocket, $send_str);
                
                //IPS_LogMessage("ReceiveData", $resultat);    
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
               //IPS_LogMessage("ReceiveData", utf8_decode($data->Buffer));          
        }

        public function GetConfigurationForParent() {
            $ipAdress = $this->ReadPropertyString("IPAddress");

            $TizenAdress = "ws://".$ipAdress.":8001/api/v2/channels/samsung.remote.control";
            return "{\"URL\": \"".$TizenAdress."\"}";
        }
    }
?>