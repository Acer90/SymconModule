<?
 // Klassendefinition
    require_once('wol.php');

    class SamsungTizen extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }

        public function Create() {
            parent::Create();

            $this->SetBuffer("Aktive", false);

            // Modul-Eigenschaftserstellung
            $this->RegisterPropertyBoolean("Active", false);

            $this->RegisterPropertyString("IPAddress", "192.168.178.1"); 
            $this->RegisterPropertyString("MACAddress", "aa:bb:cc:00:11:22"); 
            $this->RegisterPropertyInteger("Interval", 10);
            $this->RegisterPropertyInteger("Sleep", 1000);
            $this->RegisterPropertyString("SSLToken", "");

            $this->RegisterPropertyInteger("CIDR", 24);
            $this->RegisterPropertyInteger("WoLPort", 9);
            $this->RegisterPropertyString("WoLPath", "");
            $this->RegisterPropertyString("WolParameter", "");

            $this->RegisterPropertyBoolean("UseSSL", true);

            $this->RegisterVariableBoolean("VariableOnline", "Status", "~Switch", 0);

            $this->ConnectParent("{3AB77A94-3467-4E66-8A73-840B4AD89582}");
            $this->GetConfigurationForParent();

            //event erstellen 
            $this->RegisterTimer("CheckOnline", $this->ReadPropertyInteger("Interval"), 'SamsungTizen_CheckOnline($_IPS[\'TARGET\']);');
            $this->SetStatus(102);

            $ParentId = @IPS_GetInstance($this->InstanceID)['ConnectionID'];
            $this->RegisterMessage($ParentId, 10505 /* IM_CHANGESTATUS */);
        }

        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();

            $this->SetStatus(102);
            if($this->ReadPropertyBoolean("Active")){
                $this->SetTimerInterval("CheckOnline", $this->ReadPropertyInteger("Interval")*1000);
            }else{
                $this->SetTimerInterval("CheckOnline", 0);
            }


            $this->ConnectParent("{3AB77A94-3467-4E66-8A73-840B4AD89582}"); 
            $this->UpdateConfigurationForParent();

        }

        public function WakeUp(){
            $ip= $this->ReadPropertyString("IPAddress");
            $cidr= $this->ReadPropertyInteger("CIDR");
            $port = $this->ReadPropertyInteger("WoLPort");
            $w_path = $this->ReadPropertyString("WoLPath");
            $w_parameters = $this->ReadPropertyString("WolParameter");

            if(!empty($w_path)){
                IPS_Execute($w_path, $w_parameters, false, false);
            }else{
                $macAddressHexadecimal = strtoupper($this->ReadPropertyString("MACAddress"));
                wakeOnLan($macAddressHexadecimal, $ip, $cidr, $port, $output);
                $this->SendDebug("WOL",json_encode($output),0);
            }
        }

        public function SendKeys(String $keys){
            $sleep = $this->ReadPropertyInteger("Sleep");
            $sleep = $sleep / 1000;
            if (strpos($keys, ';') !== false) {
                $keys_data = explode(";", $keys);
                foreach ($keys_data as $value) {
                    $send_str = '{"method":"ms.remote.control","params":{"Cmd":"Click","DataOfCmd":"'.$value.'","Option":"false","TypeOfRemote":"SendRemoteKey"}}';
                    $this->SendDataToParent(json_encode(Array("DataID" => "{BC49DE11-24CA-484D-85AE-9B6F24D89321}", "FrameTyp" => 1, "Fin" => true, "Buffer" => $send_str)));
                    sleep($sleep);
                }
            }else{
                $send_str = '{"method":"ms.remote.control","params":{"Cmd":"Click","DataOfCmd":"'.$keys.'","Option":"false","TypeOfRemote":"SendRemoteKey"}}';
                $this->SendDataToParent(json_encode(Array("DataID" => "{BC49DE11-24CA-484D-85AE-9B6F24D89321}", "FrameTyp" => 1, "Fin" => true, "Buffer" => $send_str)));
            }
        }

        public function CheckOnline(){
            $ipAdress = $this->ReadPropertyString("IPAddress");
            $useSSL = $this->ReadPropertyBoolean("UseSSL");
            $port = 8001;

            if($useSSL){
                $port = 8002;
            }

            $fp = @fsockopen($ipAdress, $port,$errCode, $errStr, 1);
            if (!$fp){
                if($this->GetValue("VariableOnline") != false){
                    $this->SetValue("VariableOnline", false);
                    //$this->GetConfigurationForParent();
                    $this->SetStatus(104);
                }
            } else {
                if($this->GetValue("VariableOnline") != true){
                    $this->SetValue("VariableOnline", true);
                    //$this->GetConfigurationForParent();
                    $this->SetStatus(102);
                }

                fclose($fp);
            }


        }

        public function TogglePower(){
            if($this->GetValue("VariableOnline") == true){
                $this->SendKeys('KEY_POWER');
            }else{
                $this->WakeUp();
            }

        }

        public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {

            $this->SendDebug("MessageSink", "Message from SenderID ".$SenderID." with Message ".$Message."\r\n Data: ".print_r($Data, true), 0);

            switch($Data[0]){
                case 102:
                    //$this->SendDebug("Connection", "Samsung Tizen connection establish", 0);
                    $this->SetValue("VariableOnline" , true);
                    $this->SetStatus(102);
                    break;
                default:
                    $this->SendDebug("Connection", "Samsung Tizen connection lost", 0);
                    $this->SetValue("VariableOnline" , false);
                    break;
            }
        }

        public function ReceiveData($JSONString) {
               $data = json_decode($JSONString);
               $this->SendDebug("ReceiveData", utf8_decode($data->Buffer), 0);
               $r_data = json_decode($data->Buffer,true);


               if(array_key_exists("event", $r_data)){
                   $event = $r_data["event"];
                   switch ($event){
                       case "ms.channel.connect":
                           if($this->ReadPropertyBoolean("UseSSL") == true){
                               $token = $r_data["data"]["token"];
                               /*$this->SendDebug("Token", "Token des Servers:". $token, 0);


                               if($token != $this->GetValue("VariableToken")){
                                   $this->SendDebug("Token", "New Token " . $token . "has been set", 0);
                                   #$this->UpdateConfigurationForParent();
                               }*/
                           }
                           $this->SendDebug("Connection", "Samsung Tizen connection establish (ms.channel.connect)", 0);
                           $this->SetValue("VariableOnline" , true);
                           break;
                       default:

                           break;
                   }

               }
        }

        public function GetConfigurationForParent() {
            $ipAdress = $this->ReadPropertyString("IPAddress");
            $useSSL = $this->ReadPropertyBoolean("UseSSL");
            $token = $this->ReadPropertyString("SSLToken");
            $active = $this->ReadPropertyBoolean("Active");

            if($useSSL){
                $origin = "https://".$ipAdress.":8002";

                if(empty($token)){
                    $active = false;
                    $address = utf8_encode("wss://".$ipAdress.":8002/api/v2/channels/samsung.remote.control?name=SVBTeW1jb25UaXplbg==");
                }else{
                    $address = utf8_encode("wss://".$ipAdress.":8002/api/v2/channels/samsung.remote.control?name=SVBTeW1jb25UaXplbg==&token=".$token);
                }
            }else{
                $origin = "http://".$ipAdress.":8001";
                $address = utf8_encode("ws://".$ipAdress.":8001/api/v2/channels/samsung.remote.control?name=SVBTeW1jb25UaXplbg=="); //IPSymconTizen
            }

            //
            $change = "{    
                    \"Open\": \".$active.\",
                    \"URL\": \"".$address."\",
                    \"Protocol\": \"\",
                    \"Version\": 13,
                    \"Origin\": \"".$origin."\",
                    \"PingInterval\": 10,
                    \"PingPayload\": \"\",
                    \"Frame\": 1,
                    \"BasisAuth\": false,
                    \"Username\": \"\",
                    \"Password\": \"\"
                }";

            $this->SendDebug("Update Websocket", $change, 0);
            return $change;
        }

        Private function UpdateConfigurationForParent() {
            $ipAdress = $this->ReadPropertyString("IPAddress");
            $useSSL = $this->ReadPropertyBoolean("UseSSL");
            $token = $this->ReadPropertyString("SSLToken");
            $active = $this->ReadPropertyBoolean("Active");
            $ParentId = @IPS_GetInstance($this->InstanceID)['ConnectionID'];

            if($useSSL){
                $origin = "https://".$ipAdress.":8002";

                if(empty($token)){
                    $active = false;
                    $address = utf8_encode("wss://".$ipAdress.":8002/api/v2/channels/samsung.remote.control?name=SVBTeW1jb25UaXplbg==");
                }else{
                    $address = utf8_encode("wss://".$ipAdress.":8002/api/v2/channels/samsung.remote.control?name=SVBTeW1jb25UaXplbg==&token=".$token);
                }
            }else{
                $origin = "http://".$ipAdress.":8001";
                $address = utf8_encode("ws://".$ipAdress.":8001/api/v2/channels/samsung.remote.control?name=SVBTeW1jb25UaXplbg=="); //IPSymconTizen
            }

            //
            $change = "{    
                    \"Open\": \".$active.\",
                    \"URL\": \"".$address."\",
                    \"Protocol\": \"\",
                    \"Version\": 13,
                    \"Origin\": \"".$origin."\",
                    \"PingInterval\": 10,
                    \"PingPayload\": \"\",
                    \"Frame\": 1,
                    \"BasisAuth\": false,
                    \"Username\": \"\",
                    \"Password\": \"\"
                }";

            $this->SendDebug("Update Websocket", "(".$ParentId.") => ".$change, 0);
            IPS_SetConfiguration($ParentId, $change);
            IPS_ApplyChanges($ParentId);
        }
    }