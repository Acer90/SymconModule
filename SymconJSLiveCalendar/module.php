<?php
include_once (__DIR__ . '/../SymconJSLive/libs/JSLiveModule.php');

class SymconJSLiveCalendar extends JSLiveModule{
    public function Create() {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent("{9FFF3FC0-FD51-C289-FA36-BC1C370946CF}");

        //Expert
        $this->RegisterPropertyBoolean("Debug", false);
        $this->RegisterPropertyInteger("ViewLevel", 0);
        $this->RegisterPropertyBoolean("EnableCache", true);
        $this->RegisterPropertyBoolean("CreateOutput", true);
        $this->RegisterPropertyBoolean("CreateIPSView", true);
        $this->RegisterPropertyInteger("TemplateScriptID", 0);
        $this->RegisterPropertyBoolean("EnableViewport", true);
        $this->RegisterPropertyInteger("IFrameHeight", 0);
        $this->RegisterPropertyInteger("overrideWidth", 0);
        $this->RegisterPropertyInteger("overrideHeight", 0);
        $this->RegisterPropertyString("CustomCSS", "");

        //buttons
        $this->RegisterPropertyFloat("buttons_fontSize", 1.0);
        $this->RegisterPropertyString("buttons_fontSize_unitType", "em");
        $this->RegisterPropertyInteger("buttons_fontColor", 16777215);
        $this->RegisterPropertyString("buttons_fontFamily", "");
        $this->RegisterPropertyInteger("buttons_backgroundColor", 3626869);
        $this->RegisterPropertyFloat("buttons_backgroundColor_Alpha", 1.00);
        $this->RegisterPropertyInteger("buttons_backgroundColorHover", 2703703);
        $this->RegisterPropertyFloat("buttons_backgroundColorHover_Alpha", 1.00);
        $this->RegisterPropertyInteger("buttons_borderColor", 3626869);
        $this->RegisterPropertyFloat("buttons_borderColor_Alpha", 1.00);
        $this->RegisterPropertyFloat("buttons_borderWidth", 0.25);
        $this->RegisterPropertyString("buttons_borderWidth_unitType", "em");
        $this->RegisterPropertyString("buttons_borderWidth_Expert", "0.25em 0.25em 0.25em 0.25em");
        $this->RegisterPropertyFloat("buttons_borderRadius", 0.25);
        $this->RegisterPropertyString("buttons_borderRadius_unitType", "em");
        $this->RegisterPropertyString("buttons_borderRadius_Expert", "0.25em 0.25em 0.25em 0.25em");

        //title
        $this->RegisterPropertyFloat("title_fontSize", 1.0);
        $this->RegisterPropertyString("title_fontSize_unitType", "em");
        $this->RegisterPropertyInteger("title_fontColor", 0);
        $this->RegisterPropertyString("title_fontFamily", "");
        $this->RegisterPropertyString("title_formatYear", "numeric");
        $this->RegisterPropertyString("title_formatMonth", "short");
        $this->RegisterPropertyString("title_formatDay", "numeric");

        //Header
        $this->RegisterPropertyBoolean("header_display", True);
        $this->RegisterPropertyString("header_Toolbar_start", "[{\"Type\":\"title\",\"connectPrevious\":false}]");
        $this->RegisterPropertyString("header_Toolbar_center", "[{\"Type\":\"timeGridDay\",\"connectPrevious\":false},{\"Type\":\"timeGridWeek\",\"connectPrevious\":true},{\"Type\":\"dayGridMonth\",\"connectPrevious\":true}]");
        $this->RegisterPropertyString("header_Toolbar_end", "[{\"Type\":\"prev\",\"connectPrevious\":false},{\"Type\":\"next\",\"connectPrevious\":true}]");
        $this->RegisterPropertyInteger("header_backgroundColor", -1);
        $this->RegisterPropertyFloat("header_backgroundColor_Alpha", 1.0);
        $this->RegisterPropertyFloat("header_margin", 1.5);
        $this->RegisterPropertyString("header_margin_unitType", "em");

        //footer
        $this->RegisterPropertyBoolean("footer_display", False);
        $this->RegisterPropertyString("footer_Toolbar_start", "[]");
        $this->RegisterPropertyString("footer_Toolbar_center", "[]");
        $this->RegisterPropertyString("footer_Toolbar_end", "[]");
        $this->RegisterPropertyInteger("footer_backgroundColor", -1);
        $this->RegisterPropertyFloat("footer_backgroundColor_Alpha", 1.0);
        $this->RegisterPropertyFloat("footer_margin", 1.5);
        $this->RegisterPropertyString("footer_margin_unitType", "em");

        //table
        $this->RegisterPropertyInteger("table_today_backgroundColor", 16768040);
        $this->RegisterPropertyFloat("table_today_backgroundColor_Alpha", 0.15);

        $this->RegisterPropertyInteger("table_backgroundColor", -1);
        $this->RegisterPropertyFloat("table_backgroundColor_Alpha", 0.0);
        $this->RegisterPropertyFloat("table_body_day_fontSize", 1.0);
        $this->RegisterPropertyString("table_body_day_fontSize_unitType", "em");
        $this->RegisterPropertyInteger("table_body_day_fontColor", 0);
        $this->RegisterPropertyString("table_body_day_fontFamily", "");
        $this->RegisterPropertyFloat("table_body_dayOther_fontSize", 1.0);
        $this->RegisterPropertyString("table_body_dayOther_fontSize_unitType", "em");
        $this->RegisterPropertyInteger("table_body_dayOther_fontColor", 0);
        $this->RegisterPropertyString("table_body_dayOther_fontFamily", "");

        $this->RegisterPropertyInteger("table_fontColor", 0);
        $this->RegisterPropertyFloat("table_fontSize", 1.0);
        $this->RegisterPropertyString("table_fontSize_unitType", "em");
        $this->RegisterPropertyString("table_fontFamily", "");
        $this->RegisterPropertyInteger("table_borderColor", -1);
        $this->RegisterPropertyFloat("table_borderColor_Alpha", 1.0);
        $this->RegisterPropertyFloat("table_borderWidth", 1.0);
        $this->RegisterPropertyString("table_borderWidth_unitType", "px");
        $this->RegisterPropertyInteger("table_outer_borderColor", -1);
        $this->RegisterPropertyFloat("table_outer_borderColor_Alpha", 1.0);
        $this->RegisterPropertyFloat("table_outer_borderWidth", 1.0);
        $this->RegisterPropertyString("table_outer_borderWidth_unitType", "px");

        $this->RegisterPropertyInteger("table_header_backgroundColor", -1);
        $this->RegisterPropertyFloat("table_header_backgroundColor_Alpha", 0.0);
        $this->RegisterPropertyFloat("table_header_fontSize", 1.0);
        $this->RegisterPropertyString("table_header_fontSize_unitType", "em");
        $this->RegisterPropertyInteger("table_header_fontColor", 0);
        $this->RegisterPropertyString("table_header_fontFamily", "");

        $this->RegisterPropertyBoolean("table_weekend_header_override", False);
        $this->RegisterPropertyInteger("table_weekend_header_backgroundColor", -1);
        $this->RegisterPropertyFloat("table_weekend_header_backgroundColor_Alpha", 0.0);
        $this->RegisterPropertyFloat("table_weekend_header_fontSize", 1.0);
        $this->RegisterPropertyString("table_weekend_header_fontSize_unitType", "em");
        $this->RegisterPropertyInteger("table_weekend_header_fontColor", 0);
        $this->RegisterPropertyString("table_weekend_header_fontFamily", "");

        $this->RegisterPropertyBoolean("table_weekend_saturday_override", False);
        $this->RegisterPropertyInteger("table_weekend_saturday_backgroundColor", -1);
        $this->RegisterPropertyFloat("table_weekend_saturday_backgroundColor_Alpha", 0.0);
        $this->RegisterPropertyFloat("table_weekend_saturday_fontSize", 1.0);
        $this->RegisterPropertyString("table_weekend_saturday_fontSize_unitType", "em");
        $this->RegisterPropertyInteger("table_weekend_saturday_fontColor", 0);
        $this->RegisterPropertyString("table_weekend_saturday_fontFamily", "");

        $this->RegisterPropertyBoolean("table_weekend_sunday_override", False);
        $this->RegisterPropertyInteger("table_weekend_sunday_backgroundColor", -1);
        $this->RegisterPropertyFloat("table_weekend_sunday_backgroundColor_Alpha", 0.0);
        $this->RegisterPropertyFloat("table_weekend_sunday_fontSize", 1.0);
        $this->RegisterPropertyString("table_weekend_sunday_fontSize_unitType", "em");
        $this->RegisterPropertyInteger("table_weekend_sunday_fontColor", 0);
        $this->RegisterPropertyString("table_weekend_sunday_fontFamily", "");

        $this->RegisterPropertyBoolean("table_weekNumbers_display", False);
        $this->RegisterPropertyString("table_weekNumberFormat", "numeric");
        $this->RegisterPropertyInteger("table_weekNumber_backgroundColor", -1);
        $this->RegisterPropertyFloat("table_weekNumber_backgroundColor_Alpha", 0.0);
        $this->RegisterPropertyFloat("table_weekNumber_fontSize", 1.0);
        $this->RegisterPropertyString("table_weekNumber_fontSize_unitType", "em");
        $this->RegisterPropertyInteger("table_weekNumber_fontColor", 0);
        $this->RegisterPropertyString("table_weekNumber_fontFamily", "");

        $this->RegisterPropertyFloat("table_events_fontSize", 0.85);
        $this->RegisterPropertyString("table_events_fontSize_unitType", "em");
        $this->RegisterPropertyString("table_events_fontFamily", "");

        $this->RegisterPropertyString("initialView", "dayGridMonth");
        $this->RegisterPropertyString("dataEvents", "[]");
        $this->RegisterPropertyString("customViews", "[]");

    }
    public function ApplyChanges() {
        //Never delete this line!
        parent::ApplyChanges();

        $this->SetBuffer("OutputCSS", "");
        
    }

