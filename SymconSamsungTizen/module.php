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
            $this->RegisterPropertyString("IPAddress", "192.168.178.1"); 
            $this->RegisterPropertyString("MACAddress", "aa:bb:cc:00:11:22"); 
            $this->RegisterPropertyInteger("Interval", 10);
            $this->RegisterPropertyInteger("Sleep", 1000);

            $this->RegisterPropertyInteger("CIDR", 24);
            $this->RegisterPropertyInteger("WoLPort", 9); 

            $this->ConnectParent("{3AB77A94-3467-4E66-8A73-840B4AD89582}");  
            $this->GetConfigurationForParent();

            $this->RegisterVariableBoolean("VariableOnline", "Status", "~Switch", 0);

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

            $macAddressHexadecimal = strtoupper($this->ReadPropertyString("MACAddress"));
            $ip= $this->ReadPropertyString("IPAddress");
            $cidr= $this->ReadPropertyInteger("CIDR");
            $port = $this->ReadPropertyInteger("WoLPort");

            wakeOnLan($macAddressHexadecimal, $ip, $cidr, $port, $output);

            $this->SendDebug("WOL",json_encode($output),0);
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
            $varonline = IPS_GetObjectIDByIdent("VariableOnline", $Intid);
            $ipAdress = $this->ReadPropertyString("IPAddress");

            $fp = @fsockopen($ipAdress, 8001,$errCode, $errStr, 1);
            if (!$fp){
                if(GetValueBoolean($varonline) != false){
                    SetValueBoolean($varonline, false);
                    $this->SetBuffer("Aktive", false);
                    $this->GetConfigurationForParent();
                    $this->SetStatus(104);
                }
            } else {
                if(GetValueBoolean($varonline) != true){
                    SetValueBoolean($varonline, true);
                    $this->SetBuffer("Aktive", true);
                    $this->GetConfigurationForParent();
                    $this->SetStatus(102);
                }

                fclose($fp);
            }


        }

        public function TogglePower(){
            $Intid = $this->InstanceID;
            $varonline = IPS_GetObjectIDByIdent("VariableOnline", $Intid);

            if($varonline == 0 || !IPS_VariableExists($varonline)) return false;

            if(GetValueBoolean($varonline) == true){
                SamsungTizen_SendKeys($Intid, 'KEY_POWER');
            }else{
                SamsungTizen_WakeUp($Intid);
            }

        }

        public function ReceiveData($JSONString) {
               $data = json_decode($JSONString);
               $this->SendDebug("ReceiveData", utf8_decode($data->Buffer), 0);          
        }

        public function GetConfigurationForParent() {
            $ipAdress = $this->ReadPropertyString("IPAddress");
            $active = $this->GetBuffer("Aktive");
            $origin = "http://".$ipAdress.":8001";
            $TizenAdress = "ws://".$ipAdress.":8001/api/v2/channels/samsung.remote.control?name=symcon";

            //"Open": ".$active.",
            $change = "{    
                    \"URL\": \"".$TizenAdress."\",
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

            //return "{\"URL\": \"".$TizenAdress."\", \"Open\": \"".$active."\"}";
            //return "{\"URL\": \"".$TizenAdress."\"}";
        }
    }
?>
