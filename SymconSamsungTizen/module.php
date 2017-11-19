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

            $this->RegisterPropertyString("SIPAddress", "127.0.0.1");
            $this->RegisterPropertyString("SPort", "8001");
            $this->RegisterPropertyInteger("Timeout", 3);

            $this->RegisterPropertyInteger("VariableOnline", 0);

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

        public function SendKeys($keys, $sleep = 1){
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
                @$resultat = $this->SendDataToParent(json_encode(Array("DataID" => "{BC49DE11-24CA-484D-85AE-9B6F24D89321}", "FrameTyp" => 1, "Fin" => true, "Buffer" => ""))); 
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
        }
    }
?>