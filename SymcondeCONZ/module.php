<?

    class deCONZ extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }

        public function Create() {
            parent::Create();

            // Modul-Eigenschaftserstellung
            $this->RegisterPropertyString("IPAddress", "");
            $this->RegisterPropertyInteger("Port", 80);
            $this->RegisterPropertyInteger("WSPort", 443);
            $this->RegisterPropertyInteger("Interval", 60);
            $this->RegisterPropertyString("APIKey", "");
            $this->RegisterPropertyString("DeviceList", "");


            $this->ConnectParent("{3AB77A94-3467-4E66-8A73-840B4AD89582}");
            $this->GetConfigurationForParent();

            //event erstellen 
            $this->RegisterTimer("CheckOnline", $this->ReadPropertyInteger("Interval"), 'deCONZ_UpdateDevices($_IPS[\'TARGET\']);');
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

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

            $result = curl_exec($ch);
            if(curl_errno($ch) !== 0) {
                $this->SendDebug("PostData", 'cURL error when connecting to ' . $url . ': ' . curl_error($ch), 0);
            }

            curl_close($ch);
            return $result;

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
            return $result;
        }

        public function test(){
            //$this->GetData("discover", "https://dresden-light.appspot.com");
            //$this->GetData("api");
            //$this->PostData("api", "{\"username\": \"988112a4e198cc1211\",\"devicetype\": \"my application\"}");

        }

        public function GETConfiguration(){
            try {
                $data = json_decode($this->GetData("discover", "https://dresden-light.appspot.com"), true);
                IPS_SetProperty($this->InstanceID, "IPAddress", $data[0]["internalipaddress"]);
                IPS_SetProperty($this->InstanceID, "Port", $data[0]["internalport"]);

                if(IPS_HasChanges($this->InstanceID))
                {
                    IPS_ApplyChanges($this->InstanceID);
                }
                $this->GETAPIKey();

                //holen des websocktports
                $APIKey = $this->ReadPropertyString("APIKey");
                if(!empty($APIKey)){
                    $data = json_decode($this->GetData("api/".$APIKey."/config"), true);
                    print_r($data);
                    IPS_SetProperty($this->InstanceID, "WSPort", $data["websocketport"]);

                    if(IPS_HasChanges($this->InstanceID))
                    {
                        IPS_ApplyChanges($this->InstanceID);
                    }
                    $this->SetStatus(102);
                }
            } catch (Exception $e) {
                $this->SendDebug("ERROR", $e->getMessage(),0);
                $this->SetStatus(201);
            }
        }

        public function GETAPIKey(){
            try {
                //prüfen ob IpAdresse und port gesetzt sind
                $ipAdress = $this->ReadPropertyString("IPAddress");
                $port = $this->ReadPropertyInteger("Port");

                if(empty($ipAdress) || empty($port)){
                    $this->SetStatus(203);
                    return false;
                }


                $key = $this->ReadPropertyString("APIKey");
                $data = array();
                if(empty($key)){
                    $data = $this->PostData("api", "{\"devicetype\": \"IP-Symcon\"}");
                }else{
                    $data = $this->PostData("api", "{\"username\": \"".$key."\", \"devicetype\": \"IP-Symcon\"}");
                }
                $data = json_decode($data, true);
                if ( is_array($data) && count($data) > 0 && array_key_exists('success', $data[0])) {
                    IPS_SetProperty($this->InstanceID, "APIKey", $data[0][success]["username"]);
                    if(IPS_HasChanges($this->InstanceID))
                    {
                        IPS_ApplyChanges($this->InstanceID);
                    }
                    $this->SetStatus(102);
                    return true;
                }else{
                    $this->SetStatus(202);
                }
            } catch (Exception $e) {
                $this->SendDebug("ERROR", $e->getMessage(),0);
                $this->SetStatus(201);
            }
            return false;
        }

        public function UpdateDevices(){
            $test = "[{\"id\": 1,\"name\": \"Kategorie\",\"address\": \"\"},{
                \"parent\": 1,
                \"instanceID\": 53398,
                \"name\": \"Fehlerhafte Instanz\",
                \"address\": \"4\"
            }]";

            IPS_SetProperty($this->InstanceID, "DeviceList", $test);
            if(IPS_HasChanges($this->InstanceID))
            {
                IPS_ApplyChanges($this->InstanceID);
            }
        }

        public function ReceiveData($JSONString) {
            $data = json_decode($JSONString);
            $this->SendDebug("ReceiveData", utf8_decode($data->Buffer), 0);

            $data_arr = json_decode($data->Buffer, true);

            if(array_key_exists("r",$data_arr)){
                switch ($data_arr["r"]){
                    case "sensors":
                        $this->SendJSONToSensors($data->Buffer);
                        break;
                    case "lights":
                        $this->SendJSONToLights($data->Buffer);
                        break;
                    default:

                        break;
                }
            }
        }

        protected function SendJSONToSensors ($data)
        {
            // Weiterleitung zu allen Gerät-/Device-Instanzen
            $this->SendDataToChildren(json_encode(Array("DataID" => "{AE76A7E7-860B-DC48-00D1-C100202AFA1C}", "Buffer" => $data))); //  I/O RX GUI
        }

        protected function SendJSONToLights ($data)
        {
            // Weiterleitung zu allen Gerät-/Device-Instanzen
            $this->SendDataToChildren(json_encode(Array("DataID" => "{D59F4287-6E3F-3400-245A-1783EA0FB2F3}", "Buffer" => $data))); //  I/O RX GUI
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
