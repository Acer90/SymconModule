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

        }

        public function AlertList(integer $startdate = null, bool $reset = null){
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

        public function CamConfig(bool $reset = null, bool $enable = null, integer $pause = null, bool $motion = null, bool $schedule = null, bool $ptzcycle = null, bool $ptzevents = null, integer $alerts = null, integer $record = null){
            $id = $this->InstanceID;
            $pid = IPS_GetParent($id);
            $camera = $this->ReadPropertyString("ShortName");

            $sid = BlueIris_Login($pid);
            if($sid != "ERROR")
                return BlueIris_CamConfig($pid, $sid, $camera, $reset, $enable, $pause, $motion, $schedule, $ptzcycle, $ptzevents, $alerts, $record);
            else
                return  "ERROR";
        }

        public function ClipList(integer $startdate = null, integer $enddate = null, bool $tiles = null){
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

        public function PTZ(integer $button = null, integer $updown = null){
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