<?php
    // Klassendefinition
    class SymconBlueIrisCam extends IPSModule {
        public function __construct(int $InstanceID) {
            parent::__construct($InstanceID);
        }

        public function Create() {
            parent::Create();

            $this->ConnectParent("{E138AFDC-D1E0-B462-A5E5-AF24F57D4686}");

            // Modul-Eigenschaftserstellung
            $this->RegisterPropertyString("ShortName", "");
            $this->RegisterPropertyBoolean("PTZ", false);
            $this->RegisterPropertyBoolean("showFPS", false);
        }

        public function ApplyChanges()
        {
            //Never delete this line!
            parent::ApplyChanges();
            $this->SetReceiveDataFilter('.*sname\\\":[ \\\"]*(' . $this->ReadPropertyString("ShortName") . '|0)[\\\”]*.*');

            //create variables
            $this->RegisterVariableBoolean("isAlerting",  $this->Translate("Alert"), "~Switch", 1);
            $this->RegisterVariableBoolean("isMotion",  $this->Translate("Motion"), "~Switch", 2);
            $this->RegisterVariableBoolean("isTriggered",  $this->Translate("Triggered"), "~Switch", 3);
            $this->RegisterVariableBoolean("isRecording",  $this->Translate("Recording"), "~Switch", 4);

            $this->RegisterVariableBoolean("isOnline",  $this->Translate("Online"), "~Switch", 90);
            $this->RegisterVariableBoolean("isEnabled",  $this->Translate("Enabled"), "~Switch", 90);
            $this->RegisterVariableBoolean("isPaused",  $this->Translate("Pause"), "~Switch", 91);
            $this->RegisterVariableBoolean("isNoSignal",  $this->Translate("NoSignal"), "~Switch", 92);

            $this->EnableAction("isTriggered");
            $this->EnableAction("isEnabled");
            $this->EnableAction("isPaused");

            if($this->ReadPropertyBoolean("PTZ")){
                if (!IPS_VariableProfileExists("BlueIris.Preset")){
                    IPS_CreateVariableProfile("BlueIris.Preset", 1);
                    IPS_SetVariableProfileAssociation("BlueIris.Preset", 101, $this->Translate("PTZ-Preset")." 1", "", 0x00ff00);
                    IPS_SetVariableProfileAssociation("BlueIris.Preset", 102, $this->Translate("PTZ-Preset")." 2", "", 0x00ff00);
                    IPS_SetVariableProfileAssociation("BlueIris.Preset", 103, $this->Translate("PTZ-Preset")." 3", "", 0x00ff00);
                    IPS_SetVariableProfileAssociation("BlueIris.Preset", 104, $this->Translate("PTZ-Preset")." 4", "", 0x00ff00);
                    IPS_SetVariableProfileAssociation("BlueIris.Preset", 105, $this->Translate("PTZ-Preset")." 5", "", 0x00ff00);
                    IPS_SetVariableProfileAssociation("BlueIris.Preset", 106, $this->Translate("PTZ-Preset")." 6", "", 0x00ff00);
                    IPS_SetVariableProfileAssociation("BlueIris.Preset", 107, $this->Translate("PTZ-Preset")." 7", "", 0x00ff00);
                    IPS_SetVariableProfileAssociation("BlueIris.Preset", 108, $this->Translate("PTZ-Preset")." 8", "", 0x00ff00);
                    IPS_SetVariableProfileAssociation("BlueIris.Preset", 109, $this->Translate("PTZ-Preset")." 9", "", 0x00ff00);
                    IPS_SetVariableProfileAssociation("BlueIris.Preset", 110, $this->Translate("PTZ-Preset")." 10", "", 0x00ff00);
                    IPS_SetVariableProfileAssociation("BlueIris.Preset", 111, $this->Translate("PTZ-Preset")." 11", "", 0x00ff00);
                    IPS_SetVariableProfileAssociation("BlueIris.Preset", 112, $this->Translate("PTZ-Preset")." 12", "", 0x00ff00);
                    IPS_SetVariableProfileAssociation("BlueIris.Preset", 113, $this->Translate("PTZ-Preset")." 13", "", 0x00ff00);
                    IPS_SetVariableProfileAssociation("BlueIris.Preset", 114, $this->Translate("PTZ-Preset")." 14", "", 0x00ff00);
                    IPS_SetVariableProfileAssociation("BlueIris.Preset", 115, $this->Translate("PTZ-Preset")." 15", "", 0x00ff00);
                    IPS_SetVariableProfileAssociation("BlueIris.Preset", 116, $this->Translate("PTZ-Preset")." 16", "", 0x00ff00);
                    IPS_SetVariableProfileAssociation("BlueIris.Preset", 117, $this->Translate("PTZ-Preset")." 17", "", 0x00ff00);
                    IPS_SetVariableProfileAssociation("BlueIris.Preset", 118, $this->Translate("PTZ-Preset")." 18", "", 0x00ff00);
                    IPS_SetVariableProfileAssociation("BlueIris.Preset", 119, $this->Translate("PTZ-Preset")." 19", "", 0x00ff00);
                    IPS_SetVariableProfileAssociation("BlueIris.Preset", 120, $this->Translate("PTZ-Preset")." 20", "", 0x00ff00);
                }
                if (!IPS_VariableProfileExists("BlueIris.Move")){
                    IPS_CreateVariableProfile("BlueIris.Move", 1);
                    IPS_SetVariableProfileValues ("BlueIris.Move", -1, 1, 1);
                }

                $this->RegisterVariableInteger("PTZPreset",  $this->Translate("PTZ-Preset"), "BlueIris.Preset", 10);
                $this->RegisterVariableInteger("PTZMoveLeftRight",  $this->Translate("PTZ-Move Left/Right"), "BlueIris.Move", 11);
                $this->RegisterVariableInteger("PTZMoveUPDown",  $this->Translate("PTZ-Move UP/Down"), "BlueIris.Move", 12);
                $this->RegisterVariableInteger("PTZMoveZoom",  $this->Translate("PTZ-Move Zoom"), "BlueIris.Move", 13);

                $this->EnableAction("PTZPreset");
                $this->EnableAction("PTZMoveLeftRight");
                $this->EnableAction("PTZMoveUPDown");
                $this->EnableAction("PTZMoveZoom");

                if($this->GetValue("PTZPreset") < 101 || $this->GetValue("PTZPreset") > 120) $this->SetValue("PTZPreset", 101);
            }

            if($this->ReadPropertyBoolean("showFPS")){
                $this->RegisterVariableInteger("FPS",  $this->Translate("FPS"), "", 99);
            }
        }

        public function RequestAction($Ident, $Value) {
            switch($Ident) {
                case "PTZPreset":
                    if($Value >= 101 && $Value <= 120){
                        $this->PTZ($Value,0);
                    }
                    $this->SetValue($Ident, $Value);
                    break;
                case "PTZMoveLeftRight":
                    if($Value < 0){
                        $this->PTZ(0,0);
                    }elseif($Value > 0){
                        $this->PTZ(1,0);
                    }
                    $this->SetValue($Ident, 0);
                    break;
                case "PTZMoveUPDown":
                    if($Value > 0){
                        $this->PTZ(2,0);
                    }elseif($Value < 0){
                        $this->PTZ(3,0);
                    }
                    $this->SetValue($Ident, 0);
                    break;
                case "PTZMoveZoom":
                    if($Value > 0){
                        $this->PTZ(5,0);
                    }elseif($Value < 0){
                        $this->PTZ(6,0);
                    }
                    $this->SetValue($Ident, 0);
                    break;
                case "isEnabled":
                case "isPaused":
                    $this->SetValue($Ident, $Value);
                    $this->UpdateCamConfig();
                    break;
                case "isTriggered":
                    $this->SetValue($Ident, $Value);
                    if($Value){
                        $this->SendDebug(__FUNCTION__, $this->Trigger(), 0);
                    }
                    break;
                default:
                    throw new Exception("Invalid Ident");
            }
        }

        public function ReceiveData($JSONString)
        {
            $rData = json_decode($JSONString, true);
            $buffer = json_decode($rData["Buffer"], true);

            switch($buffer['cmd']) {
                case "CamList":
                    return $this->UpdateConfig($buffer['payload']);
                default:
                    $this->SendDebug(__FUNCTION__, "ACTION " . $buffer['cmd'] . " FOR THIS MODULE NOT DEFINED!", 0);
                    break;
            }
        }

        private function UpdateConfig($config){
            //$this->SendDebug(__FUNCTION__, json_encode($config), 0);
            $this->SetValue("isEnabled", $config["isEnabled"]);
            $this->SetValue("isAlerting", $config["isAlerting"]);
            $this->SetValue("isMotion", $config["isMotion"]);
            $this->SetValue("isTriggered", $config["isTriggered"]);
            $this->SetValue("isRecording", $config["isRecording"]);
            $this->SetValue("isOnline", $config["isOnline"]);
            $this->SetValue("isPaused", $config["isPaused"]);
            $this->SetValue("isNoSignal", $config["isNoSignal"]);

            if($this->ReadPropertyBoolean("showFPS")){
                $this->SetValue("FPS", $config["FPS"]);
            }
        }

        private function UpdateCamConfig(){
            $enable = $this->GetValue("isEnabled");
            if($this->GetValue("isPaused")){
                $pause = -1; //-1 pause| 0 stop pause
            }else{
                $pause = 0; //-1 pause| 0 stop pause
            }

            $result = $this->CamConfig(null, $enable, $pause);
            $this->SendDebug(__FUNCTION__, $result, 0);

            return $result;
        }

        public function CamConfig(bool $reset = null, bool $enable = null, int $pause = null, bool $motion = null, bool $schedule = null, bool $ptzcycle = null, bool $ptzevents = null, int $alerts = null, int $record = null){
            $camera = $this->ReadPropertyString("ShortName");

            $data = array();
            $data["camera"] = $camera;
            if(!is_null($reset)) $data["reset"] = $reset;
            if(!is_null($enable)) $data["enable"] = $enable;
            if(!is_null($pause)) $data["pause"] = $pause;
            if(!is_null($motion)) $data["motion"] = $motion;
            if(!is_null($schedule)) $data["schedule"] = $schedule;
            if(!is_null($ptzcycle)) $data["ptzcycle"] = $ptzcycle;
            if(!is_null($ptzevents)) $data["ptzevents"] = $ptzevents;
            if(!is_null($alerts)) $data["alerts"] = $alerts;
            if(!is_null($record)) $data["record"] = $record;

            $sendData = array("cmd" => "CamConfig", "cam" => $camera, "data" => $data);
            return $this->SendDataToParent(json_encode([
                'DataID' => "{95E9AD15-F5B0-01C6-4FF2-9093B9BD4E36}",
                'Buffer' => utf8_encode(json_encode($sendData))
            ]));
        }

        public function AlertList(int $startdate = null, bool $reset = null){
            $camera = $this->ReadPropertyString("ShortName");

            $data = array();
            $data["camera"] = $camera;
            if(is_null($camera)) $camera = "index";
            if(is_null($startdate)) $startdate = 0;
            if(is_null($reset)) $reset = false;

            $sendData = array("cmd" => "AlertList", "data" => $data);
            return $this->SendDataToParent(json_encode([
                'DataID' => "{95E9AD15-F5B0-01C6-4FF2-9093B9BD4E36}",
                'Buffer' => utf8_encode(json_encode($sendData))
            ]));
        }
        public function ClipList(int $startdate = null, int $enddate = null, bool $tiles = null){
            $camera = $this->ReadPropertyString("ShortName");

            $data = array();
            $data["camera"] = $camera;
            if(is_null($startdate)) $startdate = 0;
            if(is_null($enddate)) $enddate = time();
            if(is_null($tiles)) $tiles = false;

            $sendData = array("cmd" => "ClipList","data" => $data);
            return $this->SendDataToParent(json_encode([
                'DataID' => "{95E9AD15-F5B0-01C6-4FF2-9093B9BD4E36}",
                'Buffer' => utf8_encode(json_encode($sendData))
            ]));
        }
        public function PTZ(int $button = 4, int $updown = null){
            $camera = $this->ReadPropertyString("ShortName");

            $data = array();
            $data["camera"] = $camera;
            $data["button"] = $button;
            if(is_null($updown)) $updown = 0;

            $sendData = array("cmd" => "PTZ", "data" => $data);
            return $this->SendDataToParent(json_encode([
                'DataID' => "{95E9AD15-F5B0-01C6-4FF2-9093B9BD4E36}",
                'Buffer' => utf8_encode(json_encode($sendData))
            ]));
        }
        public function Trigger(){
            $camera = $this->ReadPropertyString("ShortName");

            $data = array();
            $data["camera"] = $camera;

            $sendData = array("cmd" => "Trigger", "data" => $data);
            return $this->SendDataToParent(json_encode([
                'DataID' => "{95E9AD15-F5B0-01C6-4FF2-9093B9BD4E36}",
                'Buffer' => utf8_encode(json_encode($sendData))
            ]));
        }

        public function CreateMediaFile(){
            $sendData = array("cmd" => "GetLink");
            $rData = $this->SendDataToParent(json_encode([
                'DataID' => "{95E9AD15-F5B0-01C6-4FF2-9093B9BD4E36}",
                'Buffer' => utf8_encode(json_encode($sendData))
            ]));
            if(empty($rData)) return;

            $rData = json_decode($rData, true);

            $MediaID = @IPS_GetMediaIDByName("Stream", $this->InstanceID);
            if($MediaID === False){
                if(!empty($rData["user"]) && !empty($rData["pw"]))
                    $ImageFile = $rData["link"]."/mjpg/". $this->ReadPropertyString("ShortName"). "/video.mjpg?user=".$rData["user"]."&pw=".$rData["pw"]; // Image-Datei
                else
                    $ImageFile = $rData["link"]."/mjpg/". $this->ReadPropertyString("ShortName"). "/video.mjpg";

                $MediaID = IPS_CreateMedia(3);                  // Image im MedienPool anlegen
                IPS_SetMediaFile($MediaID, $ImageFile, true);   // Image im MedienPool mit Image-Datei verbinden
                IPS_SetName($MediaID, "Stream"); // Medienobjekt benennen
                IPS_SetParent($MediaID, $this->InstanceID);
            }
        }
    }
