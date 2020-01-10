<?

// Klassendefinition
require_once('wol.php');

class SamsungTizen extends IPSModule
{
    // Buffer sind immer String. Kovertierungen durch integer, float oder bool können Probleme verursachen.
    // Also wird mit serialize immer alles typensicher in einen String gewandelt.
    public function SetBuffer($Name, $Daten)
    {
        parent::SetBuffer($Name, serialize($Daten));
    }

    public function GetBuffer($Name)
    {
        return unserialize(parent::GetBuffer($Name));
    }

    public function Create()
    {
        parent::Create();
        $this->SendDebug(__FUNCTION__, '', 0);

        // Alt / Neu Vergleich für die ParentId
        $this->SetBuffer("OldParent", 0);

        // Modul-Eigenschaftserstellung
        $this->RegisterPropertyBoolean("Active", false);

        $this->RegisterPropertyString("IPAddress", "192.168.178.1");
        $this->RegisterPropertyString("MACAddress", "aa:bb:cc:00:11:22");
        $this->RegisterPropertyInteger("Interval", 10);
        $this->RegisterPropertyInteger("Sleep", 1000);

        $this->RegisterPropertyInteger("CIDR", 24);
        $this->RegisterPropertyInteger("WoLPort", 9);
        $this->RegisterPropertyString("WoLPath", "");
        $this->RegisterPropertyString("WolParameter", "");

        $this->RegisterPropertyBoolean("UseSSL", true);

        $this->RegisterVariableString('VariableToken', 'Token', "", 0);
        $this->RegisterVariableBoolean("VariableOnline", "Status", "~Switch", 0);
        $this->RegisterVariableString("VariableApps", "Apps", "", 0);

        $this->RequireParent("{3AB77A94-3467-4E66-8A73-840B4AD89582}");

        //event erstellen
        $this->RegisterTimer("CheckOnline", 0, 'SamsungTizen_CheckOnline($_IPS[\'TARGET\']);');
        $this->SetStatus(102);
    }

    public function ApplyChanges()
    {
        $this->RegisterMessage(0, 10001 /* IPS_KERNELSTARTED */);
        // Diese Zeile nicht löschen
        parent::ApplyChanges();
        $this->SendDebug(__FUNCTION__, '', 0);

        $this->SendDebug(__FUNCTION__, '', 0);
        $this->RegisterVariableString('VariableToken', 'Token', "", 0);
        $this->RegisterVariableBoolean("VariableOnline", "Status", "~Switch", 0);
        $this->SetValue("VariableOnline", false);
        $this->RegisterVariableString("VariableApps", "Apps", "", 0);

        //$this->SetTimerInterval("CheckOnline", 0);

        $this->RegisterMessage($this->InstanceID, 11101 /* FM_CONNECT */);
        $this->RegisterMessage($this->InstanceID, 11102 /* FM_DISCONNECT */);
        if (IPS_GetKernelRunlevel() == 10103 /* KR_READY */) {
            $this->RegisterParent();
            $this->UpdateConfigurationForParent();
        }
    }

    /**
     * Ermittelt den Parent und verwaltet die Einträge des Parent im MessageSink
     * Ermöglicht es das Statusänderungen des Parent empfangen werden können.
     *
     * @access protected
     * @return int ID des Parent.
     */
    protected function RegisterParent()
    {
        $this->SendDebug(__FUNCTION__, '', 0);
        $OldParentId = $this->GetBuffer("OldParent");
        $ParentId = @IPS_GetInstance($this->InstanceID)['ConnectionID'];
        if ($ParentId <> $OldParentId) {
            if ($OldParentId > 0) {
                $this->UnregisterMessage($OldParentId, 10505 /* IM_CHANGESTATUS */);
            }
            if ($ParentId > 0) {
                $this->RegisterMessage($ParentId, 10505 /* IM_CHANGESTATUS */);
            } else {
                $ParentId = 0;
            }
            $this->SetBuffer("OldParent", $ParentId);
        }
        return $ParentId;
    }

