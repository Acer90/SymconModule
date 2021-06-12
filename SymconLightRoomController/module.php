<?php
// Klassendefinition
class LightRoomController extends IPSModule
{
    protected function SetBuffer($Name, $Daten)
    {
        parent::SetBuffer($Name, serialize($Daten));
    }
    protected function GetBuffer($Name)
    {
        return unserialize(parent::GetBuffer($Name));
    }
    public function __construct($InstanceID)
    {
        parent::__construct($InstanceID);
    }

    public function Create()
    {
        parent::Create();

        $this->RegisterPropertyString("VarList_Sensors","{}");
        $this->RegisterPropertyString("VarList_LightSensors","{}");
        $this->RegisterPropertyString("Data_Scenes","{}");
        $this->RequireParent("{2224E8CF-F240-53F7-937A-E732EC4F7EDA}");

        if (IPS_GetKernelRunlevel() == 10103 /* KR_READY */) {
            $this->UpdateScene();
        }
        $this->UpdateMessageSink();
        $this->UpdateLightData();

        $this->SetBuffer("Running", false);
        $this->SetBuffer("currentScene", -1);
        $this->SetBuffer("oldScene", -1);
        $this->RegisterTimer("Update", 1000, 'LightRoomController_UpdateRunningScene($_IPS[\'TARGET\']);');
    }
    public function ApplyChanges() {
        parent::ApplyChanges();
        $this->RequireParent("{2224E8CF-F240-53F7-937A-E732EC4F7EDA}");

        $this->UpdateMessageSink();
        $this->UpdateLightData();

        $this->RegisterMessage(0, 10001 /* IPS_KERNELSTARTED */);
        $this->RegisterMessage($this->InstanceID, 11101 /* FM_CONNECT */);
        $this->RegisterMessage($this->InstanceID, 11102 /* FM_DISCONNECT */);
        if (IPS_GetKernelRunlevel() == 10103 /* KR_READY */) {
            $this->RegisterParent();
            $this->UpdateScene();
        }
    }

