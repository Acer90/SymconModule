<?php
include_once (__DIR__ . '/../SymconJSLive/libs/JSLiveModule.php');

class SymconJSLiveGauge extends JSLiveModule{
    public function Create() {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent("{9FFF3FC0-FD51-C289-FA36-BC1C370946CF}");

        $this->RegisterPropertyInteger("Variable", 0);
        $this->RegisterPropertyFloat("min", 0);
        $this->RegisterPropertyFloat("max", 1000);
        $this->RegisterPropertyInteger("precision", 0);
        $this->RegisterPropertyString("template", "CanvasGauges-Radial");

        //Expert
        $this->RegisterPropertyBoolean("Debug", false);
        $this->RegisterPropertyBoolean("EnableCache", true);
        $this->RegisterPropertyBoolean("CreateOutput", true);
        $this->RegisterPropertyBoolean("CreateIPSView", true);
        $this->RegisterPropertyInteger("TemplateScriptID", 0);
        $this->RegisterPropertyBoolean("EnableViewport", true);
        $this->RegisterPropertyInteger("IFrameHeight", 0);
        $this->RegisterPropertyInteger("overrideWidth", 0);
        $this->RegisterPropertyInteger("overrideHeight", 0);

        //title
        $this->RegisterPropertyString("title_text", "");
        $this->RegisterPropertyBoolean("title_display", false);
        $this->RegisterPropertyInteger("title_fontSize", 20);
        $this->RegisterPropertyInteger("title_fontColor", 0);
        $this->RegisterPropertyString("title_fontFamily", "");

        //Plate
        $this->RegisterPropertyBoolean("plate_display", false);
        $this->RegisterPropertyString("plate_unit", "");
        $this->RegisterPropertyInteger("plate_colorPlate", 16777215);
        $this->RegisterPropertyFloat("plate_colorPlate_Alpha", 1);
        $this->RegisterPropertyInteger("plate_colorPlateEnd", 16777215);
        $this->RegisterPropertyFloat("plate_colorPlateEnd_Alpha", 1);

        //Needle
        $this->RegisterPropertyBoolean("needle_display", false);
        $this->RegisterPropertyString("needle_Type", "arrow"); //"line"
        $this->RegisterPropertyInteger("needle_start", 0);
        $this->RegisterPropertyInteger("needle_end", 80);
        $this->RegisterPropertyInteger("needle_width", 2);
        $this->RegisterPropertyInteger("needle_colorNeedle", 0);
        $this->RegisterPropertyFloat("needle_colorNeedle_Alpha", 1);
        $this->RegisterPropertyInteger("needle_colorNeedleEnd", 0);
        $this->RegisterPropertyFloat("needle_colorNeedleEnd_Alpha", 1);

        //Valuebox
        $this->RegisterPropertyBoolean("valuebox_display", false);
        $this->RegisterPropertyBoolean("valuebox_separator", false);
        $this->RegisterPropertyInteger("valuebox_fontSize", 14);
        $this->RegisterPropertyInteger("valuebox_fontColor", 0);
        $this->RegisterPropertyInteger("valuebox_colorValueBoxBackground", 0);
        $this->RegisterPropertyFloat("valuebox_colorValueBoxBackground_Alpha", 1);
        $this->RegisterPropertyString("valuebox_fontFamily", "");

        //Progressbar
        $this->RegisterPropertyBoolean("progressbar_display", false);
        $this->RegisterPropertyInteger("progressbar_barWidth", 5);
        $this->RegisterPropertyInteger("progressbar_barShadow", 1);
        $this->RegisterPropertyInteger("progressbar_colorBar", 0);
        $this->RegisterPropertyFloat("progressbar_colorBar_Alpha", 1);
        $this->RegisterPropertyInteger("progressbar_colorBarProgress", 0);
        $this->RegisterPropertyFloat("progressbar_colorBarProgress_Alpha", 1);

        //Ticks
        $this->RegisterPropertyString("ticks_fontFamily", "");
        $this->RegisterPropertyInteger("ticks_fontSize", 12);
        $this->RegisterPropertyBoolean("ticks_strokeTicks", false);
        $this->RegisterPropertyInteger("ticks_highlightsWidth", 5);
        $this->RegisterPropertyInteger("ticks_minorTicks", 5);
        $this->RegisterPropertyInteger("ticks_colorMajorTick", 0);
        $this->RegisterPropertyInteger("ticks_colorMinorTicks", 0);
        $this->RegisterPropertyInteger("ticks_colorUnits", 0);
        $this->RegisterPropertyInteger("ticks_colorNumbers", 0);
        $this->RegisterPropertyBoolean("ticks_exactTicks", false);

        //animation
        $this->RegisterPropertyString("animation_rule", "linear");
        $this->RegisterPropertyInteger("animation_duration", 500);

        $this->RegisterPropertyString("Ticks", "[]");
        $this->RegisterPropertyString("Highlights", "[]");

        //Radialgauge settings
        $this->RegisterPropertyInteger("radial_startAngle", 60);
        $this->RegisterPropertyInteger("radial_ticksAngle", 270);

        //linearsettings
        $this->RegisterPropertyString("linear_tickSide", "both");
        $this->RegisterPropertyString("linear_numberSide", "both");
        $this->RegisterPropertyString("linear_needleSide", "both");
    }
    public function ApplyChanges() {
        //Never delete this line!
        parent::ApplyChanges();
    }

