<?php
include_once (__DIR__ . '/../SymconJSLive/libs/JSLiveModule.php');

class SymconJSLiveDoughnutPie extends JSLiveModule{
    public function Create() {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent("{9FFF3FC0-FD51-C289-FA36-BC1C370946CF}");

        $this->RegisterPropertyString("type", "doughnut");

        //Expert
        $this->RegisterPropertyBoolean("Debug", false);
        $this->RegisterPropertyBoolean("EnableCache", true);
        $this->RegisterPropertyBoolean("CreateOutput", true);
        $this->RegisterPropertyInteger("TemplateScriptID", 0);
        $this->RegisterPropertyBoolean("EnableViewport", true);
        $this->RegisterPropertyInteger("IFrameHeight", 0);
        $this->RegisterPropertyInteger("Ratio", 0);

        //title
        $this->RegisterPropertyString("title_text", "");
        $this->RegisterPropertyBoolean("title_display", false);
        $this->RegisterPropertyString("title_position", "top");
        $this->RegisterPropertyInteger("title_fontSize", 12);
        $this->RegisterPropertyInteger("title_fontColor", 0);
        $this->RegisterPropertyString("title_fontFamily", "");

        //Legend
        $this->RegisterPropertyBoolean("legend_display", true);
        $this->RegisterPropertyString("legend_position", "top");
        $this->RegisterPropertyString("legend_align", "center");
        $this->RegisterPropertyInteger("legend_fontSize", 12);
        $this->RegisterPropertyInteger("legend_fontColor", 0);
        $this->RegisterPropertyString("legend_fontFamily", "");
        $this->RegisterPropertyInteger("legend_boxWidth", 40);

        //Tooltips
        $this->RegisterPropertyBoolean("tooltips_enabled", true);
        $this->RegisterPropertyString("tooltips_position", "average");
        $this->RegisterPropertyString("tooltips_mode", "index");
        $this->RegisterPropertyInteger("tooltips_fontSize", 12);
        $this->RegisterPropertyInteger("tooltips_fontColor", 65535);
        $this->RegisterPropertyString("tooltips_fontFamily", "");
        $this->RegisterPropertyInteger("tooltips_backgroundColor", 0);
        $this->RegisterPropertyInteger("tooltips_cornerRadius", 5);

        //Raotation
        $this->RegisterPropertyInteger("rotation_start", 180);
        $this->RegisterPropertyInteger("rotation_length", 360);

        //Animation
        $this->RegisterPropertyInteger("animation_duration", 500);
        $this->RegisterPropertyString("animation_easing", "linear");

        //Datalabels
        $this->RegisterPropertyString("datalabels_anchoring", "center");
        $this->RegisterPropertyString("datalabels_align", "center");
        $this->RegisterPropertyInteger("datalabels_fontSize", 12);
        $this->RegisterPropertyInteger("datalabels_fontColor", 0);
        $this->RegisterPropertyString("datalabels_fontFamily", "");
        $this->RegisterPropertyInteger("datalabels_borderWidth", 1);
        $this->RegisterPropertyInteger("datalabels_borderRadius", 2);

        //data
        $this->RegisterPropertyInteger("data_precision", 2);

        //dataset
        $this->RegisterPropertyString("Datasets", "[]");
    }
    public function ApplyChanges() {
        //Never delete this line!
        parent::ApplyChanges();
    }

