<?php
include_once (__DIR__ . '/../SymconJSLive/libs/JSLiveModule.php');

class SymconJSLiveRadarChart extends JSLiveModule{
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
        $this->RegisterPropertyBoolean("customScale_mode", false);
        $this->RegisterPropertyString("customScale", "[]");
        $this->RegisterPropertyBoolean("EnableViewport", true);
        $this->RegisterPropertyInteger("IFrameHeight", 0);
        $this->RegisterPropertyInteger("overrideWidth", 0);
        $this->RegisterPropertyInteger("overrideHeight", 0);
        $this->RegisterPropertyInteger("Ratio", 0);

        //title
        $this->RegisterPropertyString("title_text", "");
        $this->RegisterPropertyBoolean("title_display", false);
        $this->RegisterPropertyString("title_position", "top");
        $this->RegisterPropertyInteger("title_fontSize", 12);
        $this->RegisterPropertyInteger("title_fontColor", 0);
        $this->RegisterPropertyString("title_fontFamily", "");

        //Axes
        $this->RegisterPropertyBoolean("axes_display", true);

        $this->RegisterPropertyBoolean("axes_gridLines_display", true);
        $this->RegisterPropertyBoolean("axes_gridLines_drawTicks", true);
        $this->RegisterPropertyInteger("axes_gridLines_color", 0);
        $this->RegisterPropertyInteger("axes_gridLines_lineWidth", 1);

        $this->RegisterPropertyBoolean("axes_angleLines_display", true);
        $this->RegisterPropertyInteger("axes_angleLines_lineWidth", 1);
        $this->RegisterPropertyInteger("axes_angleLines_color", 0);

        $this->RegisterPropertyBoolean("axes_pointLabels_display", true);
        $this->RegisterPropertyInteger("axes_pointLabels_color", 0);
        $this->RegisterPropertyInteger("axes_pointLabels_fontSize", 12);
        $this->RegisterPropertyString("axes_pointLabels_fontFamily", "");

        $this->RegisterPropertyInteger("axes_ticks_fontColor", 0);
        $this->RegisterPropertyInteger("axes_ticks_fontSize", 12);
        $this->RegisterPropertyString("axes_ticks_fontFamily", "");
        $this->RegisterPropertyInteger("axes_ticks_backdropColor", 16777215);
        $this->RegisterPropertyFloat("axes_ticks_backdropColor_Alpha", 0);

        //Points
        $this->RegisterPropertyInteger("point_radius", 0);
        $this->RegisterPropertyInteger("point_hoverRadius", 15);
        $this->RegisterPropertyString("points_Style", "circle");

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
        $this->RegisterPropertyInteger("tooltips_fontColor", 16777215);
        $this->RegisterPropertyString("tooltips_fontFamily", "");
        $this->RegisterPropertyInteger("tooltips_backgroundColor", 0);
        $this->RegisterPropertyInteger("tooltips_cornerRadius", 5);

        //Animation
        $this->RegisterPropertyInteger("animation_duration", 500);
        $this->RegisterPropertyString("animation_easing", "linear");

        //Data
        $this->RegisterPropertyInteger("data_minValue", 0);
        $this->RegisterPropertyInteger("data_maxValue", 360);
        $this->RegisterPropertyInteger("data_sections", 16);
        $this->RegisterPropertyInteger("data_precision", 0);
        $this->RegisterPropertyBoolean("data_loadAsync", true);

        //Datalabels
        $this->RegisterPropertyString("datalabels_anchoring", "center");
        $this->RegisterPropertyString("datalabels_align", "center");
        $this->RegisterPropertyInteger("datalabels_fontSize", 12);
        $this->RegisterPropertyInteger("datalabels_fontColor", 0);
        $this->RegisterPropertyString("datalabels_fontFamily", "");
        $this->RegisterPropertyInteger("datalabels_borderWidth", 1);
        $this->RegisterPropertyInteger("datalabels_borderRadius", 2);