    protected function RegisterParent()
    {
        //$this->SendDebug(__FUNCTION__, '', 0);
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
    private function UpdateMessageSink(){
        $VarList = json_decode($this->ReadPropertyString("VarList_Sensors"), true);
        if(!is_array($VarList)) return;
        foreach($VarList as $item){
            if($item["Variable"] > 0 && !$item["disable"]){
                $this->RegisterMessage($item["Variable"], 10603);
                $this->SendDebug("UpdateMessageSink", "RegisterMessage => " . $item["Variable"] . "(10603)",0);
            }

            if($item["disable"]){
                $this->UnregisterMessage($item["Variable"], 10603);
                $this->SendDebug("UpdateMessageSink", "UnregisterMessage => " . $item["Variable"] . "(10603)",0);
            }
        }

        //Lichtsensoren
        $VarList = json_decode($this->ReadPropertyString("VarList_LightSensors"), true);
        if(!is_array($VarList)) return;
        foreach($VarList as $item){
            if($item["Variable"] > 0 && !$item["disable"]){
                $this->RegisterMessage($item["Variable"], 10603);
                $this->SendDebug("UpdateMessageSink", "RegisterMessage => " . $item["Variable"] . "(10603)",0);
            }

            if($item["disable"]){
                $this->UnregisterMessage($item["Variable"], 10603);
                $this->SendDebug("UpdateMessageSink", "UnregisterMessage => " . $item["Variable"] . "(10603)",0);
            }
        }
    }
    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        switch($Message) {
            case 10603:
                //$this->SendDebug("MessageSink", "Sensor " . $SenderID. " satus geändert! => ". print_r($Data),0);
                $lightSensorData = json_decode($this->ReadPropertyString("VarList_LightSensors"), true);
                if(is_array($lightSensorData)) {
                    $key = array_search($SenderID, array_column($lightSensorData, 'Variable'));
                    if ($key !== false) {
                        //LichtSensor
                        //array("Variable" => $id, "disable" => $SensorData["disable"], "Max_val" => $max, "Current" => $cur ."%");

                        $lightData = json_decode($this->GetBuffer("LightData"), true);
                        $max = $lightSensorData[$key]["Max_val"];
                        $cur = $Data[0] / $max * 100;
                        $lightData[$SenderID] = $cur;
                        $this->SendDebug("MessageSink", "Lightsensor " . $SenderID . " new Value => " .$cur. " (".$Data[0].")", 0);

                        $this->SetBuffer("LightData", json_encode($lightData));
                        return;
                    }
                }

                $SensorData = json_decode($this->ReadPropertyString("VarList_Sensors"), true);
                if(is_array($SensorData)) {
                    if (array_search($SenderID, array_column($SensorData, 'Variable')) !== false) {
                        //Bewegungsensor
                        if ($Data[1] == 1 && $Data[0] == 1) {
                            $this->SendDebug("MessageSink", "Motionsensor " . $SenderID . " => activ", 0);
                            $this->StartScene($this->GetCurrentScene());
                        }
                        return;
                    }
                }

                $this->SendDebug("MessageSink", "Ignore Sensor " . $SenderID. " => ". print_r($Data),0);
                break;
            case 10505: /* IM_CHANGESTATUS */
                switch ($Data[0]) {
                    case 102: //Running
                        $this->SendDebug("Connection", "IO connection establish", 0);
                        $this->SetStatus(102);
                        $this->SetBuffer("Running", true);
                        break;
                }
                break;
            case 10001: /* IPS_KERNELSTARTED */
                $this->RegisterParent();
                $this->UpdateScene();
                break;
            case 11101: /* FM_CONNECT */
                $this->RegisterParent();
                break;
            case 11102: /* FM_DISCONNECT */
                $this->RegisterParent();
        }
    }
    private function UpdateLightData(){
        $lightSensorData = json_decode($this->ReadPropertyString("VarList_LightSensors"), true);

        $light_values = array();
        if(is_array($lightSensorData)){
            foreach ($lightSensorData as $SensorData) {
                $id = $SensorData["Variable"];
                $max = $SensorData["Max_val"];
                $cur = -1;

                if($SensorData["disable"]) continue;

                if (IPS_ObjectExists($id)){
                    $cur = GetValue($id) / $max * 100;
                }
                $light_values[$id] = $cur;
            }
        }

        $this->SetBuffer("LightData", json_encode($light_values));
    }
    private function GetLightDataLowestValue(){
        $lightData = json_decode($this->GetBuffer("LightData"), true);
        $r_val = 0;

        if(is_array($lightData) && count($lightData) > 0) $r_val = min($lightData);
        if($r_val > 100) $r_val = 100;

        $this->SendDebug("LightDataGetLowestValue", "Lowest Light Value => ".$r_val. "%", 0);
        return $r_val;
    }

    private function GetCurrentScene(){
        $sceneData = json_decode($this->GetBuffer("sceneData"), true);
        $VarList = json_decode($this->ReadPropertyString("Data_Scenes"), true);
        $prio  = array_column($sceneData, 'Prio');
        array_multisort($prio, SORT_DESC, $sceneData);

        $id = -1;
        foreach ($sceneData as $scene){
            //$this->SendDebug("GetCurrentScene", json_encode($scene),0);
            if($scene["active"] == true && $scene["disable"] == false && $scene["ID"] > 0){
                //Correct Scene Found

                $key = array_search($scene["ID"], array_column($VarList, 'ID'));
                if($key !== FALSE) {
                    if (array_key_exists("Scene", $VarList[$key]) && count($VarList[$key]["Scene"]) > 0) {
                        $this->SendDebug("GetCurrentScene", "Test". json_encode($VarList[$key]),0);
                        $this->SendDebug("GetCurrentScene", "Correct Scene Found ID => " . $scene["ID"],0);
                        $id = $scene["ID"];
                        break;
                    }
                }
            }
        }
        $this->SetBuffer("currentScene", $id);
        return $id;
    }