    public function WakeUp()
    {
        $this->SendDebug(__FUNCTION__, '', 0);
        $ip = $this->ReadPropertyString("IPAddress");
        $cidr = $this->ReadPropertyInteger("CIDR");
        $port = $this->ReadPropertyInteger("WoLPort");
        $w_path = $this->ReadPropertyString("WoLPath");
        $w_parameters = $this->ReadPropertyString("WolParameter");

        if (!empty($w_path)) {
            IPS_Execute($w_path, $w_parameters, false, false);
        } else {
            $macAddressHexadecimal = strtoupper($this->ReadPropertyString("MACAddress"));
            wakeOnLan($macAddressHexadecimal, $ip, $cidr, $port, $output);
            $this->SendDebug("WOL", json_encode($output), 0);
        }
    }

    public function SendKeys(String $keys)
    {
        $this->SendDebug(__FUNCTION__, '', 0);
        $sleep = $this->ReadPropertyInteger("Sleep");
        $sleep = $sleep / 1000;
        if (strpos($keys, ';') !== false) {
            $keys_data = explode(";", $keys);
            foreach ($keys_data as $value) {
                $send_str = '{"method":"ms.remote.control","params":{"Cmd":"Click","DataOfCmd":"' . $value . '","Option":"false","TypeOfRemote":"SendRemoteKey"}}';
                $this->SendDataToParent(json_encode(Array("DataID" => "{BC49DE11-24CA-484D-85AE-9B6F24D89321}", "FrameTyp" => 1, "Fin" => true, "Buffer" => $send_str)));
                sleep($sleep);
            }
        } else {
            $send_str = '{"method":"ms.remote.control","params":{"Cmd":"Click","DataOfCmd":"' . $keys . '","Option":"false","TypeOfRemote":"SendRemoteKey"}}';
            $this->SendDataToParent(json_encode(Array("DataID" => "{BC49DE11-24CA-484D-85AE-9B6F24D89321}", "FrameTyp" => 1, "Fin" => true, "Buffer" => $send_str)));
        }
    }

    public function UpdateApps()
    {
        $this->SendDebug(__FUNCTION__, '', 0);
        $send_str = '{"method":"ms.channel.emit","params":{"event": "ed.installedApp.get", "to":"host"}}';
        $this->SendDataToParent(json_encode(Array("DataID" => "{BC49DE11-24CA-484D-85AE-9B6F24D89321}", "FrameTyp" => 1, "Fin" => true, "Buffer" => $send_str)));
    }

