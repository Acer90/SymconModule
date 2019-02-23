<?

    class deCONZ extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }

        public function Create() {
            parent::Create();

            $this->SetBuffer("Aktive", false);

            // Modul-Eigenschaftserstellung
            $this->RegisterPropertyString("IPAddress", "192.168.178.1");
            $this->RegisterPropertyInteger("Port", 8090);
            $this->RegisterPropertyInteger("WSPort", 443);
            $this->RegisterPropertyInteger("Interval", 10);
            $this->RegisterPropertyString("APIKey", "");
            $this->RegisterPropertyString("Configuration", "");


            $this->ConnectParent("{3AB77A94-3467-4E66-8A73-840B4AD89582}");  
            $this->GetConfigurationForParent();

            //event erstellen 
            $this->RegisterTimer("CheckOnline", $this->ReadPropertyInteger("Interval"), 'deCONZ_CheckOnline($_IPS[\'TARGET\']);');
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

        private function GetData($path, $url = ""){
            $ipAdress = $this->ReadPropertyString("IPAddress");
            $port = $this->ReadPropertyInteger("Port");
            if($path[0] == "/" || $path[0] == "\\") $path = substr($path, 1);
            if($url == "") $url = 'http://'.$ipAdress.':'.$port.'/'.$path; else $url = $url.'/'.$path;
            echo $url;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            //curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

            $result = curl_exec($ch);
            if(curl_errno($ch) !== 0) {
                $this->SendDebug("PostData", 'cURL error when connecting to ' . $url . ': ' . curl_error($ch), 0);
            }

            curl_close($ch);
            print_r($result);
        }

        private function PostData($path, $data){
            $ipAdress = $this->ReadPropertyString("IPAddress");
            $port = $this->ReadPropertyInteger("Port");
            if($path[0] == "/" || $path[0] == "\\") $path = substr($path, 1);

            $url = 'http://'.$ipAdress.':'.$port.'/'.$path;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

            $result = curl_exec($ch);
            if(curl_errno($ch) !== 0) {
                $this->SendDebug("PostData", 'cURL error when connecting to ' . $url . ': ' . curl_error($ch), 0);
            }

            curl_close($ch);
            print_r($result);
        }

        public function test(){
            $this->GetData("api");
            $this->PostData("api", "{\"username\": \"988112a4e198cc1211\",\"devicetype\": \"my application\"}");
            echo "aaa";
        }


        public function CheckOnline(){

        }

        public function ReceiveData($JSONString) {
               $data = json_decode($JSONString);
               $this->SendDebug("ReceiveData", utf8_decode($data->Buffer), 0);          
        }

        public function GetConfigurationForParent() {
            $ipAdress = $this->ReadPropertyString("IPAddress");
            $origin = "http://".$ipAdress.":443";
            $Adress = "ws://".$ipAdress.":443";

            $change = "{    
                    \"URL\": \"".$Adress."\",
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

            return $change;
        }
    }
?>