    public function ReceiveData($JSONString) {
        parent::ReceiveData($JSONString);
        $jsonData = json_decode($JSONString, true);
        $buffer = json_decode($jsonData['Buffer'], true);
        if($buffer["instance"] == 0) return;

        switch($buffer['cmd']) {
            case "exportConfiguration":
                return $this->ExportConfiguration();
            case "getContend":
                return $this->GetOutput();
            case "getUpdate":
                return $this->GetUpdate();
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
            $scriptData = file_get_contents (__DIR__ ."/../SymconJSLive/templates/Doughnut-PIE.html");
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
    public function GetUpdate(){
        $updateData = array();

        $updateData["DATASETS"] = $this->GenerateDataSet();
        $updateData["CONFIG"] = $this->GetConfigurationData();

        return json_encode($updateData);
    }
    private function GetData(array $querydata){
        $output = array();
        $load_vars = array();
        $registered_vars = array();
        $datasets = json_decode($this->ReadPropertyString("Datasets"), true);

        //load all variables
        foreach ($datasets as $item){
            foreach ($item["Variables"] as $vars){
                if(!in_array($vars["Variable"], $load_vars)){
                    $registered_vars[] = $vars["Variable"];
                }
            }
        }

        if(!array_key_exists("var", $querydata)) {
            //$this->SendDebug("GetData", "PARAMETER VARIABLE NOT SET!(" . json_encode($querydata). ")", 0);
            $load_vars = $registered_vars;
        }else{
            $load_vars[] = $querydata["var"];
        }


        foreach($load_vars as $var){
            $o_item = array();
            $o_item["Variable"] = $var;
            if(!IPS_VariableExists($var)){
                $this->SendDebug("GetData", "VARIABLE NOT EXIST!", 0);
                continue;
            }


            $key = array_search($var, array_column($registered_vars, 'Variable'));
            if(!in_array($var, $registered_vars)){
                $this->SendDebug("GetData", "VARIABLE NOT IN INSTANCE!", 0);
                continue;
            }

            $o_item["Value"] = round(GetValue($var), $this->ReadPropertyInteger("data_precision"));

            if($this->ReadPropertyBoolean("Debug"))
                $this->SendDebug("GetData", json_encode($querydata), 0);

            $output[] = $o_item;
        }

        return json_encode($output);
    }

    private function ReplacePlaceholder(string $htmlData){
        $htmlData = str_replace("{TITLE_TEXT}", $this->ReadPropertyString("title_text"), $htmlData);

        //Title
        $htmlData = str_replace("{TITLE}", $this->json_encode_advanced($this->GenerateTitleData()), $htmlData);

        //data
        $htmlData = str_replace("{DATA}", $this->json_encode_advanced($this->GenerateDataSet()), $htmlData);

        //Legend
        $htmlData = str_replace("{LEGEND}", $this->json_encode_advanced($this->GenerateLegendData()), $htmlData);

        //Tooltipdata
        $htmlData = str_replace("{TOOLTIPS}", $this->json_encode_advanced($this->GenerateTooltipData()), $htmlData);

        //configuration Data
        $htmlData = str_replace("{CONFIG}", $this->json_encode_advanced($this->GetConfigurationData()), $htmlData);

        //Load Fonts
        $arr = array($this->ReadPropertyString("title_fontFamily") ,$this->ReadPropertyString("legend_fontFamily"), $this->ReadPropertyString("tooltips_fontFamily"), $this->ReadPropertyString("datalabels_fontFamily"));
        $htmlData = str_replace("{FONTS}", $this->LoadFonts($arr), $htmlData);

        return $htmlData;
    }

    private function GenerateTitleData(){
        $output = array();
        $output["text"] = $this->ReadPropertyString("title_text");
        $output["position"] = $this->ReadPropertyString("title_position");
        $output["font"]["size"] = $this->ReadPropertyInteger("title_fontSize");
        $output["font"]["family"] = $this->ReadPropertyString("title_fontFamily");

        $output["display"] = $this->ReadPropertyBoolean("title_display");

        if($this->ReadPropertyInteger("title_fontColor") >= 0) {
            $rgbdata = $this->HexToRGB($this->ReadPropertyInteger("title_fontColor"));
            $output["color"] = "rgb(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ")";
        }

        return $output;
    }
    private function GenerateTooltipData(){
        $output = array();
        $output["enabled"] = $this->ReadPropertyBoolean("tooltips_enabled");
        $output["position"] = $this->ReadPropertyString("tooltips_position");
        $output["mode"] = $this->ReadPropertyString("tooltips_mode");

        $output["titleFontSize"] = $this->ReadPropertyInteger("tooltips_fontSize");
        $output["bodyFontSize"] = $this->ReadPropertyInteger("tooltips_fontSize");
        $output["footerFontSize"] = $this->ReadPropertyInteger("tooltips_fontSize");

        $output["titleFontFamily"] = $this->ReadPropertyString("tooltips_fontFamily");
        $output["bodyFontFamily"] = $this->ReadPropertyString("tooltips_fontFamily");
        $output["footerFontFamily"] = $this->ReadPropertyString("tooltips_fontFamily");


        $output["cornerRadius"] = $this->ReadPropertyInteger("tooltips_cornerRadius");

        if($this->ReadPropertyInteger("tooltips_fontColor") >= 0){
            $rgbdata = $this->HexToRGB($this->ReadPropertyInteger("tooltips_fontColor"));
            $output["titleFontColor"] = "rgb(" . $rgbdata["R"] .", " . $rgbdata["G"] .", " . $rgbdata["B"]. ")";
            $output["bodyFontColor"] = "rgb(" . $rgbdata["R"] .", " . $rgbdata["G"] .", " . $rgbdata["B"]. ")";
            $output["footerFontColor"] = "rgb(" . $rgbdata["R"] .", " . $rgbdata["G"] .", " . $rgbdata["B"]. ")";
        }
        if($this->ReadPropertyInteger("tooltips_backgroundColor") >= 0) {
            $rgbdata = $this->HexToRGB($this->ReadPropertyInteger("tooltips_backgroundColor"));
            $output["backgroundColor"] = "rgb(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ")";
        }

        return $output;

    }
    private function GenerateLegendData(){
        $output = array();
        $output["display"] = $this->ReadPropertyBoolean("legend_display");
        $output["position"] = $this->ReadPropertyString("legend_position");
        $output["align"] = $this->ReadPropertyString("legend_align");

        if($this->ReadPropertyInteger("legend_fontColor") >= 0){
            $rgbdata = $this->HexToRGB($this->ReadPropertyInteger("legend_fontColor"));
            $output["labels"]["color"] = "rgb(" . $rgbdata["R"] .", " . $rgbdata["G"] .", " . $rgbdata["B"]. ")";
        }
        $output["labels"]["font"]["size"] = $this->ReadPropertyInteger("legend_fontSize");
        $output["labels"]["boxWidth"] = $this->ReadPropertyInteger("legend_boxWidth");
        $output["labels"]["font"]["family"] = $this->ReadPropertyString("legend_fontFamily");

        return $output;

    }
    private function GenerateDataSet(){
        $archiveControlID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
        $output["datasets"] = array();
        $output["labels"] = array();
        $output["suffix"] = array();
        $output["prefix"] = array();
        $i = 0;
        $datasets = json_decode($this->ReadPropertyString("Datasets"),true);

        //sortieren aufsteigend nach order
        $arr_order = array_column($datasets, 'Order');
        array_multisort($arr_order, SORT_ASC , $datasets);


        if(!is_array($datasets)){
            $this->SendDebug("GenerateDataSet", "No Datasets set!", 0);
            return "{}";
        }
        foreach($datasets as $item){
            if(count($item["Variables"]) == 0){
                $this->SendDebug("GenerateDataSet", "No Variables set! Jump to next Dataset!", 0);
                continue;
            }

            $singelOutput = array();
            $singelOutput["order"] = $item["Order"];

            //beim richtigen index starten
            for($n = 0; $n < $i; $n++){
                $singelOutput["variables"][] = null;
                $singelOutput["data"][] = null;
                $singelOutput["borderWidth"][] = null;
                $singelOutput["backgroundColor"][] = null;
                $singelOutput["borderColor"][] = null;
            }

            //Variablen sortieren aufsteigend nach order
            $arr_order = array_column($item["Variables"], 'Order');
            array_multisort($arr_order, SORT_ASC , $item["Variables"]);

            foreach($item["Variables"] as $varitem){
                if(!IPS_VariableExists($varitem["Variable"])) {
                    $this->SendDebug("GenerateDataSet", "VARIABLE " .$item["Variable"] . " NOT EXIST!", 0);
                    continue;
                }

                if(in_array($varitem["Label"], $output["labels"])){
                    $key = array_search($varitem["Label"], $output["labels"]);
                    $singelOutput["variables"][$key] = $varitem["Variable"];
                    $singelOutput["data"][$key] = round(GetValue($varitem["Variable"]), $this->ReadPropertyInteger("data_precision"));

                    $rgbdata = $this->HexToRGB($varitem["BackgroundColor"]);
                    $singelOutput["backgroundColor"][$key] = "rgba(" . $rgbdata["R"] .", " . $rgbdata["G"] .", " . $rgbdata["B"].", " . number_format($varitem["BackgroundColor_Alpha"], 2, '.', '').")";

                    $rgbdata = $this->HexToRGB($varitem["BorderColor"]);
                    $singelOutput["borderColor"][] = "rgba(" . $rgbdata["R"] .", " . $rgbdata["G"] .", " . $rgbdata["B"].", " . number_format($varitem["BorderColor_Alpha"], 2, '.', '').")";


                    if(IPS_VariableProfileExists($varitem["Profile"])) {
                        $profilData = IPS_GetVariableProfile($varitem["Profile"]);
                        $output["prefix"][] = $profilData["Prefix"];
                        $output["suffix"][] = $profilData["Suffix"];
                    }
                }else{
                    if(empty($varitem["Label"])){
                        //Load Variablen Name wenn label leer ist
                        $output["labels"][] = IPS_GetVariableIDByName($varitem["Variable"]);
                    }else{
                        $output["labels"][] = $varitem["Label"];
                    }

                    $singelOutput["variables"][] = $varitem["Variable"];
                    $singelOutput["data"][] = round(GetValue($varitem["Variable"]), $this->ReadPropertyInteger("data_precision"));

                    $rgbdata = $this->HexToRGB($varitem["BackgroundColor"]);
                    $singelOutput["backgroundColor"][] = "rgba(" . $rgbdata["R"] .", " . $rgbdata["G"] .", " . $rgbdata["B"].", " . number_format($varitem["BackgroundColor_Alpha"], 2, '.', '').")";

                    $rgbdata = $this->HexToRGB($varitem["BorderColor"]);
                    $singelOutput["borderColor"][] = "rgba(" . $rgbdata["R"] .", " . $rgbdata["G"] .", " . $rgbdata["B"].", " . number_format($varitem["BorderColor_Alpha"], 2, '.', '').")";

                    $singelOutput["borderWidth"][] = $varitem["BorderWidth"];
                    if(IPS_VariableProfileExists($varitem["Profile"])) {
                        $profilData = IPS_GetVariableProfile($varitem["Profile"]);
                        $output["prefix"][] = $profilData["Prefix"];
                        $output["suffix"][] = $profilData["Suffix"];
                    }
                    $i++;
                }
            }

            //datalabels
            if($item["datalabels_enable"]){
                $datalabels = array();
                $datalabels["display"] = true;

                if($item["datalabels_BackgroundColor"] >= 0){
                    $datalabels["useBackgroundColor"] = false;
                    $rgbdata = $this->HexToRGB($item["datalabels_BackgroundColor"]);
                    $datalabels["BackgroundColor"] = "rgba(" . $rgbdata["R"] .", " . $rgbdata["G"] .", " . $rgbdata["B"].", " . number_format($item["datalabels_BackgroundColor_Alpha"], 2, '.', '').")";

                }else{
                    $datalabels["useBackgroundColor"] = true;
                }

                if($item["datalabels_BorderColor"] >= 0) {
                    $datalabels["useBorderColor"] = false;
                    $rgbdata = $this->HexToRGB($item["datalabels_BorderColor"]);
                    $datalabels["BorderColor"] = "rgba(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ", " . number_format($item["datalabels_BorderColor_Alpha"], 2, '.', '') . ")";
                }else{
                    $datalabels["useBorderColor"] = true;
                }

                if($item["datalabels_BorderColor"] >= 0) {
                    $datalabels["useBorderColor"] = false;
                    $rgbdata = $this->HexToRGB($item["datalabels_BorderColor"]);
                    $datalabels["BorderColor"] = "rgba(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ", " . number_format($item["datalabels_BorderColor_Alpha"], 2, '.', '') . ")";
                }else{
                    $datalabels["useBorderColor"] = true;
                }

                if($item["datalabels_FontColor"] >= 0) {
                    $rgbdata = $this->HexToRGB($item["datalabels_FontColor"]);
                    $datalabels["color"] = "rgb(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ")";
                }else{
                    $rgbdata = $this->HexToRGB($this->ReadPropertyInteger("datalabels_fontColor"));
                    $datalabels["color"] = "rgb(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ")";
                }

                if(!empty($item["datalabels_anchoring"])){
                    $datalabels["anchor"] = $item["datalabels_anchoring"];
                }

                $datalabels["showPrefix"] = $item["datalabels_showPrefix"];
                $datalabels["showSuffix"] = $item["datalabels_showSuffix"];

                //"visible": false,

                $singelOutput["datalabels"] = $datalabels;
            }

            $output["datasets"][] = $singelOutput;
        }

        //datasets auffüllen mit 0
        $start = 0;
        $c = 0;
        $end = 0;
        foreach($output["datasets"] as $key => $val){
            for($n = count($val["variables"]); $n < $i; $n++){
                if($c >= count($output["datasets"])){
                    //Reslichen überspringen
                    continue;
                }

                if($end <= $start){
                    $start = count($output["datasets"][$c]["variables"]);
                    $c++;

                    if((count($output["datasets"])-1) >= $c){
                        //$this->SendDebug("VAR", json_encode($output["datasets"][$c]["variables"]), 0);
                        $end = count($output["datasets"][$c]["variables"]) - 1;
                    }else{
                        $end = $start;
                    }
                }else{
                    $start++;
                }
                if($c >= count($output["datasets"])){
                    //Reslichen überspringen
                    continue;
                }

                $output["datasets"][$key]["variables"][] = null;
                $output["datasets"][$key]["data"][] = null;

                //$this->SendDebug("Count", "C=".$c ." | Start=" . $start . " | END=". $end, 0);
                //$this->SendDebug("Test", json_encode($output["datasets"][$c]["backgroundColor"]), 0);
                //$this->SendDebug("Test", $output["datasets"][$c]["backgroundColor"][$start], 0);

                $output["datasets"][$key]["borderWidth"][] = $output["datasets"][$c]["borderWidth"][$start];
                $output["datasets"][$key]["backgroundColor"][] = $output["datasets"][$c]["backgroundColor"][$start];
                $output["datasets"][$key]["borderColor"][] = $output["datasets"][$c]["borderColor"][$start];
            }
        }
        return $output;
    }

    private function GetConfigurationData(){
        $output = json_decode(IPS_GetConfiguration($this->InstanceID), true);

        $output["InstanceID"] = $this->InstanceID;

        //alle colorvariablen umwandeln!
        foreach($output as $key => $val){
            $pos = strpos($key, "Color");
            if ($pos !== false) {
                $rgbdata = $this->HexToRGB($val);
                $output[$key] = "rgb(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ")";
            }
        }

        //remove Dataset
        unset($output["Datasets"]);

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