    public function StartApp($appName)
    {
        $this->SendDebug(__FUNCTION__, '', 0);
        $data = $this->GetValue("VariableApps");
        $this->SendDebug("msg", "1", 0);
        if (empty($data))
            return false;
        $data = json_decode($data, true);
        //[{"appId":"111299001912","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/111299001912\/250x250.png","name":"YouTube"},{"appId":"3201606009684","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201606009684\/250x250.png","name":"Spotify"},{"appId":"11101200001","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/11101200001\/250x250.png","name":"Netflix"},{"appId":"3201506003123","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201506003123\/250x250.png","name":"maxdome"},{"appId":"111399001366","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/111399001366\/250x250.png","name":"AccuWeather - Weather for Life"},{"appId":"3201601007250","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201601007250\/250x250.png","name":"Google Play Filme"},{"appId":"20162100006","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/20162100006\/250x250.png","name":"e-Manual"},{"appId":"3201505002690","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201505002690\/250x250.png","name":"CHILI"},{"appId":"3201608010221","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201608010221\/250x250.png","name":"ProSieben"},{"appId":"3201705012365","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201705012365\/250x250.png","name":"ZDF mediathek"},{"appId":"org.tizen.browser","app_type":4,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/webbrowser\/250x250.png","name":"Web Browser"},{"appId":"3201512006785","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201512006785\/250x250.png","name":"Amazon Video"},{"appId":"11101314801","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/11101314801\/250x250.png","name":"AMPYA"},{"appId":"3201601007386","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201601007386\/250x250.png","name":"Videoload"},{"appId":"3201511006428","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201511006428\/250x250.png","name":"Rakuten TV"},{"appId":"3201602007987","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201602007987\/250x250.png","name":"TV DIGITAL"},{"appId":"3201412000679","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201412000679\/250x250.png","name":"ARD"},{"appId":"3201502001386","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201502001386\/250x250.png","name":"7TV"},{"appId":"111477001366","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/111477001366\/250x250.png","name":"Disney Channel"},{"appId":"3201411000562","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201411000562\/250x250.png","name":"Sky Ticket"},{"appId":"3201411000446","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201411000446\/250x250.png","name":"JUKE!"},{"appId":"3201508004843","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201508004843\/250x250.png","name":"n-tv"},{"appId":"3201608010222","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201608010222\/250x250.png","name":"SAT.1"},{"appId":"3201608010225","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201608010225\/250x250.png","name":"ProSieben MAXX"},{"appId":"3201607009920","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201607009920\/250x250.png","name":"DAZN"},{"appId":"3201608010269","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201608010269\/250x250.png","name":"Brandworld"},{"appId":"111199000390","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/111199000390\/250x250.png","name":"BILD"},{"appId":"3201608010224","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201608010224\/250x250.png","name":"sixx"},{"appId":"3201608010226","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201608010226\/250x250.png","name":"SAT.1 Gold"},{"appId":"3201608010223","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201608010223\/250x250.png","name":"kabel eins"},{"appId":"3201609010551","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201609010551\/250x250.png","name":"kabel eins Doku"},{"appId":"111299001432","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/111299001432\/250x250.png","name":"Zattoo"},{"appId":"3201509005086","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201509005086\/250x250.png","name":"TIERWELT live"},{"appId":"111477001150","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/111477001150\/250x250.png","name":"WELT"},{"appId":"111299001783","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/111299001783\/250x250.png","name":"Mercedes-Benz TV"},{"appId":"11111358501","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/11111358501\/250x250.png","name":"Audi"},{"appId":"111199000385","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/111199000385\/250x250.png","name":"Digital Concert Hall"},{"appId":"3201611010976","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201611010976\/250x250.png","name":"DJI"},{"appId":"3201604008870","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201604008870\/250x250.png","name":"WRC \u00e2\u0080\u0093 The Official App"},{"appId":"3201704012215","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201704012215\/250x250.png","name":"VR-SmartTV"},{"appId":"3201706014233","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201706014233\/250x250.png","name":"Peugeot"},{"appId":"111477001125","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/111477001125\/250x250.png","name":"ANTENNE BAYERN"},{"appId":"3201706014301","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201706014301\/250x250.png","name":"Goalplay"},{"appId":"3201707014358","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201707014358\/250x250.png","name":"MagentaSport"},{"appId":"11091000000","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/11091000000\/250x250.png","name":"Facebook Watch"},{"appId":"3201703012079","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201703012079\/250x250.png","name":"Eurosport Player"},{"appId":"3201801015650","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201801015650\/250x250.png","name":"Diveo"},{"appId":"3201903018105","app_type":2,"icon":"\/opt\/down\/webappservice\/apps_icon\/FirstScreen\/3201903018105\/250x250.png","name":"Samsung Sportworld"}]

        $key = array_search($appName, array_column($data, 'name'));

        $this->SendDebug("msg", "2", 0);
        $appID = $data[$key]["appId"];
        $appType = $data[$key]["app_type"];
        $actionType = "";

        if ($appType == 2)
            $actionType = "DEEP_LINK";
        if ($appType == 4)
            $actionType = "NATIVE_LAUNCH";
        if (empty($actionType))
            return false;

        $send_str = '{"method":"ms.channel.emit","params":{"event": "ed.apps.launch", "to":"host", "data":{"appId": "' . $appID . '", "action_type": "' . $actionType . '"}}}';
        $this->SendDebug("msg", $send_str, 0);
        $this->SendDataToParent(json_encode(Array("DataID" => "{BC49DE11-24CA-484D-85AE-9B6F24D89321}", "FrameTyp" => 1, "Fin" => true, "Buffer" => $send_str)));
        return true;
    }