    public function ReceiveData($JSONString) {
        parent::ReceiveData($JSONString);
        $jsonData = json_decode($JSONString, true);
        $buffer = json_decode($jsonData['Buffer'], true);


        //if($buffer["instance"] != $this->InstanceID) return;
        //$this->SendDebug("ReceiveData", $jsonData['Buffer']. " =>" . $this->InstanceID, 0);

        switch($buffer['cmd']) {
            case "exportConfiguration":
                return $this->ExportConfiguration();
            case "getContend":
                return $this->GetOutput();
            case "getData":
                return $this->GetData($buffer['queryData']);
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
            $scriptData = file_get_contents (__DIR__ ."/../SymconJSLive/templates/".$this->ReadPropertyString("template").".html");
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
    public function GetData(array $querydata){
        $output = array();

        $output["Variable"] = $this->ReadPropertyInteger("Variable");
        if(!IPS_VariableExists($output["Variable"])){
            $this->SendDebug("GetData", "VARIABLE NOT EXIST!", 0);
            return "VARIABLE NOT EXIST!";
        }

        $output["Value"] = GetValue($output["Variable"]);

        return json_encode($output);
    }

    private function ReplacePlaceholder(string $htmlData){
        $htmlData = str_replace("{TITLE_TEXT}", $this->ReadPropertyString("title_text"), $htmlData);

        //configuration Data
        $htmlData = str_replace("{CONFIG}", $this->json_encode_advanced($this->GetConfigurationData()), $htmlData);

        //ticks
        $htmlData = str_replace("{TICKS}", $this->json_encode_advanced($this->GenerateTicks()), $htmlData);

        //tHighlights
        $htmlData = str_replace("{HIGHLIGHTS}", $this->json_encode_advanced($this->GenerateHighlights()), $htmlData);

        //VALUE
        $val = 0;
        if(IPS_VariableExists($this->ReadPropertyInteger("Variable"))){
            $val = GetValue($this->ReadPropertyInteger("Variable"));
        }else{
            $this->SendDebug("ReplacePlaceholder", "Variable (".$this->ReadPropertyInteger("Variable").") NOT EXIST!", 0);
        }
        $htmlData = str_replace("{VALUE}", number_format($val, $this->ReadPropertyInteger("precision"), '.', ''), $htmlData);

        //Load Fonts
        $arr = array($this->ReadPropertyString("valuebox_fontFamily"), $this->ReadPropertyString("ticks_fontFamily"), $this->ReadPropertyString("title_fontFamily"));
        $htmlData = str_replace("{FONTS}", $this->LoadFonts($arr), $htmlData);

        return $htmlData;
    }

    private function GenerateTicks(){
        $ticks = json_decode($this->ReadPropertyString("Ticks"), true);
        $min = $this->ReadPropertyFloat("min");
        $max = $this->ReadPropertyFloat("max");
        array_multisort(array_column($ticks, 'Value'), SORT_ASC, $ticks);

        $majorticks = array();
        $majorticks[] =  number_format($min, $this->ReadPropertyInteger("precision"), '.', '');
        foreach ($ticks as $tick) {
            $majorticks[] = number_format($tick["Value"], $this->ReadPropertyInteger("precision"), '.', '');
        }
        $majorticks[] =  number_format($max, $this->ReadPropertyInteger("precision"), '.', '');

        //$this->SendDebug("TEST", print_r($majorticks, true), 0);
        return $majorticks;
    }

    private function GenerateHighlights(){
        $arr = json_decode($this->ReadPropertyString("Highlights"), true);
        $highlights = array();
        array_multisort(array_column($arr, 'From'), SORT_ASC, $arr);

        foreach ($arr as $item){
            $highlight_item = array();
            $highlight_item["from"] =  number_format($item["From"], $this->ReadPropertyInteger("precision"), '.', '');
            $highlight_item["to"] =  number_format($item["To"], $this->ReadPropertyInteger("precision"), '.', '');

            $rgbdata = $this->HexToRGB($item["HighlightColor"]);
            $highlight_item["color"] = "rgba(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ", " . number_format($item["HighlightColor_Alpha"], $this->ReadPropertyInteger("precision"), '.', '') . ")";

            $highlights[] = $highlight_item;
        }
        return $highlights;
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
                    $output[$key] = "rgba(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ", ".number_format($output[$key."_Alpha"], 2, '.', '') .")";
                }else{
                    $rgbdata = $this->HexToRGB($val);
                    $output[$key] = "rgb(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ")";
                }
            }
        }

        if(!$this->ReadPropertyBoolean("plate_display")){
            $output["plate_colorPlate"] = "rgba(0, 0, 0, 0)";
            $output["plate_colorPlateEnd"] = "rgba(0, 0, 0, 0)";
        }

        //check if fonts is sets
        $arr = array($this->ReadPropertyString("valuebox_fontFamily"), $this->ReadPropertyString("ticks_fontFamily"), $this->ReadPropertyString("title_fontFamily"));
        $output["font_isSet"] = false;
        foreach ($arr as $item){
            if(!empty($item)) $output["font_isSet"] = true;
        }

        unset($output["Ticks"]);
        unset($output["Highlights"]);
        return $output;
    }

    public function LoadOtherConfiguration(int $id){
        if(!IPS_ObjectExists($id)) return "Instance/Chart not found!";

        if(IPS_GetObject($id)["ObjectType"] == 1){
            //instance
            $intData = IPS_GetInstance($id);
            if($intData["ModuleInfo"]["ModuleID"] != IPS_GetInstance($this->InstanceID)["ModuleInfo"]["ModuleID"]) return "Only Allowed at the same Modul!";

            $confData = json_decode(IPS_GetConfiguration($id), true);

            //bestimmte aktuelle einstellungen beibehalten
            $confData["title_text"] = $this->ReadPropertyString("title_text");
            $confData["Datasets"]= $this->ReadPropertyString("Datasets");

            IPS_SetConfiguration($this->InstanceID, json_encode($confData));
            IPS_ApplyChanges($this->InstanceID);
        }else return "A Instance must be selected!";
    }
}

?>