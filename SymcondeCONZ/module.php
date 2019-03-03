<?

    class deCONZ extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }

        public function Create() {
            parent::Create();

            $this->SetBuffer("Aktive", false);

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

        public function GetConfigurationForm() {

            return '{
                "elements":
                [
                    { "type": "ValidationTextBox", "name": "IPAddress", "caption": "Host" },
                    { "type": "NumberSpinner", "name": "Port", "caption": "Port" },
                    { "type": "NumberSpinner", "name": "WSPort", "caption": "Websocket-Port" },
                    { "type": "IntervalBox", "name": "Interval", "caption": "Sek" },
                    { "type": "ValidationTextBox", "name": "APIKey", "caption": "API Key" }
            
                ],
                "actions":
                [
            
                    { "type": "Button", "label": "Konfiguration holen", "onClick": "deCONZ_GETConfiguration($id);" },
                    { "type": "Button", "label": "Nur API-Key holen", "onClick": "deCONZ_GETAPIKey($id);" },
                    { "type": "Button", "label": "Geräteliste aktualisieren", "onClick": "echo deCONZ_UpdateDevices($id);" },
                    {
                        "type": "Configurator",
                        "name": "Configuration",
                        "caption": "Configuration",
                        "delete": true,
                        "rowCount": 10,
                        "values": [
                            {
                                "id": 1,
                                "name": "Kategorie",
                                "address": ""
                            },{
                                "parent": 1,
                                "name": "Rechenmodul - Minimum",
                                "address": "2",
                                "create": {
                                    "moduleID": "{A7B0B43B-BEB0-4452-B55E-CD8A9A56B052}",
                                    "configuration": {
                                        "Calculation": 2,
                                        "Variables": "[]"
                                    }
                                }
                            },{
                                "parent": 1,
                                "name": "Rechenmodul im Wohnzimmer",
                                "address": "2",
                                "create": {
                                    "moduleID": "{A7B0B43B-BEB0-4452-B55E-CD8A9A56B052}",
                                    "configuration": {
                                        "Calculation": 2,
                                        "Variables": "[]"
                                    },
                                    "location": [
                                        "Erdgeschoss",
                                        "Wohnzimmer"
                                    ]
                                }
                            },{
                                "parent": 1,
                                "instanceID": 53398,
                                "name": "Fehlerhafte Instanz",
                                "address": "4"
                            },{
                                "parent": 1,
                                "name": "Rechenmodul - Auswahl",
                                "address": "2",
                                "create": {
                                    "Maximum": {
                                        "moduleID": "{A7B0B43B-BEB0-4452-B55E-CD8A9A56B052}",
                                        "configuration": {
                                            "Calculation": 3,
                                            "Variables": "[]"
                                        }
                                    },
                                    "Average": {
                                        "moduleID": "{A7B0B43B-BEB0-4452-B55E-CD8A9A56B052}",
                                        "configuration": {
                                            "Calculation": 4,
                                            "Variables": "[]"
                                        }
                                    }
                                }
                            }, {
                                "parent": 1,
                                "name": "OZW772 IP-Interface",
                                "address": "00:A0:03:FD:14:BB",
                                "create": [
                                    {
                                        "moduleID": "{33765ABB-CFA5-40AA-89C0-A7CEA89CFE7A}",
                                        "configuration": {}
                                    },
                                    {
                                        "moduleID": "{1C902193-B044-43B8-9433-419F09C641B8}",
                                        "configuration": {
                                            "GatewayMode":1
                                        }
                                    },
                                    {
                                        "moduleID": "{82347F20-F541-41E1-AC5B-A636FD3AE2D8}",
                                        "configuration": {
                                            "Host":"172.17.31.95",
                                            "Port":3671,
                                            "Open":true
                                        }
                                    }
                                ]
                            }
                        ]
                    }
                ],
                "status":
                [
                    { "code": 102, "icon": "active", "caption": "Connection Success!" },
                    { "code": 201, "icon": "error", "caption": "Abrufen der Daten fehlgeschalgen! Weitere Infos in den Debug Logs!" },
                    { "code": 202, "icon": "error", "caption": "App verbinden Schaltfläche ist nicht aktive! Gehe in dein Webinterface und aktiviere \"App Verbinden\" unter Gateway => Erweitert" },
                    { "code": 203, "icon": "error", "caption": "Führe bitte zuerst \"Konfiguration holen\"" }
                ]
            }';


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
