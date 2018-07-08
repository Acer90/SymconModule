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
            $addr = $this->ReadPropertyString("IPAddress");
            $mac_address = $this->ReadPropertyString("MACAddress");

            $ip_arr = explode(".", $addr);
            $ip = "";
            for ($i=0; $i < count($ip_arr)-1; $i++) {
                $ip .= $ip_arr[$i].".";
            }
            $ip .= "255";

            if(strstr($mac_address, "-") !== false)
                $addr_byte = explode('-', $mac_address);
            else if(strstr($mac_address, ":") !== false)
                $addr_byte = explode(':', $mac_address);

            $hw_addr = '';

            for ($a=0; $a < 6; $a++) $hw_addr .= chr(hexdec($addr_byte[$a]));

            $msg = chr(255).chr(255).chr(255).chr(255).chr(255).chr(255);

            for ($a = 1; $a <= 16; $a++) $msg .= $hw_addr;

            $s = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            if ($s != false)
            {
                $opt_ret = @socket_set_option($s, 1, 6, TRUE);
                $e = socket_sendto($s, $msg, strlen($msg), 0, $ip, 2050);
                socket_close($s);
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
            $varonline = IPS_GetObjectIDByIdent("VariableOnline", $Intid);
            $ipAdress = $this->ReadPropertyString("IPAddress");

            $fp = @fsockopen($ipAdress, 8001,$errCode, $errStr, 1);
            if (!$fp){
                if(GetValueBoolean($varonline) != false) SetValueBoolean($varonline, false);
            } else {
                if(GetValueBoolean($varonline) != true) SetValueBoolean($varonline, true);
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

            $TizenAdress = "ws://".$ipAdress.":8001/api/v2/channels/samsung.remote.control";
            return "{\"URL\": \"".$TizenAdress."\"}";
        }
    }
?>