    private function StartScene(int $ID){
        $VarList = json_decode($this->ReadPropertyString("Data_Scenes"), true);
        $this->SendDebug("StartScene", "Start Scene ID=> ".$ID,0);

        //alle Lichter finden
        $lightsArr = array();
        //$this->SendDebug("StartScene", json_encode($VarList),0);
        foreach ($VarList as $scene){

            if(!array_key_exists("Scene", $scene)) continue;

            foreach ($scene["Scene"] as $item){
                $lightsArr[$item["Instance"]] = false;
            }
        }

        //status lampen abrufen
        $running_lights = json_decode($this->GetBuffer("RunningLights"), true);
        $now = time(); //damit für alle lampen gleich!

        $key = array_search($ID, array_column($VarList, 'ID'));
        if($key !== FALSE){
            if(array_key_exists("Scene", $VarList[$key])) {
                foreach ($VarList[$key]["Scene"] as $light) {
                    //Licht für licht schalten

                    //aber nur wenn die helligkeit zu kein ist
                    $lowLight = $this->GetLightDataLowestValue();

                    if($light["Light"] >= $lowLight){
                        $this->ControlLight($light["Instance"], $light["Zustand_val"], $light["Helligkeit_val"], $light["Temperatur_val"], $light["Farbe_val"]);
                        $lightsArr[$light["Instance"]] = true;

                        if ($light["Time"] > 0) {
                            $this->SendDebug("StartScene", "Set Duration ".$light["Instance"]." => " . $light["Time"]. " (Time=" .$now. ")", 0);
                            $running_lights[$light["Instance"]] = array("Time" => $now, "Duration" => $light["Time"]);
                        }
                    }
                }
            }
        }

        //nicht gefunden geräte ausschalten
        foreach ($lightsArr as $key => $item){
            if($item == false){
                $this->SendDebug("StartScene", "Light Off => ".$key,0);
                $this->ControlLight($key); //licht ausschalten
                unset($running_lights[$key]);
            }
        }

        $this->SetBuffer("RunningLights", json_encode($running_lights));
    }
    private function ReloadScene(int $ID){
        $VarList = json_decode($this->ReadPropertyString("Data_Scenes"), true);
        $this->SendDebug("ReloadScene", "Reload Scene ID=> ".$ID,0);

        //alle Lichter finden
        $lightsArr = array();
        //$this->SendDebug("StartScene", json_encode($VarList),0);
        foreach ($VarList as $scene){

            if(!array_key_exists("Scene", $scene)) continue;

            foreach ($scene["Scene"] as $item){
                $lightsArr[$item["Instance"]] = false;
            }
        }

        //status lampen abrufen
        $running_lights = json_decode($this->GetBuffer("RunningLights"), true);
        $now = time(); //damit für alle lampen gleich!

        $key = array_search($ID, array_column($VarList, 'ID'));
        if($key !== FALSE){
            if(array_key_exists("Scene", $VarList[$key])) {
                foreach ($VarList[$key]["Scene"] as $light) {
                    //Licht für licht schalten

                    $this->ControlLight($light["Instance"], $light["Zustand_val"], $light["Helligkeit_val"], $light["Temperatur_val"], $light["Farbe_val"]);
                    $lightsArr[$light["Instance"]] = true;

                    if ($light["Time"] > 0) {
                        $this->SendDebug("ReloadScene", "Set Duration ".$light["Instance"]." => " . $light["Time"]. " (Time=" .$now. ")", 0);
                        $running_lights[$light["Instance"]] = array("Time" => $now, "Duration" => $light["Time"]);
                    }
                }
            }
        }

        //nicht gefunden geräte ausschalten
        foreach ($lightsArr as $key => $item){
            if($item == false){
                $this->SendDebug("ReloadScene", "Light Off => ".$key,0);
                $this->ControlLight($key); //licht ausschalten
                unset($running_lights[$key]);
            }
        }

        $this->SetBuffer("RunningLights", json_encode($running_lights));
    }
    public function UpdateRunningScene(){
        $oldScene = $this->GetBuffer("oldScene");
        $currentScene = $this->GetBuffer("currentScene");


        $Sensor_activ = false;
        $VarList = json_decode($this->ReadPropertyString("VarList_Sensors"), true);
        if(is_array($VarList)) {
            foreach($VarList as $item){
                if($item["Variable"] > 0 && !$item["disable"]){
                    if(GetValueBoolean($item["Variable"]) == true) $Sensor_activ = true;
                }
            }
        }

        //$this->SendDebug("UpdateRunningScene",  $oldScene. " == ". $currentScene, 0);
        if($currentScene != $oldScene){
            //Änderungen Update auf neue Scene!
            $this->SendDebug("UpdateRunningScene", "New Scene (".$currentScene.") detected!", 0);


            if($Sensor_activ){
                //Livechange Scene
                $this->ReloadScene($currentScene);
            }
        }

        $running_lights = json_decode($this->GetBuffer("RunningLights"), true);
        //$this->SendDebug("UpdateRunningScene", $this->GetBuffer("RunningLights"), 0);
        if(is_array($running_lights)){
            $now = time();
            foreach ($running_lights as $key => $item){
                //pürfen ob die Zeit abgelaufen
                $timespan = $now - $item["Time"];
                if($timespan >= $item["Duration"]){

                    if($Sensor_activ){
                        //Prüfen ob Sensor noch Aktiv, dann Zeit von vorn!
                        $running_lights[$key]["Time"] = $now;
                        $this->SendDebug("UpdateRunningScene", "Licht ID=".$key." einschaltzeit verlängert (".$item["Duration"].")", 0);
                    }else{
                        //Sonst Licht ausschalten
                        $this->SendDebug("UpdateRunningScene", "Licht => ". $key ." auschalten", 0);
                        unset($running_lights[$key]);
                        $this->ControlLight($key);
                    }

                }
            }
        }

        $this->SetBuffer("RunningLights", json_encode($running_lights));
        $this->SetBuffer("oldScene", $currentScene);
    }