    public function GetConfigurationForm() {
        $ical_instances = IPS_GetInstanceListByModuleID("{5127CDDC-2859-4223-A870-4D26AC83622C}");
        //if(count($ical_instances) === 0) return '{ "actions": [ { "type": "Label", "label": "'.$this->Translate("No ModulInstances iCal Calendar Reader found!").'" } ] }';

        //update Items for InstanceSelectList!
        $formData = $this->LoadConfigurationForm();
        $key = 0;
        foreach ($formData["elements"] as $keyNr => $element) {
            if (array_key_exists("name", $element) && $element["name"] === "dataEvents") {
                $key = $keyNr;
                break;
            }
        }

        //ICal Module laden und anbieten
        if(count($ical_instances) > 0) {
            $colData = $formData["elements"][$key]["columns"];
            $colkey = array_search("moduleInstance", array_column($colData, 'name'));

            $options_arr = array();
            $options_arr[] = array("value" => "", "caption" => "");
            foreach ($ical_instances as $instanceID) {
                $options_arr[] = array("value" => $instanceID, "caption" => $instanceID . " => " . IPS_GetObject($instanceID)["ObjectName"]);
            }

            $formData["elements"][$key]["columns"][$colkey]["add"] = $options_arr[0]["value"];
            $formData["elements"][$key]["columns"][$colkey]["edit"]["options"] = $options_arr;
        }

        //update Toolbar and initalview with customeview
        $viewsoptions = array();
        $key = 0;
        foreach ($formData["elements"] as $keyNr => $element) {
            if (array_key_exists("name", $element) && $element["name"] === "customView") {
                $key = $keyNr;
                break;
            }
        }
        $views = json_decode($this->ReadPropertyString("customViews"), true);
        $ignoreViews = array("dayGridMonth", "timeGridDay", "timeGridWeek", "listDay", "listWeek", "listMonth", "listYear", "dayGridDay", "dayGridWeek" );

        foreach ($views as $row => $view) {
            $formData["elements"][$key]["items"][0]["values"][$row] = $view;

            if(empty($view["name"])){
                $formData["elements"][$key]["items"][0]["values"][$row]["rowColor"] = "#ff0000";
            }else{
                if(in_array($view["name"], $ignoreViews)) continue;
                $iName = "view_".preg_replace('/[^A-Za-z0-9._]/', '', $view["name"]);
                $viewsoptions[] = array("caption" => $view["name"], "value" => $iName);
            }
        }

        if(count($viewsoptions) > 0){
            //alle element finden und updaten
            $pathList =$this->GET_PathList($formData["elements"]);
            //print_r($pathList);

            //initialview
            $key = array_search('initialView', array_column($pathList, 'name'));
            $path = $pathList[$key]["path"];
            $path[] = "options";

            //print_r($path);
            $data = $this->GET_By_KEYPATH($formData["elements"], $path);
            //print_r($data);
            $data = array_merge($data, $viewsoptions);

            $this->SET_By_KEYPATH($path, $formData["elements"], $data);

            //toolbars
            $array_toolbars = array("header_Toolbar_start", "header_Toolbar_center", "header_Toolbar_end", "footer_Toolbar_start", "footer_Toolbar_center", "footer_Toolbar_end");

            foreach($array_toolbars as $lookup){
                $key = array_search($lookup, array_column($pathList, 'name'));
                $path = $pathList[$key]["path"];
                $path[] = "columns";
                $path[] = 0;
                $path[] = "edit";
                $path[] = "options";

                //print_r($path);

                $data = $this->GET_By_KEYPATH($formData["elements"], $path);
                if($data == null || empty($data) || count($data) === 0) continue;
                $data = array_merge($data, $viewsoptions);

                $this->SET_By_KEYPATH($path, $formData["elements"], $data);
            }
        }

        return json_encode($formData);
    }