    public function StartWebpage($url)
    {
        $this->SendDebug(__FUNCTION__, '', 0);
        $send_str = '{"method":"ms.channel.emit","params":{"event": "ed.apps.launch", "to":"host", "data":{"appId":"org.tizen.browser","action_type":"NATIVE_LAUNCH","metaTag":"' . $url . '"}}}';
        $this->SendDataToParent(json_encode(Array("DataID" => "{BC49DE11-24CA-484D-85AE-9B6F24D89321}", "FrameTyp" => 1, "Fin" => true, "Buffer" => $send_str)));
    }

    public function CheckOnline()
    {
        $this->SendDebug(__FUNCTION__, '', 0);

        $ipAdress = $this->ReadPropertyString("IPAddress");
        if (Sys_Ping($ipAdress, 5000)) {
//            if ($this->GetValue("VariableOnline") == false) {
            //$this->SetValue("VariableOnline", true);
            $this->SetTimerInterval("CheckOnline", 0);
            $this->UpdateConfigurationForParent();
            //          }
        }/* else {
          if ($this->GetValue("VariableOnline")) {
          $this->SetValue("VariableOnline", false);
          $this->SetStatus(104);
          }
          } */
    }

    public function TogglePower()
    {
        $this->SendDebug(__FUNCTION__, '', 0);
        if ($this->GetValue("VariableOnline") == true) {
            $this->SendKeys('KEY_POWER');
        } else {
            $this->WakeUp();
        }
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        $this->SendDebug(__FUNCTION__, '', 0);
        $this->SendDebug($Message . '.' . $SenderID, $Data, 0);
        switch ($Message) {
            case 10001: /* IPS_KERNELSTARTED */
                $this->RegisterParent();
                $this->UpdateConfigurationForParent();
                break;
            // Falls der User die übergeordnete Instanz ändert
            case 11101: /* FM_CONNECT */
                // Neuer IO mit dieser Instanz verbunden.
                $this->SendDebug("Connection", "Samsung Tizen has new IO", 0);
                // Parent merken für IM_CHANGESTATUS
                $this->RegisterParent();
                // Config reinschreiben
                $this->UpdateConfigurationForParent();
                break;
            // Falls der User die übergeordnete Instanz löscht
            case 11102: /* FM_DISCONNECT */
                // Parent löschen für IM_CHANGESTATUS
                $this->RegisterParent();
                $this->SendDebug("Connection", "Samsung Tizen has no IO", 0);
                $this->SetValue("VariableOnline", false);
                $this->SetStatus(104);
                $this->SetTimerInterval("CheckOnline", 0);
                break;
            case 10505: /* IM_CHANGESTATUS */
                switch ($Data[0]) {
                    case 102: // WebSocket ist aktiv
                        $this->SendDebug("Connection", "IO connection establish", 0);
                        $this->SetStatus(102);
                        $this->SetTimerInterval("CheckOnline", 0);
                        //$this->SetValue("VariableOnline", true);
                        break;
                    case 104: // WebSocket ist inaktiv
                        $this->SendDebug("Connection", "IO connection closed", 0);
                        $this->SetValue("VariableOnline", false);
                        $this->SetStatus(104);
                        if ($this->ReadPropertyBoolean("Active")) {
                            $this->SetTimerInterval("CheckOnline", $this->ReadPropertyInteger("Interval") * 1000);
                        } else {
                            $this->SetTimerInterval("CheckOnline", 0);
                        }
                        break;
                    default: // Fehlerzustände
                        /* $this->SendDebug("Connection", "Samsung Tizen connection lost", 0);
                          $this->SetValue("VariableOnline", false);
                          $this->SetStatus(104);
                          if ($this->ReadPropertyBoolean("Active")) {
                          $this->SetTimerInterval("CheckOnline", $this->ReadPropertyInteger("Interval") * 1000);
                          } else {
                          $this->SetTimerInterval("CheckOnline", 0);
                          } */
                        $this->SendDebug("Force close Websocket", $SenderID, 0);
                        // losgelöst vom aktuellen context, damit wir keinen DeathLock haben.
                        // Hierdurch wird wieder MessageSink mit case 104 (inaktiv) getriggert.
                        $Script = 'IPS_SetProperty(' . $SenderID . ', "Open", false);' . PHP_EOL;
                        $Script .= 'IPS_ApplyChanges(' . $SenderID . ');';
                        IPS_RunScriptText($Script);
                        break;
                }
                break;
        }
    }

