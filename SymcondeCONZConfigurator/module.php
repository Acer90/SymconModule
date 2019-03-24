<?

    class deCONZConfigurator extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }

        public function Create() {
            parent::Create();

            $this->RegisterPropertyInteger("Kategorie", 0);

            $this->ConnectParent("{8AA55C67-B28A-C67B-5332-99CCE8190ACA}");

            //event erstellen
            $this->SetStatus(102);
        }

        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();

            $this->ConnectParent("{8AA55C67-B28A-C67B-5332-99CCE8190ACA}");
        }

        private function GetData($path){
            $ParentId = @IPS_GetInstance($this->InstanceID)['ConnectionID'];
            if($ParentId == 0) return "";
            $arr = json_decode(IPS_GetConfiguration ($ParentId), true);

            $apikey = $arr["APIKey"];
            $ipAdress = $arr["IPAddress"];
            $port = $arr["Port"];
            if($path[0] == "/" || $path[0] == "\\") $path = substr($path, 1);
            $url = 'http://'.$ipAdress.':'.$port.'/api/'.$apikey.'/'.$path;

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
            $ParentId = @IPS_GetInstance($this->InstanceID)['ConnectionID'];
            if($ParentId == 0) return "";
            $arr = json_decode(IPS_GetConfiguration ($ParentId), true);

            $apikey = $arr["APIKey"];
            $ipAdress = $arr["IPAddress"];
            $port = $arr["Port"];
            if($path[0] == "/" || $path[0] == "\\") $path = substr($path, 1);

            $url = 'http://'.$ipAdress.':'.$port.'/api/'.$apikey.'/'.$path;

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

        protected function GetCategoryPath($array, $id){
            if(IPS_GetParent($id) > 0) $array = $this->GetCategoryPath($array, IPS_GetParent($id));
            $array[] = IPS_GetName($id);
            return $array;
        }

        public function GetConfigurationForm() {
            $kat_arr = array();
            $kat_id = $this->ReadPropertyInteger("Kategorie");
            if(!IPS_ObjectExists($kat_id)) $kat_id = 0;
            $kat_arr = $this->GetCategoryPath($kat_arr, $kat_id);

            $lights = array();
            $buttons = array();
            $sensors = array();

            $output = array();
            $elements = array();
            $actions = array();
            $status = array();

            $elements[] = array("type"=> "SelectCategory", "name"=> "Kategorie", "caption"=> "Speicherort");

            $status[] = array("code" => 102, "icon" => "active", "caption" => "Connection Success!");
            $status[] = array("code" => 201, "icon" => "error", "caption" => "Abrufen der Daten fehlgeschalgen! Weitere Infos in den Debug Logs!");
            $status[] = array("code" => 202, "icon" => "error", "caption" => "App verbinden Schaltfläche ist nicht aktive! Gehe in dein Webinterface und aktiviere \"App Verbinden\" unter Gateway => Erweitert");
            $status[] = array("code" => 203, "icon" => "error", "caption" => "Führe bitte zuerst \"Konfiguration holen\"");


            $configurator = array();
            $configurator["type"] = "Configurator";
            $configurator["name"]  = "DeviceList";
            $configurator["caption"]  = "Configuration";
            $configurator["delete"]  = true;
            //$configurator["rowCount"]  = 10;

            $values = array();
            $values[] = array("id" => 1, "name" => "Lampen", "address" => "");
            $values[] = array("id" => 2, "name" => "Schalter", "address" => "");
            $values[] = array("id" => 3, "name" => "Sensoren", "address" => "");


            $data_sensors = IPS_GetInstanceListByModuleID("{AECF8A6E-1E81-E886-8361-4370C5910490}");
            foreach ($data_sensors as $item){
                if(IPS_InstanceExists($item)){
                    $arr = json_decode(IPS_GetConfiguration ($item), true);
                    $sensors[$arr["uniqueid"]] = $item;
                }
            }

            $data_sensors = json_decode($this->GetData("sensors"), true);
            if(is_array($data_sensors)){
                foreach ($data_sensors as $item) {
                    $intID = 0;
                    $name = $item["name"];

                    if(array_key_exists($item["uniqueid"], $sensors)) $intID = $sensors[$item["uniqueid"]];
                    if($intID > 0) $name = IPS_GetName($intID);
                    if(array_key_exists("state", $item)){
                        $name = $name. " (State=";
                        $first = true;
                        foreach($item["state"] as $key_val => $item_val){
                            if($first){
                                $name = $name.$key_val;
                                $first = false;
                            }else{
                                $name = $name.",".$key_val;
                            }
                        }
                        $name = $name.")";
                    }

                    if(strlen($name > 100)) $name = substr($name, 0, 97). "...";
                    $create = array();
                    $create["moduleID"] ="{AECF8A6E-1E81-E886-8361-4370C5910490}";

                    $configuration = array();
                    $configuration["uniqueid"] = $item["uniqueid"];
                    $create["configuration"] = $configuration;

                    if($kat_id > 0){
                        $create["location"] =  $kat_arr;
                    }

                    $values[] = array("parent" => 3, "name" => $name, "address" => $item["uniqueid"], "instanceID" => $intID, "create" => $create);

                }
            }



            $configurator["values"]  = $values;
            $actions[] = $configurator;

            $output["elements"] = $elements;
            $output["actions"] = $actions;
            $output["status"] = $status;


            //$this->SendDebug("debug", json_encode($output),0);
            return json_encode($output);
        }

        public function ReceiveData($JSONString) {
               $data = json_decode($JSONString);
               $this->SendDebug("ReceiveData", utf8_decode($data->Buffer), 0);
        }
    }
?>