    public function ReceiveData($JSONString) {
        parent::ReceiveData($JSONString);
        $jsonData = json_decode($JSONString, true);
        $buffer = json_decode($jsonData['Buffer'], true);

        switch($buffer['cmd']) {
            case "exportConfiguration":
                return $this->ExportConfiguration();
            case "getContend":
                return $this->GetOutput();
            case "getData":
                return $this->GetData($buffer['queryData']);
            case "getFeed":
                return $this->GetFeed($buffer['queryData']);
            case "getCSS":
                return $this->GetCSS();
            case "setData":
                return $this->SetData($buffer['queryData']);
            case "getICS":
                return $this->GetICS($buffer['queryData']['md5']);
            default:
               if($buffer['cmd'] != "UpdateCache")
                    $this->SendDebug("ReceiveData", "ACTION " . $buffer['cmd'] . " FOR THIS MODULE NOT DEFINED!", 0);
                break;
        }
    }
    protected function GetWebpage(){
        $scriptID = $this->ReadPropertyInteger("TemplateScriptID");
        if(empty($scriptID)){
            if($this->ReadPropertyBoolean("Debug"))
                $this->SendDebug('GetWebpage', 'load default template!', 0);
            $scriptData = file_get_contents(__DIR__ ."/../SymconJSLive/templates/Calendar.html");
        }else{
            if(!IPS_ScriptExists($scriptID)){
                $this->SendDebug('GetWebpage', 'Template NOT FOUND!', 0);
                return "";
            }

            $scriptData = IPS_GetScriptContent($scriptID);
            if($scriptData == ""){
                $this->SendDebug('GetWebpage', 'Template IS EMPTY!', 0);
            }
        }

        //$this->SendDebug('GetWebpage', $scriptData, 0);
        $scriptData = $this->ReplacePlaceholder($scriptData);

        return $scriptData;
    }
    private function GetFeed(array $querydata){
        $output = array();
        if(!array_key_exists("id", $querydata)) return json_encode($output);

        $InstanceID = (int) $querydata['id'];
        if (!IPS_ObjectExists($InstanceID)) return json_encode($output);

        $InstanceInfo = IPS_GetInstance($InstanceID);
        switch ($InstanceInfo['ModuleInfo']['ModuleID']) {
            case '{5127CDDC-2859-4223-A870-4D26AC83622C}': // reader instance
                /** @noinspection PhpUndefinedFunctionInspection */
                $CalendarFeed = json_decode(ICCR_GetCachedCalendar($InstanceID), true);
                break;
            case '{F22703FF-8576-4AB1-A0E7-02E3116CD3BA}': // notifier instance
                /** @noinspection PhpUndefinedFunctionInspection */
                $CalendarFeed = json_decode(ICCN_GetNotifierPresenceReason($InstanceID), true);
                break;
            default:
                // no job for us
                doReturn();
        }

        if (empty($CalendarFeed )) return json_encode($output);

        foreach ($CalendarFeed as $Event)
        {
            $CalEvent = array();
            $CalEvent[ "id" ] = $Event[ "UID" ];
            $CalEvent[ "title" ] = $Event[ "Name" ];
            $CalEvent[ "start" ] = $Event[ "FromS" ];
            $CalEvent[ "end" ] = $Event[ "ToS" ];

            if (isset($Event['allDay'])) $CalEvent['allDay'] = $Event['allDay'];

            $output[] = $CalEvent;
        }
        return json_encode($output);
    }
    private function GetCSS(){
        $EnableCache = $this->ReadPropertyBoolean("EnableCache");
        if($EnableCache){
            //Load data from Cache
            if(empty($this->GetBuffer("OutputCSS"))){
                //updateCache when empty
                $this->SetBuffer("OutputCSS", $this->GenerateCSS());
            }

            if($this->ReadPropertyBoolean("Debug"))
                $this->SendDebug("GetOutput", "Get Data form Cache!", 0);
            return json_encode(array("Contend" => $this->GetBuffer("OutputCSS"), "lastModify" => $this->GetBuffer("LastModifed"), "EnableCache" => $EnableCache, "InstanceID" => $this->InstanceID));
        }else{
            return json_encode(array("Contend" => $this->GenerateCSS(), "lastModify" => time(), "EnableCache" => $EnableCache, "InstanceID" => $this->InstanceID));
        }
    }
    private function GetICS($md5){
        $data = json_decode($this->ReadPropertyString("dataEvents"), true);
        $fileData = "";

        foreach($data as $item){
            if(!empty($item["moduleInstance"])) continue;
            if(!empty($item["ical"]) && md5($item["ical"]) === $md5){
                $this->SendDebug("GetICS", "Loading File => " . $item["Name"], 0);

                $fileData = base64_decode($item["ical"]);
                break;
            }elseif(!empty($item["icalLink"]) && md5($item["icalLink"]) === $md5){
                $this->SendDebug("GetICS", "Loading Link => " . $item["icalLink"], 0);

                $fileData = file_get_contents($item["icalLink"]);
                break;
            }
        }

        if(empty($fileData)) $fileData = "File Not Found!";

        return $fileData;
    }
    private function GenerateCSS(){
        $viewLevel = $this->ReadPropertyInteger("ViewLevel");
        if(!empty($this->ReadPropertyString("CustomCSS"))){
            $css_String = base64_decode($this->ReadPropertyString("CustomCSS"));
        }else {
            $css_String = file_get_contents(realpath(__DIR__ . "/../SymconJSLive/js/fullcalendar/PLACEHOLDER-main.css"));

            $pattern = '/\#([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)#/';
            preg_match_all($pattern, $css_String, $matches);

            $config = json_decode(IPS_GetConfiguration($this->InstanceID), true);

            //override function
            if($config["table_weekend_header_override"] == false){
                $config["table_weekend_header_backgroundColor"] = $config["table_header_backgroundColor"];
                $config["table_weekend_header_backgroundColor_Alpha"] = $config["table_header_backgroundColor_Alpha"];
                $config["table_weekend_header_fontSize"] = $config["table_header_fontSize"];
                $config["table_weekend_header_fontSize_unitType"] = $config["table_header_fontSize_unitType"];
                $config["table_weekend_header_fontColor"] = $config["table_header_fontColor"];
                $config["table_weekend_header_fontFamily"] = $config["table_header_fontFamily"];
            }

            if($config["table_weekend_saturday_override"] == false){
                $config["table_weekend_saturday_backgroundColor"] = $config["table_backgroundColor"];
                $config["table_weekend_saturday_backgroundColor_Alpha"] = $config["table_backgroundColor_Alpha"];
                $config["table_weekend_saturday_fontSize"] = $config["table_body_day_fontSize"];
                $config["table_weekend_saturday_fontSize_unitType"] = $config["table_body_day_fontSize_unitType"];
                $config["table_weekend_saturday_fontColor"] = $config["table_body_day_fontColor"];
                $config["table_weekend_saturday_fontFamily"] = $config["table_body_day_fontFamily"];
            }

            if($config["table_weekend_sunday_override"] == false){
                $config["table_weekend_sunday_backgroundColor"] = $config["table_backgroundColor"];
                $config["table_weekend_sunday_backgroundColor_Alpha"] = $config["table_backgroundColor_Alpha"];
                $config["table_weekend_sunday_fontSize"] = $config["table_body_day_fontSize"];
                $config["table_weekend_sunday_fontSize_unitType"] = $config["table_body_day_fontSize_unitType"];
                $config["table_weekend_sunday_fontColor"] = $config["table_body_day_fontColor"];
                $config["table_weekend_sunday_fontFamily"] = $config["table_body_day_fontFamily"];
            }

            if (is_array($matches) && count($matches) > 0) {
                foreach ($matches[0] as $var) {
                    $obj = str_replace("#", "", $var);

                    if (array_key_exists($obj, $config)) {
                        $pos = strpos(strtolower($obj), "color");
                        if ($pos !== false) {
                            //wenn es sich um eine Farbe handelt
                            $str_color = "";

                            if (array_key_exists($obj . "_Alpha", $config)) {
                                //mit alpha
                                if($config[$obj] < 0){
                                    //ausblenden wenn nicht transparent (-1)
                                    $str_color = "rgba(0, 0, 0, 0)";
                                }else{
                                    $rgbdata = $this->HexToRGB($config[$obj]);
                                    $str_color = "rgba(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ", " . number_format($config[$obj . "_Alpha"], 2, '.', '') . ")";
                                }
                            } else {
                                //ohne Alpha
                                if($config[$obj] < 0){
                                    //weiß wenn transparent (-1)
                                    $str_color = "rgb(0, 0, 0)";
                                }else{
                                    $rgbdata = $this->HexToRGB($config[$obj]);
                                    $str_color = "rgb(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ")";
                                }
                            }

                            $css_String = str_replace($var, $str_color, $css_String);
                        } else {
                            $val = $config[$obj];
                            if(is_float($val)) $val = number_format($val, 2, '.', '');

                            //wenn expertvariable vorhanden ist
                            if (array_key_exists($obj . "_Expert", $config)) {
                                if($viewLevel >= 2){
                                    $val = $config[$obj . "_Expert"];
                                }else{
                                    if (array_key_exists($obj . "_unitType", $config)) {
                                        $val = $val . $config[$obj . "_unitType"];
                                    }
                                }
                            }else{
                                if (array_key_exists($obj . "_unitType", $config)) {
                                    $val = $val . $config[$obj . "_unitType"];
                                }
                            }

                            $css_String = str_replace($var, $val, $css_String);
                        }
                    }
                }
            }
        }

        return $css_String;
    }
    private function GenerateToolbarString($listElement){
        $r_str = "";
        $data = json_decode($this->ReadPropertyString($listElement), true);
        $first = true;

        foreach($data as $item){
            $connectPrevious = true;
            if(array_key_exists("connectPrevious", $item)) $connectPrevious = $item["connectPrevious"];

            if($first){
                $r_str = $item["Type"];
            }elseif(!$connectPrevious){
                $r_str = $r_str . " " . $item["Type"];
            }else{
                $r_str = $r_str . "," . $item["Type"];
            }

            $first = false;
        }

        return $r_str;
    }
    private function GenerateToolbarArray(){
        $arr_ToolbarName = array("header", "footer");
        $arr_ToolbarItems = array("start", "center", "end");
        $output = array();

        foreach($arr_ToolbarName as $Name){
            if($this->ReadPropertyBoolean($Name."_display") == false){
                $output[$Name] = false;
                continue;
            }

            foreach($arr_ToolbarItems as $item){
                $listName = $Name."_Toolbar_".$item;
                $output[$Name][$item] = $this->GenerateToolbarString($listName);
            }
        }

        return $output;
    }
    private function GenerateTitleFormat(){
        $output = array();
        $year = $this->ReadPropertyString("title_formatYear");
        $month = $this->ReadPropertyString("title_formatMonth");
        $day = $this->ReadPropertyString("title_formatDay");

        if(!empty($year)) $output["year"] = $year;
        if(!empty($month)) $output["month"] = $month;
        if(!empty($day)) $output["day"] = $day;

        return $output;
    }
    private function GenerateCustomViews(){
        $output = array();
        $data = json_decode($this->ReadPropertyString("customViews"), true);
        $specialViews = array("dayGridMonth", "timeGridDay", "timeGridWeek", "listDay", "listWeek", "listMonth", "listYear", "dayGridDay", "dayGridWeek" );

        foreach($data as $item){
            if(empty($item["name"])) continue;
            $iName = "view_".preg_replace('/[^A-Za-z0-9._]/', '', $item["name"]);
            if(in_array($item["name"], $specialViews)) $iName = $item["name"];

            $s_output = array();
            if(!empty($item["type"])) $s_output["type"] = $item["type"];
            $s_output["duration"][$item["durationType"]] = $item["duration"];
            $s_output["buttonText"] = $item["name"];

            //timegrid optional
            if($item["type"] == "timeGrid"){
                $s_output["slotDuration"] = $item["slotDuration"];
                $s_output["slotMinTime"] = $item["slotMinTime"];
                $s_output["slotMaxTime"] = $item["slotMaxTime"];
                $s_output["scrollTime"] = $item["scrollTime"];
                $s_output["weekends"] = $item["weekends"];
            }

            $output[$iName] = $s_output;
        }

        return $output;
    }
    private function GetDataEvents(){
        $output = array();
        $data = json_decode($this->ReadPropertyString("dataEvents"), true);

        foreach($data as $item){
            $s_output = array();
            $s_output["Name"] = $item["Name"];
            $s_output["Type"] = "";

            if($item["Color"] >= 0) {
                $rgbdata = $this->HexToRGB($item["Color"]);
                $s_output["color"] = "rgba(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ", " . number_format($item["Color_Alpha"], 2, '.', '') . ")";
            }

            if($item["textColor"] >= 0) {
                $rgbdata = $this->HexToRGB($item["textColor"]);
                $s_output["textColor"] = "rgba(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ")";
            }

            if(!empty($item["moduleInstance"])){
                $s_output["extraParams"] = array("Instance" => $this->InstanceID, "id" => $item["moduleInstance"]);
                $s_output["Type"] = "module";
                $output[] = $s_output;
            }elseif(!empty($item["ical"])){
                //downloadfile verfügbar machen
                $s_output["md5"] = md5($item["ical"]);
                $s_output["format"] = "ics";
                $s_output["Type"] = "file";
                $output[] = $s_output;
            }elseif(!empty($item["icalLink"])){
                $s_output["url"] = $item["icalLink"];
                $s_output["md5"] = md5($item["icalLink"]);
                $s_output["format"] = "ics";
                $s_output["Type"] = "link";
                $output[] = $s_output;
            }
        }

        return $output;
    }

    private function ReplacePlaceholder(string $htmlData){
        //configuration Data
        $htmlData = str_replace("{CONFIG}", $this->json_encode_advanced($this->GetConfigurationData()), $htmlData);

        //DATAEVENTS
        $htmlData = str_replace("{DATAEVENTS}",  $this->json_encode_advanced($this->GetDataEvents()), $htmlData);

        //CustomeViews
        $htmlData = str_replace("{VIEWS}",  $this->json_encode_advanced($this->GenerateCustomViews()), $htmlData);
        //Load Fonts
        $htmlData = str_replace("{FONTS}", $this->LoadFonts(), $htmlData);

        return $htmlData;
    }
    private function GetConfigurationData(){
        $output = json_decode(IPS_GetConfiguration($this->InstanceID), true);
        $output["InstanceID"] = $this->InstanceID;

        //override function
        if($output["table_weekend_header_override"] == false){
            $output["table_weekend_header_backgroundColor"] = $output["table_header_backgroundColor"];
            $output["table_weekend_header_backgroundColor_Alpha"] = $output["table_header_backgroundColor_Alpha"];
            $output["table_weekend_header_fontSize"] = $output["table_header_fontSize"];
            $output["table_weekend_header_fontSize_unitType"] = $output["table_header_fontSize_unitType"];
            $output["table_weekend_header_fontColor"] = $output["table_header_fontColor"];
            $output["table_weekend_header_fontFamily"] = $output["table_header_fontFamily"];
        }

        if($output["table_weekend_saturday_override"] == false){
            $output["table_weekend_saturday_backgroundColor"] = $output["table_backgroundColor"];
            $output["table_weekend_saturday_backgroundColor_Alpha"] = $output["table_backgroundColor_Alpha"];
            $output["table_weekend_saturday_fontSize"] = $output["table_body_day_fontSize"];
            $output["table_weekend_saturday_fontSize_unitType"] = $output["table_body_day_fontSize_unitType"];
            $output["table_weekend_saturday_fontColor"] = $output["table_body_day_fontColor"];
            $output["table_weekend_saturday_fontFamily"] = $output["table_body_day_fontFamily"];
        }

        if($output["table_weekend_sunday_override"] == false){
            $output["table_weekend_sunday_backgroundColor"] = $output["table_backgroundColor"];
            $output["table_weekend_sunday_backgroundColor_Alpha"] = $output["table_backgroundColor_Alpha"];
            $output["table_weekend_sunday_fontSize"] = $output["table_body_day_fontSize"];
            $output["table_weekend_sunday_fontSize_unitType"] = $output["table_body_day_fontSize_unitType"];
            $output["table_weekend_sunday_fontColor"] = $output["table_body_day_fontColor"];
            $output["table_weekend_sunday_fontFamily"] = $output["table_body_day_fontFamily"];
        }

        //alle colorvariablen umwandeln!
        foreach($output as $key => $val){
            $pos = strpos(strtolower($key), "color");
            $pos2 = strpos(strtolower($key), "alpha");
            if ($pos !== false && $pos2 === false) {

                if(array_key_exists($key."_Alpha", $output)){
                    $rgbdata = $this->HexToRGB($val);
                    $output[$key] = "rgba(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ", ".$output[$key."_Alpha"].")";
                }else{
                    $rgbdata = $this->HexToRGB($val);
                    $output[$key] = "rgb(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ")";
                }
            }
        }

        //header and footer
        $toolbarData = $this->GenerateToolbarArray();
        $output["header"] = $toolbarData["header"];
        $output["footer"] = $toolbarData["footer"];

        //gen titleformat
        $output["titleFormat"] = $this->GenerateTitleFormat();

        //remove Dataset
        unset($output["dataEvents"]);
        unset($output["header_Toolbar_start"]);
        unset($output["header_Toolbar_center"]);
        unset($output["header_Toolbar_end"]);

        unset($output["footer_Toolbar_start"]);
        unset($output["footer_Toolbar_center"]);
        unset($output["footer_Toolbar_end"]);

        unset($output["customViews"]);

        return $output;
    }

    public function LoadOtherConfiguration(int $id){
        if(!IPS_ObjectExists($id)) return "Instance/Chart not found!";

        if(IPS_GetObject($id)["ObjectType"] == 1){
            //instance
            $intData = IPS_GetInstance($id);
            if($intData["ModuleInfo"]["ModuleID"] != IPS_GetInstance($this->InstanceID)["ModuleInfo"]["ModuleID"]) return "Only Allowed at the same Modul!";

            $confData = json_decode(IPS_GetConfiguration($id), true);

            IPS_SetConfiguration($this->InstanceID, json_encode($confData));
            IPS_ApplyChanges($this->InstanceID);
        }else return "A Instance must be selected!";
    }

    public function GetCSSLink(){
        $link = $this->GetLink();
        $arr = explode("JSLive?", $link);

        return $arr[0]."JSLive"."/getCSS?".$arr[1];
    }
    public function GetDefaultCSSLink(){
        $link = $this->GetLink();
        $arr = explode("JSLive?", $link);

        return $arr[0]."JSLive"."/js/fullcalendar/main.css";
    }
}

?>