<?php
    // Klassendefinition
    class BlueIrisCam extends IPSModule {
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
            $this->RegisterVariableBoolean("isTriggered",  $this->Translate("Alert"), "~Switch", 3);
            $this->RegisterVariableBoolean("isRecording",  $this->Translate("Recording"), "~Switch", 4);

            $this->RegisterVariableBoolean("isOnline",  $this->Translate("Online"), "~Switch", 90);
            $this->RegisterVariableBoolean("isPaused",  $this->Translate("Pause"), "~Switch", 91);
            $this->RegisterVariableBoolean("isNoSignal",  $this->Translate("NoSignal"), "~Switch", 92);

            $this->RegisterVariableInteger("lastalert",  $this->Translate("Last Alert"), "~UnixTimestamp", 99);

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

            $this->SetValue("isAlerting", $config["isAlerting"]);
            $this->SetValue("isMotion", $config["isMotion"]);
            $this->SetValue("isTriggered", $config["isTriggered"]);
            $this->SetValue("isRecording", $config["isRecording"]);
            $this->SetValue("isOnline", $config["isOnline"]);
            $this->SetValue("isPaused", $config["isPaused"]);
            $this->SetValue("isNoSignal", $config["isNoSignal"]);

            $this->SetValue("lastalert", $config["lastalert"]);
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

            $sendData = array("cmd" => "AlertList", "cam" => $camera, "data" => $data);
            return $this->SendDataToParent(json_encode([
                'DataID' => "{95E9AD15-F5B0-01C6-4FF2-9093B9BD4E36}",
                'Buffer' => utf8_encode(json_encode($sendData))
            ]));
        }

        public function ClipList(int $startdate = null, int $enddate = null, bool $tiles = null){
            if(is_null($startdate)) $startdate = 0;
            if(is_null($enddate)) $enddate = time();
            if(is_null($tiles)) $tiles = false;

            $id = $this->InstanceID;
            $pid = IPS_GetParent($id);
            $camera = $this->ReadPropertyString("ShortName");

            $sid = BlueIris_Login($pid);
            if($sid != "ERROR")
                return BlueIris_ClipList($pid, $sid, $camera, $startdate, $enddate, $tiles);
            else
                return  "ERROR";
        }

        public function PTZ(int $button = null, int $updown = null){
            if(is_null($button)){
                $this->SetStatus(203);
                return "ERROR";
            } 
            if(is_null($updown)) $updown = 0;

            $id = $this->InstanceID;
            $pid = IPS_GetParent($id);
            $camera = $this->ReadPropertyString("ShortName");

            $sid = BlueIris_Login($pid);
            if($sid != "ERROR")
                return BlueIris_PTZ($pid, $sid, $camera, $button, $updown);
            else
                return "ERROR";
        }

        public function Trigger(){

            $id = $this->InstanceID;
            $pid = IPS_GetParent($id);
            $camera = $this->ReadPropertyString("ShortName");

            $sid = BlueIris_Login($pid);
            if($sid != "ERROR")
                return BlueIris_Trigger($pid, $sid, $camera);
            else
                return  "ERROR";
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