    public function ReceiveData($JSONString)
    {
        $this->SendDebug(__FUNCTION__, '', 0);
        $data = json_decode($JSONString);
        $this->SendDebug("ReceiveData", $data, 0);
        $r_data = json_decode($data->Buffer, true);

        /*
         *  ms.channel.connect bei NEUEM Client:
         * {
          "data": {
          "clients": [
          {
          "attributes": {
          "name": "SVBTeW1jb25UaXplbg==",
          "token": ""
          },
          "connectTime": 1561215420786,
          "deviceName": "SVBTeW1jb25UaXplbg==",
          "id": "abcf7897-a353-4fa5-b3cd-95f3e9b81c",
          "isHost": false
          }
          ],
          "id": "abcf7897-a353-4fa5-b3cd-95f3e9b81c",
          "token": "12734469"
          },
          "event": "ms.channel.connect"
          }
         * ms.channel.connect bei Client mit gültigen token beim Verbindungsaufbau:
         *  {
          "data": {
          "clients": [
          {
          "attributes": {
          "name": "SVBTeW1jb25UaXplbg==",
          "token": "76921230"
          },
          "connectTime": 1561216559735,
          "deviceName": "SVBTeW1jb25UaXplbg==",
          "id": "431f7a2d-c9d3-477e-97fd-59a0b0696b0",
          "isHost": false
          }
          ],
          "id": "431f7a2d-c9d3-477e-97fd-59a0b0696b0"
          },
          "event": "ms.channel.connect"
          }
         */
        if (array_key_exists("event", $r_data)) {
            $event = $r_data["event"];
            switch ($event) {
                case "ms.channel.connect":
                    if ($this->ReadPropertyBoolean("UseSSL") == true) {
                        $token = '';
                        // Unser Client in der Antwort?
                        foreach ($r_data['data']['clients'] as $Client) {
                            if (base64_decode($Client['attributes']['name']) == 'IPSymconTizen') {
                                // Client found
                                // token nutzen, er kann auch leer sein!
                                $token = $Client['attributes']['token'];
                            }
                        }
                        // token leer
                        if ($token == '') {
                            if (array_key_exists('token', $r_data["data"])) {
                                $token = $r_data["data"]["token"];
                            }
                        }
                        // token noch immer leer?
                        if ($token == '') {
                            $this->SendDebug("Token", "Server send no token", 0);
                        } else {
                            $this->SendDebug("Token", "Token des Servers:" . $token, 0);
                            // Token neu?
                            if ($token != $this->GetValue("VariableToken")) {
                                $this->SetValue("VariableToken", $token);
                                $this->SendDebug("Token", "New Token " . $token . " has been set", 0);

                                //$this->SetValue("VariableOnline", false);
                                /*
                                  if ($this->ReadPropertyBoolean("Active")) {
                                  $this->SetTimerInterval("CheckOnline", $this->ReadPropertyInteger("Interval") * 1000);
                                  } else {
                                  $this->SetTimerInterval("CheckOnline", 0);
                                  } */

                                $this->UpdateConfigurationForParent();
                                return;
                            }
                        }
                    }
                    $this->SendDebug("Connection", "Samsung Tizen connection establish (ms.channel.connect)", 0);
                    $this->SetValue("VariableOnline", true);
                    break;
                case "ed.installedApp.get":
                    $this->SetValue("VariableApps", json_encode($r_data['data']['data']));
                    break;
                default:

                    break;
            }
        }
    }

