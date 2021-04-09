<?
 // Klassendefinition
    class OBS extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }

        public function Create() {
            parent::Create();

            // Modul-Eigenschaftserstellung
            $this->RegisterPropertyString("IPAddress", "192.168.178.1");
            $this->RegisterPropertyInteger("Port", 4444);
            $this->RegisterPropertyInteger("Interval", 10);
            $this->RegisterPropertyInteger("Timeout", 5);

            $this->RegisterVariableString("CurrentScene", "Aktuelle Scene", "");
            $this->EnableAction("CurrentScene");
            $this->RegisterVariableString("CurrentTransition", "Überblendung", "");
            $this->EnableAction("CurrentTransition");
            $this->RegisterVariableBoolean("Streaming", "Streaming", "~Switch");
            $this->EnableAction("Streaming");

            if (!IPS_VariableProfileExists("FPS")){
                IPS_CreateVariableProfile("FPS", 2);
                IPS_SetVariableProfileDigits("FPS",1);
            }
            $this->RegisterVariableFloat("FPS", "FPS", "FPS");

            if (!IPS_VariableProfileExists("KByte_sec")){
                IPS_CreateVariableProfile("KByte_sec", 2);
                IPS_SetVariableProfileDigits("KByte_sec",1);
                IPS_SetVariableProfileText("KByte_sec", "", " KByte/Sec");
            }
            $this->RegisterVariableFloat("Uploadspeed", "Upload Speed", "KByte_sec");
            $this->RegisterVariableString("TotalStreamTime", "Total Stream Time", "");

            //event erstellen 
            $this->RegisterTimer("CheckData", $this->ReadPropertyInteger("Interval"), 'OBS_CheckData($_IPS[\'TARGET\']);');
            $this->SetStatus(102);
        }

        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();

            $this->SetStatus(102);
            $this->SetTimerInterval("CheckData", $this->ReadPropertyInteger("Interval")*1000);

            $this->ConnectParent("{3AB77A94-3467-4E66-8A73-840B4AD89582}"); 
            $this->GetConfigurationForParent();

        }

        public function CheckData()
        {
            //Buffer Clear
            $BufferList = $this->GetBufferList();
            if (count($BufferList) > 0) {
                foreach ($BufferList as $item) {
                    if (strpos($item, '-') !== false) {
                        $split_item = explode("-", $item);
                        $diff = time() - $split_item[0];

                        if ($diff >= 5) {
                            $this->SetBuffer($item, "");
                        }
                    }
                }
            }

            $send_data = array();
            $send_data["request-type"] = "GetCurrentScene";
            $send_data["message-id"] = "GetCurrentScene";


            //$this->SendData($send_data);
        }


        public function SetMute(string $device = null, bool $value = true)
        {
            if($device == null) return;
            $source = str_replace("Mute_", "", $device);

            $send_data = array();
            $send_data["request-type"] = "SetMute";
            $send_data["message-id"] = "SetMute";
            $send_data["source"] = $source;
            $send_data["mute"] = $value;

            $this->SendData($send_data);
            $this->SetValue("Mute_".$source, $value);
        }

        public function SetVolume(string $device = null, int $value = 0)
        {
            if($device == null) return;
            $source = str_replace("Audio_", "", $device);

            $send_data = array();
            $send_data["request-type"] = "SetVolume";
            $send_data["message-id"] = "SetVolume";
            $send_data["source"] = $source;
            $send_data["volume"] = $value/100.0;

            $this->SendData($send_data);
            $this->SetValue("Audio_".$source, $value);
        }

        public function SetStreaming(bool $value = false)
        {
            $send_data = array();
            if($value){
                $send_data["request-type"] = "StartStreaming";
                $send_data["message-id"] = "StartStreaming";
            }else{
                $send_data["request-type"] = "StopStreaming";
                $send_data["message-id"] = "StopStreaming";
            }

            $this->SendData($send_data);
            $this->SetValue("Streaming", $value);
        }

        public function SwitchScenes(string $scene_name = null){
            if($scene_name == null) return;

            $send_data = array();
            $send_data["request-type"] = "SetCurrentScene";
            $send_data["message-id"] = "SetCurrentScene";
            $send_data["scene-name"] = $scene_name;

            $this->SetValue("CurrentScene", $scene_name);
            $this->SendData($send_data);
        }

        public function SetCurrentTransition(string $transition_name = null){
            if($transition_name == null) return;

            $send_data = array();
            $send_data["request-type"] = "SetCurrentScene";
            $send_data["message-id"] = "SetCurrentScene";
            $send_data["scene-name"] = $transition_name;

            $this->SetValue("CurrentTransition", $transition_name);
            $this->SendData($send_data);
        }


        public function ListProfiles(bool $retrun_value = false){
            $send_data = array();
            $send_data["request-type"] = "GetSourcesList";
            $send_data["message-id"] = "GetSourcesList";

            $this->SendData($send_data, $retrun_value);
        }

        public function SendData(array $array = null, bool $retrun_value = false){
            $timeout = $this->ReadPropertyInteger("Timeout")*10;
            if($array == null || count($array) == 0) return;


            if($retrun_value) {
                $array["message-id"] = time() ."-". $array["message-id"];
            }

            $send_str = json_encode($array);
            $resultat = $this->SendDataToParent(json_encode(Array("DataID" => "{BC49DE11-24CA-484D-85AE-9B6F24D89321}", "FrameTyp" => 1, "Fin" => true, "Buffer" => $send_str)));

            if($retrun_value){
                $i = 0;
                while($i < $timeout){ //Max Sec.

                    $BufferList = $this->GetBufferList();
                    if(in_array($array["message-id"], $BufferList)) {
                        $Bufferdata = $this->GetBuffer($array["message-id"]);
                        $this->SetBuffer($array["message-id"], "");
                        return $Bufferdata;
                    }

                    usleep(100000);
                    $i++;
                }
            }
        }

        public function ReceiveData($JSONString) {
            $data = json_decode($JSONString);
            $ws_data =  json_decode($data->Buffer, true);
            $this->SendDebug("ReceiveData", json_encode($ws_data), 0);

            if(array_key_exists("message-id", $ws_data)){
                if (strpos($ws_data["message-id"], '-') !== false) {
                   $this->SetBuffer($ws_data["message-id"],  json_encode($ws_data));
                }

                switch($ws_data["message-id"]){
                    case "GetCurrentScene":
                        $this->SetValue("", "");
                        break;
                    case "":
                        break;
                }
            }elseif(array_key_exists("update-type", $ws_data)){
                switch($ws_data["update-type"]){
                    case "SwitchScenes":
                        $this->SetValue("CurrentScene", $ws_data["scene-name"]);
                        break;
                    case "SwitchTransition":
                        $this->SetValue("CurrentTransition", $ws_data["transition-name"]);
                        break;
                    case "StreamStatus":
                        $this->SetValue("Streaming", $ws_data["streaming"]);
                        $this->SetValue("Uploadspeed", $ws_data["bytes-per-sec"]/1024);
                        $this->SetValue("TotalStreamTime", $ws_data["total-stream-time"]);
                        $this->SetValue("FPS", $ws_data["fps"]);
                        break;
                    case "StreamStopped":
                        $this->SetValue("Streaming", false);
                        $this->SetValue("Uploadspeed", 0);
                        $this->SetValue("TotalStreamTime", 0);
                        $this->SetValue("FPS", 0);
                        break;
                }
            }
        }

        public function RequestAction($Ident, $Value) {

            switch(true) {
                case $Ident == "CurrentScene":
                    $this->SwitchScenes($Value);
                    break;
                case $Ident == "CurrentTransition":
                    $this->SetCurrentTransition($Value);
                    break;
                case $Ident == "Streaming";
                    $this->SetStreaming($Value);
                    break;
                case strpos($Ident, 'Mute_') !== false:
                    $this->SetMute($Ident, $Value);
                    break;
                case strpos($Ident, 'Audio_') !== false:
                    $this->SetVolume($Ident, $Value);
                    break;
            }

        }

        public function GetConfigurationForParent() {
            $ipAdress = $this->ReadPropertyString("IPAddress");
            $port = $this->ReadPropertyInteger("Port");

            $WSAdress = "ws://".$ipAdress.":".$port;
            return "{\"URL\": \"".$WSAdress."\"}";
        }
    }