    private function ControlLight(int $instance, bool $zustand = false, int $helligkeit = 0, int $temperatur = 0, int $farbe = 0){
        //Reihenfolge Farbe => Temperatur => Helligkeit => An/Aus
        if($instance == 0){
            $this->SendDebug("ControlLight", "Waring! InstanceId 0 not allowed!",0);
            return;
        }
        $instance_info = IPS_GetInstance($instance);

        /*  Mode 0 = Zustand
         *  Mode 1 = Helligkeit
         *  Mode 2 = Temperatur + Helligkeit
         *  Mode 3 = Farbe
         */

        $mode = 0;
        if($helligkeit > 0){ $mode = 1; }
        if ($temperatur > 0 ){ $mode = 2; }
        if ($farbe > 0 ){  $mode = 3; }
        if (!$zustand) { $mode = 0; }

        $this->SendDebug("ControlLight", "Int=>".$instance." Z=>".$zustand." H=>".$helligkeit. " T=>".$temperatur." F=>".$farbe. " Mode=>".$mode,0);

        switch($instance_info["ModuleInfo"]["ModuleID"]){
            case "{729BE8EB-6624-4C6B-B9E5-6E09482A3E36}":
            case "{C47C8889-02C4-40A2-B18A-DBD9E47CE23D}":
                //huelight
                //hueGroup
                if($helligkeit > 0) $helligkeit = round((254 / 100) * $helligkeit);

                $this->SendDebug("TEST", "T=". $temperatur . " H=".$helligkeit, 0);

                if($mode == 0) HUE_SetValues($instance, array("STATE" => $zustand));
                if($mode == 1) HUE_SetValues($instance, array("STATE" => $zustand, "BRIGHTNESS" => $helligkeit));
                if($mode == 2) HUE_SetValues($instance, array("STATE" => $zustand, "COLOR_TEMPERATURE" => $temperatur, "BRIGHTNESS" => $helligkeit));
                if($mode == 3) HUE_SetValues($instance, array("STATE" => $zustand, "COLOR" => $farbe));
                break;
            case "{EE4A81C6-5C90-4DB7-AD2F-F6BBD521412E}":
                //HomeMatic Device
                if($mode == 0) HM_WriteValueBoolean($instance, "STATE", $zustand);
                if($mode == 1) {
                    HM_WriteValueBoolean($instance, "STATE", $zustand);
                    HM_WriteValueFloat($instance, "LEVEL", ($helligkeit / 100.0));
                }
                if($mode == 2)
                if($mode == 3){
                    HM_WriteValueBoolean($instance, "STATE", $zustand);
                    HM_WriteValueFloat($instance, "LEVEL", ($helligkeit / 100.0));
                    HM_WriteValueInteger($instance, 'COLOR', $farbe);
                }
                break;
            case "{E5BB36C6-A70B-EB23-3716-9151A09AC8A2}":
                //zigbee2mqtt
                $var_color = 0;
                $var_state = 0;
                $var_level = 0;
                $var_temp = 0;
                foreach(IPS_GetObject($instance)["ChildrenIDs"] as $var_item){
                    $obj = IPS_GetObject($var_item);

                    switch($obj["ObjectIdent"]){

                        case "Z2M_Brightness":
                            $var_level = $var_item;
                            break;
                        case "Z2M_ColorTemp":
                            $var_temp = $var_item;
                            break;
                        case "Z2M_State":
                        case "Z2M_Statel1":
                        case "Z2M_Statel2":
                        case "Z2M_Statel3":
                        case "Z2M_Statel4":
                            $var_state = $var_item;
                            break;
                        case "Z2M_Color":
                            $this->SendDebug("ControlLight", "Ident:".$obj["ObjectIdent"],0);
                            $var_color = $var_item;
                            break;
                    }
                }
                //$this->SendDebug("ControlLight", "Mode:".$mode,0);
                //$this->SendDebug("ControlLight", "state:".$var_state."|level:".$var_level."|temp:".$var_temp."|color:".$var_color,0);

                if($mode == 0 && IPS_VariableExists($var_state)) RequestAction($var_state, $zustand);
                elseif($mode == 1) {
                    if(IPS_VariableExists($var_level)) RequestAction($var_level, round($helligkeit * 2.55));
                    //if(IPS_VariableExists($var_state)) RequestAction($var_state, $zustand);
                }
                elseif($mode == 2){
                    if(IPS_VariableExists($var_level)) RequestAction($var_level, round($helligkeit * 2.55));
                    if(IPS_VariableExists($var_temp)) RequestAction($var_temp, $temperatur);
                    //if(IPS_VariableExists($var_state)) RequestAction($var_state, $zustand);
                }
                elseif($mode == 3){
                    if(IPS_VariableExists($var_color)) RequestAction($var_color, $farbe);
                    if(IPS_VariableExists($var_level)) RequestAction($var_level, round($helligkeit * 2.55));
                    //if(IPS_VariableExists($var_state)) RequestAction($var_state, $zustand);
                }
                break;
            default:
                //Device not found!
                $this->SendDebug("ControlLight", "Device not defined! Please contact the Dev!",0);
        }
    }

