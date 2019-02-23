<?
    // Klassendefinition
    class BlueIrisCam extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }

        public function Create() {
            parent::Create();

            // Modul-Eigenschaftserstellung
            $this->RegisterPropertyString("ShortName", "cam1");
            $this->SetStatus(102);
        }

        public function ReceiveData($JSONString) {

            $data = json_decode($JSONString);
            IPS_LogMessage("ReceiveData", utf8_decode($data->CreateVar));

            if($createVar){
                if(@IPS_GetVariableIDByName("isOnline", $key) === False){
                    $VarID = IPS_CreateVariable(0);
                    IPS_SetName($VarID, "isOnline"); // Variable benennen
                    IPS_SetParent($VarID, $InsID);
                    IPS_SetVariableCustomProfile($VarID, "~Switch");
                }

                if(@IPS_GetVariableIDByName("isRecording", $key) === False){
                    $VarID = IPS_CreateVariable(0);
                    IPS_SetName($VarID, "isRecording"); // Variable benennen
                    IPS_SetParent($VarID, $InsID);
                    IPS_SetVariableCustomProfile($VarID, "~Switch");
                }

                if(@IPS_GetVariableIDByName("Stream", $key) === False){
                    $ImageFile = 'http://'.$IPAddress.":".$Port."/mjpg/". $val["optionValue"]. "/video.mjpg";     // Image-Datei
                    $MediaID = IPS_CreateMedia(3);                  // Image im MedienPool anlegen
                    IPS_SetMediaFile($MediaID, $ImageFile, true);   // Image im MedienPool mit Image-Datei verbinden
                    IPS_SetName($MediaID, "Stream"); // Medienobjekt benennen
                    IPS_SetParent($MediaID, $InsID);
                }

                if(@IPS_GetVariableIDByName("FPS", $key) === False){
                    $VarID = IPS_CreateVariable(2);
                    IPS_SetName($VarID, "FPS"); // Variable benennen
                    IPS_SetParent($VarID, $InsID);
                }
            }

            $VarID = @IPS_GetVariableIDByName("isOnline", $key);
            if($VarID !== False){
                if(!empty($val["isOnline"])) SetValueBoolean($VarID, True); else SetValueBoolean($VarID, False);
            }

            $VarID = @IPS_GetVariableIDByName("isRecording", $key);
            if($VarID !== False){
                if(!empty($val["isRecording"])) SetValueBoolean($VarID, True); else SetValueBoolean($VarID, False);
            }

            $VarID = @IPS_GetVariableIDByName("FPS", $key);
            if($VarID !== False){
                if(!empty($val["FPS"])) SetValue($VarID,$val["FPS"]); else SetValue($VarID, 0);
            }
        }

        public function AlertList(int $startdate = null, bool $reset = null){
            if(is_null($startdate)) $startdate = 0;
            if(is_null($reset)) $reset = false;

            $id = $this->InstanceID;
            $pid = IPS_GetParent($id);
            $camera = $this->ReadPropertyString("ShortName");

            $sid = BlueIris_Login($pid);
            if($sid != "ERROR")
                return BlueIris_AlertList($pid,$sid, $camera,$startdate, $reset);
            else
                return  "ERROR";
        }

        public function CamConfig(bool $reset = null, bool $enable = null, int $pause = null, bool $motion = null, bool $schedule = null, bool $ptzcycle = null, bool $ptzevents = null, int $alerts = null, int $record = null){
            $id = $this->InstanceID;
            $pid = IPS_GetParent($id);
            $camera = $this->ReadPropertyString("ShortName");

            $sid = BlueIris_Login($pid);
            if($sid != "ERROR")
                return BlueIris_CamConfig($pid, $sid, $camera, $reset, $enable, $pause, $motion, $schedule, $ptzcycle, $ptzevents, $alerts, $record);
            else
                return  "ERROR";
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
    }
?>