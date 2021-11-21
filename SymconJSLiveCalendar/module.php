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

        //title
        $this->RegisterPropertyFloat("title_fontSize", 1.0);
        $this->RegisterPropertyString("title_fontSize_unitType", "em");
        $this->RegisterPropertyInteger("title_fontColor", 0);
        $this->RegisterPropertyString("title_fontFamily", "");

        //Header
        $this->RegisterPropertyBoolean("header_display", True);
        $this->RegisterPropertyString("header_Toolbar", "[]");
        $this->RegisterPropertyInteger("header_backgroundColor", -1);
        $this->RegisterPropertyFloat("header_backgroundColor_Alpha", 1.0);
        $this->RegisterPropertyFloat("header_margin", 1.5);
        $this->RegisterPropertyString("header_margin_unitType", "em");

        //footer
        $this->RegisterPropertyBoolean("footer_display", True);
        $this->RegisterPropertyString("footer_Toolbar", "[]");
        $this->RegisterPropertyInteger("footer_backgroundColor", -1);
        $this->RegisterPropertyFloat("footer_backgroundColor_Alpha", 1.0);
        $this->RegisterPropertyFloat("footer_margin", 1.5);
        $this->RegisterPropertyString("footer_margin_unitType", "em");

        //table
        $this->RegisterPropertyInteger("table_today_backgroundColor", 16768040);
        $this->RegisterPropertyFloat("table_today_backgroundColor_Alpha", 0.15);

        $this->RegisterPropertyInteger("table_backgroundColor", -1);
        $this->RegisterPropertyFloat("table_backgroundColor_Alpha", 0.0);
        $this->RegisterPropertyFloat("table_fontSize", 1.0);
        $this->RegisterPropertyString("table_fontSize_unitType", "em");
        $this->RegisterPropertyInteger("table_fontColor", 0);
        $this->RegisterPropertyString("table_fontFamily", "");

        $this->RegisterPropertyInteger("table_header_backgroundColor", -1);
        $this->RegisterPropertyFloat("table_header_backgroundColor_Alpha", 0.0);
        $this->RegisterPropertyFloat("table_header_fontSize", 1.0);
        $this->RegisterPropertyString("table_header_fontSize_unitType", "em");
        $this->RegisterPropertyInteger("table_header_fontColor", 0);
        $this->RegisterPropertyString("table_header_fontFamily", "");

        $this->RegisterPropertyString("initialView", "dayGridMonth");
        $this->RegisterPropertyString("dataEvents", "[]");

    }
    public function ApplyChanges() {
        //Never delete this line!
        parent::ApplyChanges();

        $this->SetBuffer("OutputCSS", "");

        $this->RegisterVariableString("Content", $this->Translate("Content"), "", 0);
        
    }

    public function GetConfigurationForm() {
        $ical_instances = IPS_GetInstanceListByModuleID("{5127CDDC-2859-4223-A870-4D26AC83622C}");
        if(count($ical_instances) === 0) return '{ "actions": [ { "type": "Label", "label": "'.$this->Translate("No ModulInstances iCal Calendar Reader found!").'" } ] }';

        //update Items for InstanceSelectList!
        $formData = $this->LoadConfigurationForm();
        $key = 0;
        foreach ($formData["elements"] as $keyNr => $element) {
            if (array_key_exists("name", $element) && $element["name"] === "dataEvents") {
                $key = $keyNr;
                break;
            }
        }

        $colData = $formData["elements"][$key]["columns"];
        $colkey = array_search("moduleInstance", array_column($colData, 'name'));

        $options_arr = array();
        foreach ($ical_instances as $instanceID){
            $options_arr[] = array("value" => $instanceID, "caption" => $instanceID. " => " . IPS_GetObject($instanceID)["ObjectName"]);
        }

        $formData["elements"][$key]["columns"][$colkey]["add"] = $options_arr[0]["value"];
        $formData["elements"][$key]["columns"][$colkey]["edit"]["options"] = $options_arr;

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
    private function GenerateCSS(){
        if(!empty($this->ReadPropertyString("CustomCSS"))){
            $css_String = base64_decode($this->ReadPropertyString("CustomCSS"));
        }else {
            $css_String = file_get_contents(realpath(__DIR__ . "/../SymconJSLive/js/fullcalendar/PLACEHOLDER-main.css"));

            $pattern = '/\#([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)#/';
            preg_match_all($pattern, $css_String, $matches);

            $config = json_decode(IPS_GetConfiguration($this->InstanceID), true);

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

                            $css_String = str_replace($var, $val, $css_String);
                        }
                    }
                }
            }
        }

        return $css_String;
    }

    private function GetDataEvents(){
        $output = array();
        $data = json_decode($this->ReadPropertyString("dataEvents"), true);

        foreach($data as $item){
            $s_output = array();
            $s_output["Name"] = $item["Name"];
            $s_output["Type"] = $item["Type"];

            if($item["Color"] >= 0) {
                $rgbdata = $this->HexToRGB($item["Color"]);
                $s_output["color"] = "rgba(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ", " . number_format($item["Color_Alpha"], 2, '.', '') . ")";
            }

            if($item["textColor"] >= 0) {
                $rgbdata = $this->HexToRGB($item["textColor"]);
                $s_output["textColor"] = "rgba(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ")";
            }

            $s_output["extraParams"] = array("Instance" => $this->InstanceID, "id" => $item["moduleInstance"]);

            $output[] = $s_output;
        }

        return $output;
    }

    private function ReplacePlaceholder(string $htmlData){
        //configuration Data
        $htmlData = str_replace("{CONFIG}", $this->json_encode_advanced($this->GetConfigurationData()), $htmlData);

        //DATAEVENTS
        $htmlData = str_replace("{DATAEVENTS}",  $this->json_encode_advanced($this->GetDataEvents()), $htmlData);

        //Load Fonts
        $htmlData = str_replace("{FONTS}", $this->LoadFonts(), $htmlData);

        return $htmlData;
    }
    private function GetConfigurationData(){
        $output = json_decode(IPS_GetConfiguration($this->InstanceID), true);
        $output["InstanceID"] = $this->InstanceID;

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

        //remove Dataset
        unset($output["dataEvents"]);

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