        //dataset
        $this->RegisterPropertyString("Datasets", "[]");
    }
    public function ApplyChanges() {
        //Never delete this line!
        parent::ApplyChanges();

        if (!IPS_VariableProfileExists("JSLive_Periode2")){
            IPS_CreateVariableProfile("JSLive_Periode2", 1);
            IPS_SetVariableProfileAssociation("JSLive_Periode2", 0, $this->Translate("Decade"), "", -1);
            IPS_SetVariableProfileAssociation("JSLive_Periode2", 1, $this->Translate("Year"), "", -1);
            IPS_SetVariableProfileAssociation("JSLive_Periode2", 2, $this->Translate("Quarter"), "", -1);
            IPS_SetVariableProfileAssociation("JSLive_Periode2", 3, $this->Translate("Month"), "", -1);
            IPS_SetVariableProfileAssociation("JSLive_Periode2", 4, $this->Translate("Week"), "", -1);
            IPS_SetVariableProfileAssociation("JSLive_Periode2", 5, $this->Translate("Day"), "", -1);
            IPS_SetVariableProfileAssociation("JSLive_Periode2", 6, $this->Translate("Hour"), "", -1);
        }

        $this->RegisterVariableInteger("Period",  $this->Translate("Period"), "JSLive_Periode2", 0);
        $this->RegisterVariableBoolean("Relativ",  $this->Translate("Relativ"), "~Switch", 1);

        $this->EnableAction("Period");
        $this->EnableAction("Relativ");

        $identIdlist = array();
        $identIdlist[] = IPS_GetObjectIDByIdent("Period", $this->InstanceID);
        $identIdlist[] = IPS_GetObjectIDByIdent("Relativ", $this->InstanceID);
        $this->SetBuffer("IdentIDList", json_encode($identIdlist));
    }
    public function RequestAction($Ident, $Value) {
        $this->SetValue($Ident, $Value);
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
            case "getUpdate":
                return $this->GetUpdate($buffer['queryData']);
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
            $scriptData = file_get_contents (__DIR__ ."/../SymconJSLive/templates/RadarChart.html");
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
    public function GetUpdate(array $querydata){
        $updateData = array();

        if(array_key_exists("loadconfig", $querydata)) {
            $updateData["CONFIG"] = $this->GetConfigurationData();
        }
        elseif(array_key_exists("loadLabels", $querydata)) {
            $data = $this->GenerateLabels();
            if(count($data["alias"]) > 0){
                //labels ersetzen
                $updateData["LABELS"] = $data["alias"];
            }else{
                $updateData["LABELS"] = $data["labels"];
            }
        }
        elseif(array_key_exists("var", $querydata)) {
            //einzelnen datensatz laden!
            $variable = $querydata["var"];
            $labels = $this->GenerateLabels();

            $data = $this->GenerateDataSet($labels["labels"], $variable);
            $updateData["DATASETS"] = $data["datasets"];
        }else{
            $labels = $this->GenerateLabels();
            $data = $this->GenerateDataSet($labels["labels"]);
            $updateData["DATASETS"] = $data["datasets"];
            if(count($labels["alias"]) > 0){
                //labels ersetzen
                $updateData["LABELS"] = $labels["alias"];
            }else{
                $updateData["LABELS"] = $labels["labels"];
            }
            $updateData["CONFIG"] = $this->GetConfigurationData();
        }

        return json_encode($updateData);
    }
    private function GetData(array $querydata){
        $output = array();
        $load_vars = array();
        $datasets = json_decode($this->ReadPropertyString("Datasets"), true);
        $labels = $this->GenerateLabels();

        //find custome Variabeles
        foreach ($datasets as $item){
            $s_output = array();
            if(!IPS_VariableExists($item["Variable"])) {
                $this->SendDebug("GetUpdate", "VARIABLE " .$item["Variable"] . " NOT EXIST!", 0);
                continue;
            }

            $VariableType = IPS_GetVariable($item["Variable"])["VariableType"];
            if($VariableType == 3) {
                $result = json_decode(GetValue($item["Variable"]), true);
                if (json_last_error() === 0) {
                    $s_output["Variable"] = $item["Variable"];

                    $data = $this->GenerateDataSet($labels["labels"]);
                    $s_output["Value"] = $this->GetCustomData($labels["labels"], $result);

                    $output[] = $s_output;
                }else{
                    $s_output["Variable"] = $item["Variable"];
                    $s_output["Value"] = array();

                    $output[] = $s_output;
                }
            }
        }

        //Artibute laden!
        $identIdlist = json_decode($this->GetBuffer("IdentIDList"), true);

        foreach ($identIdlist as $item){
            $s_output = array();
            $s_output["Variable"] = $item;
            $s_output["Value"] = GetValue($item);

            $output[] = $s_output;
        }

        return json_encode($output);
    }

    private function ReplacePlaceholder($htmlData){
        $htmlData = str_replace("{TITLE_TEXT}", $this->ReadPropertyString("title_text"), $htmlData);

        //Title
        $htmlData = str_replace("{TITLE}", $this->json_encode_advanced($this->GenerateTitleData()), $htmlData);

        //datasets and labels
        $labels = $this->GenerateLabels();
        $data = $this->GenerateDataSet($labels["labels"]);
        $htmlData = str_replace("{DATASETS}", $this->json_encode_advanced($data["datasets"]), $htmlData);
        if(count($labels["alias"]) > 0){
            //labels ersetzen
            $htmlData = str_replace("{LABELS}", $this->json_encode_advanced($labels["alias"]), $htmlData);
        }else{
            $htmlData = str_replace("{LABELS}", $this->json_encode_advanced($labels["labels"]), $htmlData);
        }

        //Legend
        $htmlData = str_replace("{LEGEND}", $this->json_encode_advanced($this->GenerateLegendData()), $htmlData);

        //Tooltipdata
        $htmlData = str_replace("{TOOLTIPS}", $this->json_encode_advanced($this->GenerateTooltipData()), $htmlData);

        //configuration Data
        $htmlData = str_replace("{CONFIG}", $this->json_encode_advanced($this->GetConfigurationData()), $htmlData);

        //Load Fonts
        $htmlData = str_replace("{FONTS}", $this->LoadFonts(), $htmlData);

        return $htmlData;
    }

    private function GenerateTitleData(){
        $output = array();
        $output["text"] = $this->ReadPropertyString("title_text");
        $output["position"] = $this->ReadPropertyString("title_position");
        $output["fontSize"] = $this->ReadPropertyInteger("title_fontSize");

        $output["display"] = $this->ReadPropertyBoolean("title_display");

        if($this->ReadPropertyInteger("title_fontColor") >= 0) {
            $rgbdata = $this->HexToRGB($this->ReadPropertyInteger("title_fontColor"));
            $output["fontColor"] = "rgb(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ")";
        }

        $output["fontFamily"] = $this->ReadPropertyString("title_fontFamily");

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
    private function GenerateLabels(){
        $output["labels"] = array();
        $output["alias"] = array();

        //labels erstellen
        $customScale = json_decode($this->ReadPropertyString("customScale"), true);
        $customScale_Mode = $this->ReadPropertyBoolean("customScale_mode");

        if(count($customScale) > 0 && $customScale_Mode){
            $arr_order = array_column($customScale, 'Order');
            array_multisort($arr_order, SORT_ASC , $customScale);

            foreach ($customScale as $item) {
                $output["alias"][] = $item["Alias"];
                $output["labels"][] = $item["Value"];
            }
        }else{
            $start = $this->ReadPropertyInteger("data_minValue");
            $end = $this->ReadPropertyInteger("data_maxValue");
            $sections = $this->ReadPropertyInteger("data_sections");
            $precision = $this->ReadPropertyInteger("data_precision");

            $output["labels"][] = round($start, $precision);
            $jump = round((($end - $start) / $sections), $precision);

            $sections = $sections - 1;
            for($i = 1; $i <= $sections; $i++){
                $output["labels"][] = round(($i * $jump), $precision);
            }
            //$output["labels"][] = round($end, $precision);

            if(count($customScale) > 0 ){
                $output["alias"] = $output["labels"];
                foreach ($customScale as $item) {
                    if (in_array($item["Value"], $output["labels"])) {
                        $key = array_search($item["Value"], $output["labels"]);
                        $output["alias"][$key] = $item["Alias"];
                    }
                }
            }

            $this->SendDebug("GenerateLabels", json_encode($output["labels"]), 0);
        }

        return $output;
    }

    private function GenerateDataSet($labels, $var = 0){
        $output["datasets"] = array();

        //datasets erstellen
        $datasets = json_decode($this->ReadPropertyString("Datasets"),true);
        if(!is_array($datasets)){
            $this->SendDebug("GenerateDataSet", "No Variables set!", 0);
            return "{}";
        }

        if($var > 0){
            foreach($datasets as $key => $id)
            {
                if($var != $id["Variable"]){
                    //$this->SendDebug("GenerateDataSet", "ITEM(".$key.") " . $id["Variable"], 0);
                    unset($datasets[$key]);
                }
            }
        }else{
            //sortieren aufsteigend nach order
            $arr_order = array_column($datasets, 'Order');
            array_multisort($arr_order, SORT_ASC , $datasets);
        }

        $starData = $this->GetCorrectStartDate();
        $date_start = $starData["start"];
        $date_end = $starData["end"];
        $Aggregationsstufe = $starData["stufe"];

        $emptyGrpID = 0;

        foreach($datasets as $item){
            if(!IPS_VariableExists($item["Variable"])) {
                $this->SendDebug("GenerateDataSet", "VARIABLE " .$item["Variable"] . " NOT EXIST!", 0);
                continue;
            }

            $emptyGrpID--;
            $singelOutput = array();
            $singelOutput["Variable"] = $item["Variable"];

            if(empty($item["Title"])){
                //Load Variablen Name wenn label leer ist
                if(IPS_VariableExists($item["Variable"]))
                    $singelOutput["label"] = IPS_GetObject($item["Variable"])["ObjectName"];
            }else{
                $singelOutput["label"] = $item["Title"];
            }

            $singelOutput["order"] = $item["Order"];


            if($item["BackgroundColor"] >= 0) {
                $rgbdata = $this->HexToRGB($item["BackgroundColor"]);
                $singelOutput["backgroundColor"] = "rgba(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ", " . number_format($item["BackgroundColor_Alpha"], 2, '.', '') . ")";
            }

            if($item["BorderColor"] >= 0) {
                $rgbdata = $this->HexToRGB($item["BorderColor"]);
                $singelOutput["borderColor"] = "rgba(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ", " . number_format($item["BorderColor_Alpha"], 2, '.', '') . ")";
            }

            $singelOutput["borderWidth"] = $item["BorderWidth"];
            $singelOutput["offset"] = $item["Offset"];

            $singelOutput["counter"] = false;


            //datenabrufen
            $VariableType = IPS_GetVariable($item["Variable"])["VariableType"];
            if($VariableType == 3){
                //string nur custom!
                $result = json_decode(GetValue($item["Variable"]), true);

                if (json_last_error() === 0) {
                    $singelOutput["data"] = $this->GetCustomData($labels, $result);
                }else{
                    continue;
                }
            }else{
                $singelOutput["data"] = $this->GetArchivData($labels, $item["Variable"], $item["Reference"], $item["Offset"], $date_start, $date_end, $Aggregationsstufe, $item["Mode"], $starData["datasets"]);
            }

            //BorderDash
            $dash_arr = $item["Dash"];
            if(is_array($dash_arr) && count($dash_arr)){
                //sortieren aufsteigend nach order
                $arr_order = array_column($dash_arr, 'Order');
                array_multisort($arr_order, SORT_ASC , $dash_arr);

                $dash = array();
                foreach($dash_arr as $d_item){
                    $dash[] = $d_item["Length"];
                }

                $singelOutput["borderDash"] = $dash;
            }

            //Pointstyle
            $singelOutput["pointHoverRadius"] = $this->ReadPropertyInteger("point_hoverRadius");
            //Pointsytle for chart, or global
            if(array_key_exists("PointStyle", $item) && !empty($item["PointStyle"])) {
                $singelOutput["pointStyle"] = $item["PointStyle"];
            }else{
                $singelOutput["pointStyle"] = $this->ReadPropertyString("points_Style");
            }
            //Pointradius for chart, or global
            if(array_key_exists("PointRadius", $item) && $item["PointRadius"] >= 0) {
                $singelOutput["pointRadius"] = $item["PointRadius"];
            }else{
                $singelOutput["pointRadius"] = $this->ReadPropertyInteger("point_radius");
            }

            $singelOutput["digits"] = $this->ReadPropertyInteger("data_precision");

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
                //$datalabels[""] = $item["datalabels_"];

                $datalabels["showPrefix"] = $item["datalabels_showPrefix"];
                $datalabels["showSuffix"] = $item["datalabels_showSuffix"];

                //"visible": false,

                $singelOutput["datalabels"] = $datalabels;
            }


            $output["datasets"][] = $singelOutput;
        }

        return $output;
    }

    private function GetArchivData(array $labels, int $varId, $ref_varId, int $offset, int $date_start, int $date_end, int $Aggregationsstufe, $datamode, int $lastDatasets = 0, bool $jsconfig = true){
        $archiveControlID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
        $period = $this->GetValue("Period");
        $counter = false;

        $start = $this->ReadPropertyInteger("data_minValue");
        $end = $this->ReadPropertyInteger("data_maxValue");
        $sections = $this->ReadPropertyInteger("data_sections");
        $precision = $this->ReadPropertyInteger("data_precision");

        $useRef = true;
        if(!IPS_VariableExists($ref_varId)){
            $ref_varId = $varId;
            $useRef = false;
        }

        if($this->ReadPropertyBoolean("Debug")) $this->SendDebug("GetArchivData", "Get ArchivData for => " . $varId,0);
        $VariableType = IPS_GetVariable($ref_varId)["VariableType"]; // 0: Boolean, 1: Integer, 2: Float, 3: String)

        if($VariableType == 2){
            //nur bei float runden
            $jump = round((($end - $start) / $sections), $precision);

            $min = round(($start - ($jump / 2)), $precision);
            $max = round(($end + ($jump / 2)), $precision);
        }elseif($VariableType == 1){
            $jump = (int)(($end - $start) / $sections);

            $min = (int)($start - ($jump / 2));
            $max = (int)($end + ($jump / 2));
        }elseif($VariableType == 3){
            //sicher ist sicher
            return;
        }

        if($this->ReadPropertyBoolean("Debug")) $this->SendDebug("GetArchivData", "Start_Date: ".$date_start. " | End_Date: ".$date_end, 0);

        $output = array();
        $archivData = array();
        $intval_offset = 0;


        //offset
        if($offset > 0){
            $offsetData = $this->GetOffsetDate($date_start, $date_end, $offset);
            $date_start = $offsetData["start"];
            $date_end = $offsetData["end"];
            $intval_offset = $offsetData["interval"];

        }

        $mode = "";
        if($this->ReadPropertyBoolean("Debug")) {
            $this->SendDebug("GetArchivData", "Periode: " . $period . " | Start:" . gmdate("d-m-Y H:i:s", $date_start) . " | End:" . gmdate("d-m-Y H:i:s", $date_end), 0);
        }

        if(6 <= $period){
            //hohe auflösung für tag
            $archivData = AC_GetLoggedValues($archiveControlID, $ref_varId, $date_start, $date_end, 0);
            $mode = "Value";
        }else{
            $archivData = AC_GetAggregatedValues($archiveControlID, $ref_varId, $Aggregationsstufe, $date_start, $date_end, 0);
            $mode = "Avg";
        }

        //$this->SendDebug("GetArchivData", json_encode($archivData),0);

        $oldVal = 0;
        $output = array_fill(0, count($labels), 0);
        $output_count = array_fill(0, count($labels), 0); //for Debug only
        $count = 0;
        $sum = 0;
        $count_archivData = count($archivData);

        //sortieren der ausgabe
        $arr_order = array_column($archivData, 'TimeStamp');
        array_multisort($arr_order, SORT_ASC , $archivData);

        foreach ($archivData as $a_key => $item) {
            //label finden!
            if($useRef){
                $ref = $item[$mode];
                $val = 0;
                if($a_key >= ($count_archivData-1)) continue; //letzten seitraum überspringen

                $t_start = $item["TimeStamp"];
                $t_end = $archivData[$a_key+1]["TimeStamp"];

                if(6 <= $period){
                    //hohe auflösung für tag
                    $sub_archivData = AC_GetLoggedValues($archiveControlID, $varId, $t_start, $t_end, 0);
                }else{
                    $t_time = microtime();
                    $sub_archivData = AC_GetAggregatedValues($archiveControlID, $varId, $Aggregationsstufe, $t_start, $t_end, 0);
                    //$this->SendDebug("Test", round((microtime()-$t_time)*1000)."ms", 0);
                }
                $sub_count = count($sub_archivData);

                if($this->ReadPropertyBoolean("Debug")){
                    $this->SendDebug("GetArchivData", "Ref:".$ref." | Start:".gmdate("d-m-Y H:i:s", $t_start)." | End:".gmdate("d-m-Y H:i:s", $t_end)." | Count:".$sub_count,0);
                }

                if($datamode == "counter"){
                    $val = $sub_count;
                }else {
                    //mode average
                    $sub_sum = 0;
                    foreach ($sub_archivData as $sub_item) {
                        $sub_sum = $sub_sum + $sub_item[$mode];
                    }
                    if($sub_count > 0){
                        $val = round(($sub_sum / $sub_count), $precision);
                    }
                }
                if($this->ReadPropertyBoolean("Debug")) {
                    $this->SendDebug("GetArchivData", "Ref:" . $ref . " | Val:" . $val, 0);
                }
            }else{
                $val = $item[$mode];
                $ref = $item[$mode];
            }

            if($VariableType == 2){
                $ref = round($ref, $precision);
                //$this->SendDebug("GetArchivData", "Value (".$val.")", 0);
            }
            if($VariableType == 1 || $VariableType == 2) {
                if ($ref > $max || $ref < $min) {
                    //verwerfen auserhalb des bereiches
                    $this->SendDebug("GetArchivData", "Datasets Skip Value  " . $min . " < " . $ref . " > " . $max, 0);
                    continue;
                }
            }

            $index = -1;
            if($VariableType == 1 || $VariableType == 2) {
                $closest = null;
                foreach ($labels as $key => $item) {
                    if ($closest === null || abs($ref - $closest) >= abs($item - $ref)) {
                        $closest = $item;
                        $index = $key;
                    }
                }
            }elseif($VariableType == 3){
                //bei string sriekt den namen suchen und mit tolower arbeiten
                if(in_array(strtolower($ref), $labels)){
                    $index = array_search(strtolower($ref), $labels);
                }
            }

            if($index >= 0 && count($output) > $index){
                if($datamode == "counter"){
                    if($useRef){
                        $output[$index] = $output[$index] + $val;
                    }else{
                        $output[$index] = $output[$index] + 1;
                    }
                }else{
                    //mode average
                    $count = + 1;
                    $sum = $val;

                    $output_count[$index]++;
                    $output[$index] = round($sum /$count, $precision);
                }
            }else{
                $this->SendDebug("GetArchivData", "Index wrong! (".$val.") => ". $index, 0);
            }
        }

        if($this->ReadPropertyBoolean("Debug")){
            $this->SendDebug("GetArchivData", "OUPUT => ".json_encode($output), 0);
            $this->SendDebug("GetArchivData", "OUPUT_COUNT => ".json_encode($output_count), 0);
        }

        return $output;
    }

    private function GetCustomData(array $labels, array $arr){
        $output = array();

        //aller labes tolowercase
        for($i=0; $i < count($labels); $i++) {
            $labels[$i] = strtolower($labels[$i]);
        }

        $output = array_fill(0, count($labels), 0);
        foreach($arr as $key => $item){
            if(!in_array($key, $labels)){
                $this->SendDebug("GetCustomData","IGNORE KEY => ". $key . " NOT IN LIST!", 0);
            }

            $index = array_search(strtolower($key), $labels);
            $this->SendDebug("GetCustomData",$index, 0);
            $output[$index] = $item;
        }

        return $output;
    }

    private function GetConfigurationData(){
        $output = json_decode(IPS_GetConfiguration($this->InstanceID), true);

        $output["InstanceID"] = $this->InstanceID;
        $output["Period"] = $this->GetValue("Period");
        $output["Relativ"] = $this->GetValue("Relativ");

        $output["ID_Period"] = IPS_GetObjectIDByIdent("Period", $this->InstanceID);
        $output["ID_Relativ"] = IPS_GetObjectIDByIdent("Relativ", $this->InstanceID);

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

        //customVars
        //find custome Variabeles
        $datasets = json_decode($output["Datasets"], true);
        $output["CustomVars"] = array();
        $output["Var_List"] = array();
        foreach ($datasets as $item){
            if(!IPS_VariableExists($item["Variable"])) {
                $this->SendDebug("GetUpdate", "VARIABLE " .$item["Variable"] . " NOT EXIST!", 0);
                continue;
            }

            $VariableType = IPS_GetVariable($item["Variable"])["VariableType"];
            if($VariableType == 3) {
                $output["CustomVars"][] = $item["Variable"];
            }

            $output["Var_List"][] = $item["Variable"];
        }

        //remove Dataset
        unset($output["Datasets"]);
        unset($output["customScale"]);


        return $output;
    }
    private function GetCorrectStartDate(int $date = 0){
        $date_start = new DateTime();
        $date_end = new DateTime();
        $relativ = $this->GetValue("Relativ");

        $Aggregationsstufe = 0;

        $period = $this->GetValue("Period");

        if($date != 0){
            $date_start->setTimestamp($date);
        }
        else {
            $date_start = new DateTime('NOW');
        }

        if($relativ){
            //jetzt mintus zeitraum
            switch ($period){
                case 0:
                    //Dekade
                    $date_end->setDate($date_start->format('Y'), 1, 1);
                    $date_end->setTime(0, 0, 0);

                    $date_start->setDate(($date_end->format('Y') - 9), 1, 1);
                    $date_start->setTime(0, 0, 0);

                    $Aggregationsstufe = 3;
                    break;
                case 1:
                    //Jahr
                    $date_end->setDate($date_start->format('Y'), $date_start->format('m'), 1);
                    $date_end->setTime(0, 0, 0);

                    $date_start->setDate(($date_end->format('Y')), $date_start->format('m')-11, 1);
                    $date_start->setTime(0, 0, 0);

                    $Aggregationsstufe = 2;
                    break;
                case 2:
                    //Quartal
                    $date_end->setDate($date_start->format('Y'), $date_start->format('m'), 1);
                    $date_end->setTime(0, 0, 0);

                    $date_start->setDate(($date_end->format('Y')), $date_start->format('m')-2, 1);
                    $date_start->setTime(0, 0, 0);

                    $Aggregationsstufe = 1;
                    break;
                case 3:
                    //Monat
                    $date_end->setDate($date_start->format('Y'), $date_start->format('m'), $date_start->format('d'));
                    $date_end->setTime(0, 0, 0);

                    $date_start->setDate(($date_end->format('Y')), $date_start->format('m')-1, $date_start->format('d')+1);
                    $date_start->setTime(0, 0, 0);

                    $Aggregationsstufe = 1;
                    break;
                case 4:
                    //Woche
                    $date_end->setDate($date_start->format('Y'), $date_start->format('m'), $date_start->format('d'));
                    $date_end->setTime(0, 0, 0);

                    $date_start->setDate(($date_end->format('Y')), $date_start->format('m'), $date_start->format('d')-6);
                    $date_start->setTime(0, 0, 0);

                    $Aggregationsstufe = 0;
                    break;
                case 5:
                    //Tag
                    $date_end->setDate($date_start->format('Y'), $date_start->format('m'), $date_start->format('d'));
                    $date_end->setTime($date_start->format('H'), 0, 0);

                    $date_start->setDate(($date_end->format('Y')), $date_start->format('m'), $date_start->format('d'));
                    $date_start->setTime($date_start->format('H')-23, 0, 0);

                    $Aggregationsstufe = 0;
                    break;
                case 6:
                    //Stunde
                    $date_end->setDate($date_start->format('Y'), $date_start->format('m'), $date_start->format('d'));
                    $date_end->setTime($date_start->format('H'), $date_start->format('i'), 0);

                    $date_start->setDate(($date_end->format('Y')), $date_start->format('m'), $date_start->format('d'));
                    $date_start->setTime($date_start->format('H'), $date_start->format('i')-59, 0);

                    $Aggregationsstufe = 6;
                    break;
                case 7:
                    //Minute
                    //Stunde
                    $date_end->setDate($date_start->format('Y'), $date_start->format('m'), $date_start->format('d'));
                    $date_end->setTime($date_start->format('H'), $date_start->format('i'), $date_start->format('s'));

                    $date_start->setDate(($date_end->format('Y')), $date_start->format('m'), $date_start->format('d'));
                    $date_start->setTime($date_start->format('H'), $date_start->format('i'), $date_start->format('s')-59);

                    $Aggregationsstufe = 6;
                    break;
            }
        }else{
            //z.b Start heute 00:00
            switch ($period){
                case 0:
                    //Dekade
                    $start_year = (int)($date_start->format('Y') / 10) * 10;

                    $date_start->setDate($start_year, 1, 1);
                    $date_start->setTime(0, 0, 0);

                    $date_end->setDate(($start_year+9), 12, 31);
                    $date_end->setTime(23, 59, 59);

                    $Aggregationsstufe = 4;
                    break;
                case 1:
                    //Jahr
                    $date_start->setDate($date_end->format('Y'), 1, 1);
                    $date_start->setTime(0, 0, 0);

                    $date_end->setDate($date_start->format('Y'), 13, 0);
                    $date_end->setTime(23, 59, 59);

                    $Aggregationsstufe = 3;
                    break;
                case 2:
                    //Quartal
                    $start_month = (int)(($date_start->format('m') - 1) / 3) * 3 ;

                    $date_start->setDate($date_end->format('Y'), ($start_month+1), 1);
                    $date_start->setTime(0, 0, 0);

                    $date_end->setDate($date_start->format('Y'), ($date_start->format('m') + 3), 0);
                    $date_end->setTime(23, 59, 59);

                    $Aggregationsstufe = 3;
                    break;
                case 3:
                    //Monat
                    $date_start->setDate($date_end->format('Y'), $date_start->format('m'), 1);
                    $date_start->setTime(0, 0, 0);

                    $date_end->setDate($date_start->format('Y'), $date_start->format('m')+1,0);
                    $date_end->setTime(23, 59, 59);

                    $Aggregationsstufe = 1;
                    break;
                case 4:
                    //Woche
                    //start immer am Montag
                    $date_start = $date_start->modify('monday this week');

                    $date_start->setDate($date_end->format('Y'), $date_start->format('m'), $date_start->format('d'));
                    $date_start->setTime(0, 0, 0);

                    $date_end->setDate($date_start->format('Y'), $date_start->format('m'), ($date_start->format('d')+6));
                    $date_end->setTime(23, 59, 59);

                    $Aggregationsstufe = 1;
                    break;
                case 5:
                    //Tag
                    $date_start->setDate($date_end->format('Y'), $date_start->format('m'), $date_start->format('d'));
                    $date_start->setTime(0, 0, 0);

                    $date_end->setDate($date_start->format('Y'), $date_start->format('m'), $date_start->format('d'));
                    $date_end->setTime(23, 59, 59);

                    $Aggregationsstufe = 0;
                    break;
                case 6:
                    //Stunde
                    $date_start->setDate($date_end->format('Y'), $date_start->format('m'), $date_start->format('d'));
                    $date_start->setTime($date_start->format('H'), 0, 0);

                    $date_end->setDate($date_start->format('Y'), $date_start->format('m'), $date_start->format('d'));
                    $date_end->setTime($date_start->format('H'), 59, 59);

                    $Aggregationsstufe = 6;
                    break;
                case 7:
                    //Minute
                    $date_start->setDate(($date_end->format('Y')), $date_start->format('m'), $date_start->format('d'));
                    $date_start->setTime($date_start->format('H'), $date_start->format('i'), 0);

                    $date_end->setDate($date_start->format('Y'), $date_start->format('m'), $date_start->format('d'));
                    $date_end->setTime($date_start->format('H'), $date_start->format('i'), 59);

                    $Aggregationsstufe = 6;
                    break;
            }
        }

        $s = new DateTime('NOW');
        $curVales = 0;

        //get durchschnitswert
        switch ($period){
            case 0:
                //Dekade
                $curVales = (int)$s->format("z")*24*60*60;
                break;
            case 1:
            case 2:
                //Jahr
                //Quartal
                $curVales = (int)$s->format("j")*60*60;
                break;
            case 3:
            case 4:
                //Monat
                //Woche
                $curVales = (int)$s->format("G")*60;
                break;
            case 5:
                //Tag
                $curVales = (int)$s->format("i")*60;
                break;
            case 6:
                //Stunde
                $curVales = (int)$s->format("s");
                break;
            case 7:
                //Minute
                $curVales = 0;
                break;
        }


        if($this->ReadPropertyBoolean("Debug"))
            $this->SendDebug("GetCorrectStartDate", "Realtiv: ".$relativ." | Period: ".$period." | Start_Date: ".$date_start->format('d.m.Y H:i:s'). " | End_Date: ".$date_end->format('d.m.Y H:i:s'), 0);

        return array("start" => $date_start->getTimestamp(), "end" => $date_end->getTimestamp(), "stufe" => $Aggregationsstufe, "datasets" => $curVales);
    }
    private function GetOffsetDate(int $start_unixTimeStamp, int $end_unixTimeStamp, int $offset){
        $period = $this->GetValue("Period");
        $relativ = $this->GetValue("Relativ");
        $date_start = new DateTime();
        $date_end = new DateTime();
        $intval = new DateInterval("P10Y");
        $output = array();

        $date_start->setTimestamp($start_unixTimeStamp);
        $date_end->setTimestamp($end_unixTimeStamp);

        switch($period){
            case 0:
                //Dekade
                $offset = $offset * 10 * 12;
                $intval = new DateInterval("P".$offset."M");
                break;
            case 1:
                //Jahr
                $offset = $offset * 12;
                $intval = new DateInterval("P".$offset."M");
                break;
            case 2:
                //Quartal
                $offset = $offset * 3;
                $intval = new DateInterval("P".$offset."M");
                break;
            case 3:
                //Monat
                $intval = new DateInterval("P".$offset."M");
                break;
            case 4:
                //Woche
                $offset = $offset * 7;
                $intval = new DateInterval("P".$offset."D");
                break;
            case 5:
                //Tag
                $intval = new DateInterval("P".$offset."D");
                break;
            case 6:
                //Stunde
                $intval = new DateInterval("PT".$offset."H");
                break;
            case 7:
                //Minute
                $intval = new DateInterval("PT".$offset."M");
                break;
        }

        $seconds = $intval->s + ( $intval->i * 60) + ( $intval->h * 3600) + ( $intval->d * 86400) + ( $intval->m * 2592000);

        //interfall *-1
        $intval->invert = 1;

        //neues datum berechen
        $date_start->add($intval);

        if($relativ) {
            $date_end->add($intval);
        }else{
            switch ($period){
                case 0:
                    //Dekade
                    $start_year = (int)($date_start->format('Y') / 10) * 10;

                    $date_end->setDate(($start_year+9), 12, 31);
                    $date_end->setTime(23, 59, 59);
                    break;
                case 1:
                    //Jahr
                    $date_end->setDate($date_start->format('Y'), 13, 0);
                    $date_end->setTime(23, 59, 59);
                    break;
                case 2:
                    //Quartal
                    $date_end->setDate($date_start->format('Y'), ($date_start->format('m') + 3), 0);
                    $date_end->setTime(23, 59, 59);
                    break;
                case 3:
                    //Monat
                    $date_end->setDate($date_start->format('Y'), $date_start->format('m')+1,0);
                    $date_end->setTime(23, 59, 59);
                    break;
                case 4:
                    //Woche
                    //start immer am Montag
                    $date_end->setDate($date_start->format('Y'), $date_start->format('m'), ($date_start->format('d')+6));
                    $date_end->setTime(23, 59, 59);
                    break;
                case 5:
                    //Tag
                    $date_end->setDate($date_start->format('Y'), $date_start->format('m'), $date_start->format('d'));
                    $date_end->setTime(23, 59, 59);
                    break;
                case 6:
                    //Stunde
                    $date_end->setDate($date_start->format('Y'), $date_start->format('m'), $date_start->format('d'));
                    $date_end->setTime($date_start->format('H'), 59, 59);
                    break;
                case 7:
                    //Minute
                    $date_end->setDate($date_start->format('Y'), $date_start->format('m'), $date_start->format('d'));
                    $date_end->setTime($date_start->format('H'), $date_start->format('i'), 59);
                    break;
            }
        }

        if($this->ReadPropertyBoolean("Debug"))
            $this->SendDebug("GetOffsetDate", "Start_Date: ".$date_start->format('d.m.Y H:i:s')."(".$date_start->getTimestamp().") | End_Date: ".$date_end->format('d.m.Y H:i:s')."(".$date_end->getTimestamp().") | Interval: " .$seconds, 0);

        return array("start" => $date_start->getTimestamp(), "end" => $date_end->getTimestamp(), "interval" => $seconds);
    }

    public function LoadOtherConfiguration(int $id){
        if(!IPS_ObjectExists($id)) return "Instance/Chart not found!";

        $intData = IPS_GetInstance($id);
        if($intData["ModuleInfo"]["ModuleID"] != IPS_GetInstance($this->InstanceID)["ModuleInfo"]["ModuleID"]) return "Only Allowed at the same Modul!";

        $confData = json_decode(IPS_GetConfiguration($id), true);

        //bestimmte aktuelle einstellungen beibehalten
        $confData["title_text"] = $this->ReadPropertyString("title_text");
        $confData["Datasets"]= $this->ReadPropertyString("Datasets");

        $confData["customScale"] = $this->ReadPropertyString("customScale");
        $confData["customScale_mode"] = $this->ReadPropertyBoolean("customScale_mode");

        IPS_SetConfiguration($this->InstanceID, json_encode($confData));
        IPS_ApplyChanges($this->InstanceID);
    }
}

?>