    public function GetConfigurationForParent()
    {
        $this->SendDebug(__FUNCTION__, '', 0);
        $ipAdress = $this->ReadPropertyString("IPAddress");
        $useSSL = $this->ReadPropertyBoolean("UseSSL");
        $active = $this->ReadPropertyBoolean("Active");
        $Query = array('name' => base64_encode('IPSymconTizen'));

        if ($useSSL) {
            $origin = "https://" . $ipAdress . ":8002";
            $Query['token'] = $this->GetValue("VariableToken");

            $address = "wss://" . $ipAdress . ":8002/api/v2/channels/samsung.remote.control?" . http_build_query($Query);
        } else {
            $origin = "http://" . $ipAdress . ":8001";
            $address = "ws://" . $ipAdress . ":8001/api/v2/channels/samsung.remote.control?" . http_build_query($Query);
        }

        $Config = array(
            "Open"         => $active,
            "URL"          => $address,
            "Protocol"     => "",
            "Version"      => 13,
            "Origin"       => $origin,
            "PingInterval" => 10,
            "PingPayload"  => "",
            "Frame"        => 1,
            "BasisAuth"    => false,
            "Username"     => "",
            "Password"     => ""
        );

        return json_encode($Config);
    }

    Private function UpdateConfigurationForParent()
    {
        $this->SendDebug(__FUNCTION__, '', 0);
        $ParentId = @IPS_GetInstance($this->InstanceID)['ConnectionID'];
        $this->SendDebug("Force Update Websocket", $ParentId, 0);
        // losgelöst vom aktuellen context, damit wir keinen DeathLock haben.
        // UpdateConfigurationForParent wird auch durch ReceiveData ausgelöst, und das durch den WebSocketClient => DeathLock
        $Script = 'IPS_SetConfiguration(' . $ParentId . ', \'' . $this->GetConfigurationForParent() . '\');' . PHP_EOL;
        $Script .= 'IPS_ApplyChanges(' . $ParentId . ');';
        IPS_RunScriptText($Script);
        // Dadurch wird durch den IO die MessageSink mit IM_CHANGESTATUS getriggert.
    }

    /**
     * Ergänzt SendDebug um Möglichkeit Objekte und Array auszugeben.
     *
     * @param string                                           $Message Nachricht für Data.
     * @param mixed $Data    Daten für die Ausgabe.
     *
     * @return int $Format Ausgabeformat für Strings.
     */
    protected function SendDebug($Message, $Data, $Format)
    {
        if (is_array($Data)) {
            if (count($Data) > 25) {
                $this->SendDebug($Message, array_slice($Data, 0, 20), 0);
                $this->SendDebug($Message . ':CUT', '-------------CUT-----------------', 0);
                $this->SendDebug($Message, array_slice($Data, -5, null, true), 0);
            } else {
                foreach ($Data as $Key => $DebugData) {
                    $this->SendDebug($Message . ':' . $Key, $DebugData, 0);
                }
            }
        } elseif (is_object($Data)) {
            foreach ($Data as $Key => $DebugData) {
                $this->SendDebug($Message . '->' . $Key, $DebugData, 0);
            }
        } elseif (is_bool($Data)) {
            parent::SendDebug($Message, ($Data ? 'TRUE' : 'FALSE'), 0);
        } else {
            if (IPS_GetKernelRunlevel() == KR_READY) {
                parent::SendDebug($Message, (string) $Data, $Format);
            } else {
                $this->LogMessage($Message . ':' . (string) $Data, KL_DEBUG);
            }
        }
    }

}