    public function GetConfigurationForm(){
        parent::GetConfigurationForm();
        $sceneData = json_decode($this->GetBuffer("sceneData"), true);
        $lightSensorData = json_decode($this->ReadPropertyString("VarList_LightSensors"), true);

        $form = array("elements" => array(), "actions" => array(), "status" => array());
        if(!is_array($sceneData)) return json_encode($form);

        $values = array();
        foreach ($sceneData as $scene){
            $values[] = array("ID" => $scene["ID"], "Name" => $scene["Name"], "Prio" => $scene["Prio"], "Scenes" => array(array("Instance" => 0, "Zustand_val" => false, "Helligkeit_val" => 0, "Temperatur_val" => 0, "Farbe_val" => 0, "Time" => 0, "Licht" => 0))); //
        }

        $light_values = array();
        if(is_array($lightSensorData)){
            $lightData = json_decode($this->GetBuffer("LightData"), true);
            foreach ($lightSensorData as $SensorData) {
                $id = $SensorData["Variable"];
                $max = $SensorData["Max_val"];
                $cur = "---";

                if(array_key_exists($id, $lightData)) $cur = $lightData[$id] ."%";

                $light_values[] = array("Variable" => $id, "disable" => $SensorData["disable"], "Max_val" => $max, "Current" => $cur);
            }
        }

        $form = array("elements" => array(), "actions" => array(), "status" => array());

        //Actions
        $form["elements"][] = json_decode('{ "type": "Label", "label": "Sensoren" }', true);
        $form["elements"][] = json_decode('{
            "type": "List",
            "name": "VarList_Sensors",
            "caption": "Bewegungsmelder",
            "rowCount": 3,
            "add": true,
            "delete": true,
            "sort": {
                "column": "Variable",
                "direction": "ascending"
            },
            "columns": [
            {
                "label": "Variable",
                "name": "Variable",
                "width": "auto",
                "add": 0,
                "edit": {
                    "type": "SelectVariable"
                }
            }, {
                    "caption": "Deaktiviert",
                    "name": "disable",
                    "width": "120px",
                    "add": false,
                    "edit": {
                      "type": "CheckBox"
                }
           }]
        }', true);

        $form["elements"][] = json_decode('{
            "type": "List",
            "name": "VarList_LightSensors",
            "caption": "Lichtsensoren",
            "rowCount": 3,
            "add": true,
            "delete": true,
            "sort": {
                "column": "Variable",
                "direction": "ascending"
            },
            "columns": [
            {
                "label": "Variable",
                "name": "Variable",
                "width": "auto",
                "add": 0,
                "edit": {
                    "type": "SelectVariable"
                }
            }, {
                "label": "Max",
                "name": "Max_val",
                "width": "80px",
                "add": 0,
                "edit": {
                    "type": "NumberSpinner",
                    "minimum": 0
                }
            }, {
                    "caption": "Deaktiviert",
                    "name": "disable",
                    "width": "120px",
                    "add": false,
                    "edit": {
                      "type": "CheckBox"
                }
           }, {
                "caption": "Current",
                "name": "Current",
                "width": "80px",
                "add": ""
            }],
            "values": '.json_encode($light_values).'   
        }', true);

        $form["elements"][] = json_decode('{
            "type": "List",
            "name": "Data_Scenes",
            "caption": "Scenen",
            "rowCount": 10,
            "sort": {
                "column": "Prio",
                "direction": "descending"
            },
            "columns": [
            {
                "caption": "ID",
                "name": "ID",
                "width": "40px",
                "add": "0",
                "edit": {
                    "type": "NumberSpinner"
                }
            }, {
                "caption": "Scene",
                "name": "Name",
                "width": "160px",
                "add": ""
            }, {
                "caption": "Prio",
                "name": "Prio",
                "width": "80px",
                "add": ""
            }, {
                "caption": "",
                "name": "Scene",
                "width": "auto",
                "add": "",
                "delete": true,
                "edit": {
                    "type": "List",
                    "rowCount": 5,
                    "add": true,
                    "delete": true,
                    "sort": {
                        "column": "Instance",
                        "direction": "ascending"
                    },
                    "columns": [
                    {
                        "label": "Instance",
                        "name": "Instance",
                        "width": "auto",
                        "add": 0,
                        "edit": {
                            "type": "SelectInstance"
                        }
                    }, {
                        "label": "Zustand",
                        "name": "Zustand_val",
                        "width": "80px",
                        "add": true,
                        "edit": {
                            "type": "CheckBox"
                        }
                    }, {
                        "label": "Helligkeit",
                        "name": "Helligkeit_val",
                        "width": "80px",
                        "add": 100,
                        "edit": {
                            "type": "NumberSpinner",
                            "minimum": 0,
                            "maximum": 100,
                            "suffix": "%"
                        }
                    }, {
                        "label": "Temperatur",
                        "name": "Temperatur_val",
                        "width": "80px",
                        "add": 0,
                        "edit": {
                            "type": "NumberSpinner"
                        }
                    }, {
                        "label": "Farbe",
                        "name": "Farbe_val",
                        "width": "80px",
                        "add": 0,
                        "edit": {
                            "type": "SelectColor"
                        }
                    }, {
                    "label": "Time",
                    "name": "Time",
                    "width": "120px",
                    "add": 0,
                    "edit": {
                        "type": "NumberSpinner",
                            "minimum": 0,
                            "suffix": "Sek."
                        }
                    }, {
                    "label": "min. Licht",
                    "name": "Light",
                    "width": "120px",
                    "add": 0,
                    "edit": {
                        "type": "NumberSpinner",
                            "minimum": 0,
                            "maximum": 100,
                            "suffix": "%"
                        }
                    }]
                }
           }],
           "values": '.json_encode($values).'        
           }', true);

        $form["actions"][] = json_decode('{ "type": "Button", "label": "Update", "onClick": "echo LightRoomController_UpdateConfigurationForm($id);" }', true);
        $form["actions"][] = json_decode('{ "type": "Button", "label": "Update SceneList", "onClick": "echo LightRoomController_UpdateScene($id);" }', true);

        return json_encode($form);
    }
    public function UpdateConfigurationForm(){
        $this->ReloadForm();
    }

    private function UpdateSceneList(){
        $sceneData = json_decode($this->GetBuffer("sceneData"), true);
        $curScenesData = json_decode($this->ReadPropertyString("Data_Scenes"), true);
        $changes = false;

        //alte löschen
        foreach ($curScenesData as $key => $item){
            if(array_key_exists("ID", $item)){
                if(array_search($item["ID"], array_column($sceneData, 'ID')) === false) {
                    //nicht vorhanden! LÖSCHEN!
                    unset($curScenesData[$key]);
                    $changes = true;
                }
            }else{
                unset($curScenesData[$key]);
                $changes = true;
            }
        }

        //neue finden
        foreach ($sceneData as $key => $item){
            if(array_search($item["ID"], array_column($curScenesData, 'ID')) === false) {
                //nicht vorhanden! Anlegen!
                $curScenesData[]["ID"] = $item["ID"];
                //$curScenesData[]["Scenes"] = "[]";
                $changes = true;
            }
        }

        //Updaten bei änderung
        if($changes){
            IPS_SetProperty($this->InstanceID, "Data_Scenes", json_encode($curScenesData));
            IPS_ApplyChanges($this->InstanceID);
            $this->ReloadForm();
        }
    }

    public function UpdateScene(){
        $this->SendDataToParent(json_encode([
            'DataID' => "{2B43AAD9-4E36-CFCE-8EE3-C765710C9005}",
            'Buffer' => utf8_encode("UpdateMessageSink"),
        ]));
    }
    public function ReceiveData($JSONString) {
        $jsonData = json_decode($JSONString, true);
        $sceneData = json_decode($jsonData['Buffer'], true);

        $this->SendDebug("ReceiveData", $jsonData['Buffer'], 0);
        $this->SetBuffer("sceneData" , json_encode($sceneData));

        $this->UpdateSceneList();
        $this->GetCurrentScene();
    }


}