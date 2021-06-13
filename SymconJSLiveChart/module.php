<?php
include_once (__DIR__ . '/../SymconJSLive/libs/JSLiveModule.php');

class SymconJSLiveChart extends JSLiveModule{
    public function Create() {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent("{9FFF3FC0-FD51-C289-FA36-BC1C370946CF}");

        //Expert
        $this->RegisterPropertyBoolean("Debug", false);
        $this->RegisterPropertyBoolean("EnableCache", true);
        $this->RegisterPropertyBoolean("CreateOutput", true);
        $this->RegisterPropertyInteger("TemplateScriptID", 0);
        $this->RegisterPropertyBoolean("EnableViewport", true);
        $this->RegisterPropertyInteger("IFrameHeight", 0);

        //title
        $this->RegisterPropertyString("title_text", "");
        $this->RegisterPropertyBoolean("title_display", false);
        $this->RegisterPropertyString("title_position", "top");
        $this->RegisterPropertyInteger("title_fontSize", 12);
        $this->RegisterPropertyInteger("title_fontColor", 0);
        $this->RegisterPropertyString("title_fontFamily", "");

        //Axes
        $this->RegisterPropertyBoolean("axes_display", true);
        $this->RegisterPropertyBoolean("axes_showLabel", true);
        $this->RegisterPropertyBoolean("axes_drawBorder", true);
        $this->RegisterPropertyBoolean("axes_drawTicks", true);
        $this->RegisterPropertyBoolean("axes_drawOnChartArea", false);
        $this->RegisterPropertyInteger("axes_lineWidth", 1);
        $this->RegisterPropertyInteger("axes_labelText", 2);
        $this->RegisterPropertyInteger("axes_labelfontSize", 12);
        $this->RegisterPropertyInteger("axes_tickfontSize", 12);
        $this->RegisterPropertyInteger("axes_color", 0);
        $this->RegisterPropertyFloat("axes_colorAlpha", 0);
        $this->RegisterPropertyInteger("axes_fontColor", 0);
        $this->RegisterPropertyString("axes_fontFamily", "");

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
        $this->RegisterPropertyInteger("data_highResSteps", 1);
        $this->RegisterPropertyInteger("data_precision", 2);
        $this->RegisterPropertyBoolean("data_loadAsync", true);
        $this->RegisterPropertyBoolean("data_pullModeinMinute", true);
        $this->RegisterPropertyInteger("data_pullModeRefreshTime", 3);

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

        $setDefault = false;
        if(@IPS_GetObjectIDByIdent("Period", $this->InstanceID) === false) $setDefault = true;

        if (!IPS_VariableProfileExists("JSLive_Periode")){
            IPS_CreateVariableProfile("JSLive_Periode", 1);
            IPS_SetVariableProfileAssociation("JSLive_Periode", 0, $this->Translate("Decade"), "", -1);
            IPS_SetVariableProfileAssociation("JSLive_Periode", 1, $this->Translate("Year"), "", -1);
            IPS_SetVariableProfileAssociation("JSLive_Periode", 2, $this->Translate("Quarter"), "", -1);
            IPS_SetVariableProfileAssociation("JSLive_Periode", 3, $this->Translate("Month"), "", -1);
            IPS_SetVariableProfileAssociation("JSLive_Periode", 4, $this->Translate("Week"), "", -1);
            IPS_SetVariableProfileAssociation("JSLive_Periode", 5, $this->Translate("Day"), "", -1);
            IPS_SetVariableProfileAssociation("JSLive_Periode", 6, $this->Translate("Hour"), "", -1);
            IPS_SetVariableProfileAssociation("JSLive_Periode", 7, $this->Translate("Minute"), "", -1);
        }
        if (!IPS_VariableProfileExists("JSLive_Now")){
            IPS_CreateVariableProfile("JSLive_Now", 0);
            IPS_SetVariableProfileAssociation("JSLive_Now", true, $this->Translate("Now"), "", -1);
            IPS_SetVariableProfileAssociation("JSLive_Now", false, " ", "", -1);
        }

        $this->RegisterVariableInteger("Period",  $this->Translate("Period"), "JSLive_Periode", 5);
        $this->RegisterVariableBoolean("Now",  $this->Translate("Now"), "JSLive_Now", 96);
        $this->RegisterVariableBoolean("Relativ",  $this->Translate("Relativ"), "~Switch", 97);

        $this->RegisterVariableInteger("Offset", $this->Translate("Offset"), "", 98);
        $this->RegisterVariableInteger("StartDate", $this->Translate("Start Date"), "~UnixTimestamp", 99);

        $this->EnableAction("Period");
        $this->EnableAction("Offset");
        $this->EnableAction("Now");
        $this->EnableAction("StartDate");
        $this->EnableAction("Relativ");

        

        $this->SetStatus(102);

        //setdafult if Variabel Periode not define
        if($setDefault){
            $this->SetValue("Period", 6);
            $this->SetValue("Offset", 0);
            $this->SetValue("Now", True);
            $this->SetValue("StartDate", 0);
            $this->SetValue("Relativ", True);
        }
    }
    public function RequestAction($Ident, $Value) {

        switch($Ident) {
            case "Period":
                $this->SetValue("Now", true);
                $this->SetValue("Offset", 0);
                $this->SetValue($Ident, $Value);
                break;
            case "Now":
                if($Value){
                    //offset zurück auf 0!
                    $this->SetValue("Offset", 0);
                }
                $this->SetValue($Ident, $Value);
                break;
            case "Offset":
                //offset zurück auf 0!
                if($Value <= 0){
                    $this->SetValue("Now", true);
                    $this->SetValue($Ident, 0);
                }else{
                    //startdatum updaten!
                    $curDate = time();
                    $data = $this->GetCorrectStartDate($curDate);
                    $data = $this->GetOffsetDate($data["start"], $data["end"], $Value);
                    $this->SetValue("StartDate", $data["start"]);

                    $this->SetValue("Now", false);
                    $this->SetValue($Ident, $Value);
                }
                break;
            case "StartDate":
                if($Value >= time()){
                    $this->SetValue("Now", true);
                    $this->SetValue($Ident, time());
                    $this->SetValue("Offset", 0);
                }else{
                    $this->SetValue("Now", false);
                    $this->SetValue($Ident, $Value);
                }
                break;
            default:
                $this->SetValue($Ident, $Value);
        }
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
            $scriptData = file_get_contents (__DIR__ ."/../SymconJSLive/templates/Chart.html");
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

        if(array_key_exists("loadxaxes", $querydata)) {
            $updateData["XAXES"] = $this->GenerateXAxesData();
        }
        elseif(array_key_exists("loadconfig", $querydata)) {
            $updateData["Config"] = $this->GetConfigurationData();
        }
        elseif(array_key_exists("var", $querydata)) {
            //einzelnen datensatz laden!
            $variable = $querydata["var"];

            $data = $this->GenerateDataSet($variable);
            $updateData["DATASETS"] = $data["datasets"];
            $updateData["AXES"] = $data["charts"];
        }else{
            $data = $this->GenerateDataSet();
            $updateData["DATASETS"] = $data["datasets"];
            $updateData["AXES"] = $data["charts"];
            $updateData["Config"] = $this->GetConfigurationData();

            $updateData["XAXES"] = $this->GenerateXAxesData();
        }

        return json_encode($updateData);
    }
    private function GetData(array $querydata){
        $output = array();
        $load_vars = array();
        $datasets = json_decode($this->ReadPropertyString("Datasets"), true);
        $precision = $this->ReadPropertyInteger("data_precision");

        if(!array_key_exists("var", $querydata)) {
            //$this->SendDebug("GetData", "PARAMETER VARIABLE NOT SET!(" . json_encode($querydata). ")", 0);
            //load all variables
            $load_vars = json_decode($this->GetBuffer("IdentIDList"), true);

            foreach ($datasets as $item) {
                if (!in_array($item["Variable"], $load_vars)) {
                    $load_vars[] = $item["Variable"];
                }
            }
        }else{
            $load_vars[] = $querydata["var"];
        }

        foreach($load_vars as $var) {
            $o_item = array();
            $o_item["Variable"] = $var;
            if (!IPS_VariableExists($var)) {
                $this->SendDebug("GetData", "VARIABLE NOT EXIST!", 0);
                continue;
            }


            $identIdlist = json_decode($this->GetBuffer("IdentIDList"), true);
            $key = array_search($var, array_column($datasets, 'Variable'));
            if ($key === false && !in_array($var, $identIdlist)) {
                $this->SendDebug("GetData", "VARIABLE NOT IN INSTANCE!", 0);
                continue;
            }

            $o_item["Value"] = round(GetValue($var),$precision);

            if ($this->ReadPropertyBoolean("Debug"))
                $this->SendDebug("GetData", json_encode($querydata), 0);

            if (array_key_exists("start", $querydata) && array_key_exists("end", $querydata)) {
                $hires = 7;
                $offset = 0;
                $start = 0;
                $end = 0;
                $Aggregationsstufe = 7;
                $period = $this->GetValue("Period");

                if (array_key_exists("hires", $querydata)) $hires = $querydata["hires"];
                if (array_key_exists("offset", $querydata)) $offset = $querydata["offset"];
                if (array_key_exists("start", $querydata)) $start = $querydata["start"];
                if (array_key_exists("end", $querydata)) $end = $querydata["end"];

                if ($hires != 7) {
                    switch ($period) {
                        case 0:
                            //Dekade
                            $Aggregationsstufe = 4;
                            break;
                        case 1:
                        case 2:
                            //Jahr
                            //Quartal
                            $Aggregationsstufe = 3;
                            break;
                        case 3:
                        case 4:
                            //Monat
                            //Woche
                            $Aggregationsstufe = 1;
                            break;
                        case 5:
                            //Tag
                            $Aggregationsstufe = 0;
                            break;
                        case 6:
                            //Stunde
                            $Aggregationsstufe = 6;
                            break;

                    }
                }
                $o_item["archiv"] = $this->GetArchivData($var, $hires, $offset, $start, $end, $Aggregationsstufe, 0, false);
            }
            $output[] = $o_item;
        }
        return json_encode($output);
    }

    private function ReplacePlaceholder(string $htmlData){
        $htmlData = str_replace("{TITLE_TEXT}", $this->ReadPropertyString("title_text"), $htmlData);

        //Title
        $htmlData = str_replace("{TITLE}", $this->json_encode_advanced($this->GenerateTitleData()), $htmlData);


        //datasets and axis
        $data = $this->GenerateDataSet(0, false);
        $htmlData = str_replace("{DATASETS}", $this->json_encode_advanced($data["datasets"]), $htmlData);
        $htmlData = str_replace("{AXES}", $this->json_encode_advanced($data["charts"]), $htmlData);

        if(count($data["charts"]) == 0) return "NO CHARTS DEFINE!";

        //Legend
        $htmlData = str_replace("{LEGEND}", $this->json_encode_advanced($this->GenerateLegendData()), $htmlData);

        //Tooltipdata
        $htmlData = str_replace("{TOOLTIPS}", $this->json_encode_advanced($this->GenerateTooltipData()), $htmlData);

        //configuration Data
        $htmlData = str_replace("{CONFIG}", $this->json_encode_advanced($this->GetConfigurationData()), $htmlData);

        //xAxes
        $htmlData = str_replace("{XAXES}", $this->json_encode_advanced($this->GenerateXAxesData()), $htmlData);

        //Load Fonts
        $arr = array($this->ReadPropertyString("title_fontFamily"), $this->ReadPropertyString("axes_fontFamily"), $this->ReadPropertyString("legend_fontFamily"), $this->ReadPropertyString("tooltips_fontFamily"), $this->ReadPropertyString("datalabels_fontFamily"));
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
    private function GenerateDataSet(int $var = 0, bool $getData = true){
        $archiveControlID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
        $output["datasets"] = array();
        $output["charts"] = array();
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
            if($this->ReadPropertyBoolean("Debug"))
                $this->SendDebug("GenerateDataSet", "ITEM " .json_encode($item), 0);

            if(!IPS_VariableExists($item["Variable"])) {
                $this->SendDebug("GenerateDataSet", "VARIABLE " .$item["Variable"] . " NOT EXIST!", 0);
                continue;
            }

            $emptyGrpID--;
            $singelOutput = array();
            $singelOutput["Variable"] = $item["Variable"];

            if(empty($item["Title"])){
                //Load Variablen Name wenn label leer ist
                $singelOutput["label"] = IPS_GetObject($item["Variable"])["ObjectName"];
            }else{
                $singelOutput["label"] = $item["Title"];
            }

            $singelOutput["type"] = $item["Type"];
            $singelOutput["order"] = $item["Order"];


            if(is_numeric($item["BackgroundColor"]) && $item["BackgroundColor"] >= 0) {
                $rgbdata = $this->HexToRGB($item["BackgroundColor"]);
                $singelOutput["backgroundColor"] = "rgba(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ", " . number_format($item["BackgroundColor_Alpha"], 2, '.', '') . ")";
                $singelOutput["fill"] = true;
            }else{
                $singelOutput["fill"] = false;
            }

            if(is_numeric($item["BorderColor"]) && $item["BorderColor"] >= 0) {
                $rgbdata = $this->HexToRGB($item["BorderColor"]);
                $singelOutput["borderColor"] = "rgba(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ", " . number_format($item["BorderColor_Alpha"], 2, '.', '') . ")";
            }

            $singelOutput["borderWidth"] = $item["BorderWidth"];

            $singelOutput["highRes"] = $item["HighRes"];
            $singelOutput["offset"] = $item["Offset"];

            $singelOutput["counter"] = false;
            if(AC_GetAggregationType($archiveControlID, $item["Variable"]) == 1){
                $singelOutput["counter"] = true;
                $singelOutput["lastValue"] = number_format(GetValue($item["Variable"]), 5, '.', '');
            }

            //datenabrufen
            if($getData){
                $singelOutput["data"] = $this->GetArchivData($item["Variable"], $item["HighRes"], $item["Offset"], $date_start, $date_end, $Aggregationsstufe, $starData["datasets"]);
            }else{
                $singelOutput["data"] = array();
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

            //stackerd Chart
            $isStacked = false;
            if($item["StackGroup"] > 0){
                $singelOutput["stack"] = $item["Profile"]."-" .$item["StackGroup"];
                $isStacked = true;
            }else{
                $singelOutput["stack"] = $emptyGrpID."Stack";
            }

            //Axis
            $axesname = $item["Profile"];
            $singelOutput["yAxisID"] = $axesname;
            $digits = 2;

            //Pointstyle
            $singelOutput["pointRadius"] = $this->ReadPropertyInteger("point_radius");
            $singelOutput["pointHoverRadius"] = $this->ReadPropertyInteger("point_hoverRadius");
            $singelOutput["pointStyle"] = $this->ReadPropertyString("points_Style");

            if(IPS_GetVariable($item["Variable"])["VariableType"] == 0){
                //nur bei Bool variablen!
                $singelOutput["steppedLine"] = "before";
            }

            //falls noch nicht vorhanden anlegen
            $key = array_search($axesname, array_column($output["charts"], 'id'));
            if($key === FALSE){
                $axisoutput = array();
                $axisoutput["type"] = "linear";
                $axisoutput["display"] = $this->ReadPropertyBoolean("axes_display");
                $axisoutput["id"] = $axesname;
                $axisoutput["position"] = $item["Side"];
                $axisoutput["axis"] = "y";

                $axisoutput["Prefix"] = "";
                $axisoutput["Suffix"] = "";

                //vor Stacked Charts!
                $axisoutput["stacked"] = $isStacked;

                if($this->ReadPropertyInteger("axes_color") >= 0){
                    $rgbdata = $this->HexToRGB($this->ReadPropertyInteger("axes_color"));
                    $axisoutput["grid"]["borderColor"] = "rgba(" . $rgbdata["R"] .", " . $rgbdata["G"] .", " . $rgbdata["B"].", " . number_format($this->ReadPropertyFloat("axes_colorAlpha"), 2, '.', '').")";
                    $axisoutput["grid"]["borderWidth"] = $this->ReadPropertyInteger("axes_lineWidth");

                    $axisoutput["grid"]["color"] = "rgba(" . $rgbdata["R"] .", " . $rgbdata["G"] .", " . $rgbdata["B"].", " . number_format($this->ReadPropertyFloat("axes_colorAlpha"), 2, '.', '').")";
                    $axisoutput["grid"]["lineWidth"] = $this->ReadPropertyInteger("axes_lineWidth");

                    $axisoutput["ticks"]["color"] =  "rgba(" . $rgbdata["R"] .", " . $rgbdata["G"] .", " . $rgbdata["B"].", " . number_format($this->ReadPropertyFloat("axes_colorAlpha"), 2, '.', '').")";
                }

                if($this->ReadPropertyInteger("axes_fontColor") >= 0) {
                    $rgbdata = $this->HexToRGB($this->ReadPropertyInteger("axes_fontColor"));
                    $axisoutput["ticks"]["color"] = "rgba(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ")";
                }

                $axisoutput["grid"]["drawBorder"] = $this->ReadPropertyBoolean("axes_drawBorder");
                $axisoutput["grid"]["drawOnChartArea"] = $this->ReadPropertyBoolean("axes_drawOnChartArea");
                $axisoutput["grid"]["drawTicks"] = $this->ReadPropertyBoolean("axes_drawTicks");

                $axisoutput["ticks"]["font"]["size"] = $this->ReadPropertyInteger("axes_tickfontSize");
                $axisoutput["ticks"]["font"]["family"] = $this->ReadPropertyString("axes_fontFamily");

                if(IPS_VariableProfileExists($item["Profile"])){
                    $profilData = IPS_GetVariableProfile($item["Profile"]);
                    if(($profilData["MaxValue"] != 0 || $profilData["MinValue"]  != 0) && !$item["DynScale"]){

                        //$axisoutput["suggestedMax"] = $profilData["MaxValue"];
                        //$axisoutput["suggestedMin"] = $profilData["MinValue"];

                        $axisoutput["min"] =  $profilData["MinValue"];
                        $axisoutput["max"] = $profilData["MaxValue"];
                    }

                    $axisoutput["Prefix"] = $profilData["Prefix"];
                    $axisoutput["Suffix"] = $profilData["Suffix"];


                    if($profilData["StepSize"] > 0){
                        $axisoutput["ticks"]["stepSize"] = $profilData["StepSize"];
                    }

                    $digits = $profilData["Digits"];

                    $axisoutput["title"]["display"] = $this->ReadPropertyBoolean("axes_showLabel");
                    $l_text = "";
                    switch ($this->ReadPropertyInteger("axes_labelText")){
                        case 0:
                            $l_text = $item["Profile"];
                            break;
                        case 1:
                            $l_text = $item["Profile"] . "(" . $profilData["Suffix"] . ")";
                            break;
                        case 2:
                            $l_text = $profilData["Suffix"];
                            break;
                    }
                    $axisoutput["title"]["text"] = $l_text;

                    if($this->ReadPropertyInteger("axes_fontColor") >= 0) {
                        $rgbdata = $this->HexToRGB($this->ReadPropertyInteger("axes_fontColor"));
                        $axisoutput["title"]["color"] = "rgba(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ")";
                    }

                    $axisoutput["title"]["font"]["size"] = $this->ReadPropertyInteger("axes_labelfontSize");
                    $axisoutput["title"]["font"]["family"] = $this->ReadPropertyString("axes_fontFamily");
                }
                $output["charts"][$axesname] = $axisoutput;
            }else{

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
                //$datalabels[""] = $item["datalabels_"];

                $datalabels["showPrefix"] = $item["datalabels_showPrefix"];
                $datalabels["showSuffix"] = $item["datalabels_showSuffix"];

                //"visible": false,

                $singelOutput["datalabels"] = $datalabels;
            }

            $singelOutput["digits"] = $digits;
            $output["datasets"][] = $singelOutput;
        }

        return $output;
    }
    private function GenerateXAxesData(){
        $output = array();
        $output["time"]["tooltipFormat"] = "DD.MM.YYYY HH:mm:ss";
        //$output["stacked"] = true;

        $output["ticks"]["font"]["size"] = $this->ReadPropertyInteger("axes_tickfontSize");
        $output["ticks"]["font"]["family"] = $this->ReadPropertyString("axes_fontFamily");


        $period = $this->GetValue("Period");
        $relativ = $this->GetValue("Relativ");
        switch ($period) {
            case 0:
                //Dekade
                $output["type"] = "time";
                $output["time"]["unit"] = "year";
                $output["time"]["stepSize"] = 1;
                break;
            case 1:
                //Jahr
                $output["type"] = "time";
                $output["time"]["unit"] = "month";
                $output["time"]["stepSize"] = 2;
                break;
            case 2:
                //Quartal
                $output["type"] = "time";
                $output["time"]["unit"] = "month";
                $output["time"]["stepSize"] = 1;
                break;
            case 3:
                //Monat
                $output["type"] = "time";
                $output["time"]["unit"] = "day";
                $output["time"]["stepSize"] = 3;
                break;
            case 4:
                //Woche
                $output["type"] = "time";
                $output["time"]["unit"] = "day";
                $output["time"]["stepSize"] = 1;
                break;
            case 5:
                //Tag
                $output["type"] = "time";
                $output["time"]["unit"] = "hour";
                $output["time"]["stepSize"] = 3;
                break;
            case 6:
                //Stunde
                if($relativ) $output["type"] = "realtime"; else $output["type"] = "time";
                $output["time"]["unit"] = "minute";
                $output["time"]["stepSize"] = 5;

                if($relativ) {
                    $output["realtime"]["duration"] = 3600000;
                    $output["realtime"]["delay"] = 0;
                    $output["realtime"]["ttl"] = $output["realtime"]["duration"] + 30000;
                }
                break;
            case 7:
                //Minute
                if($relativ) $output["type"] = "realtime"; else $output["type"] = "time";
                $output["time"]["unit"] = "second";
                $output["time"]["stepSize"] = 10;

                if($relativ){
                    $output["realtime"]["duration"] = 60000;
                    $output["realtime"]["delay"] = 0;
                    $output["realtime"]["ttl"] = $output["realtime"]["duration"]+30000;
                }
                break;

        }

        $output["time"]["displayFormats"]["year"] = "YYYY";
        $output["time"]["displayFormats"]["month"] = "MMM YYYY";
        $output["time"]["displayFormats"]["day"] = "DD.MM";
        $output["time"]["displayFormats"]["hour"] = "HH";
        $output["time"]["displayFormats"]["minute"] = "HH:mm";
        $output["time"]["displayFormats"]["second"] = "ss";

        if($output["type"] != "realtime"){
            $starData = $this->GetCorrectStartDate();
            $this->SendDebug("GetArchivData", "Start_Date: ". $starData["start"]. " | End_Date: ".$starData["end"], 0);
            $output["min"] = ($starData["start"] * 1000);
            $output["max"] = (($starData["end"] + 1) * 1000);
        }

        //$output["realtime"]["pause"] = false;
        //$output["realtime"]["refresh"] = 180000;

        if($this->ReadPropertyInteger("axes_color") >= 0){
            $rgbdata = $this->HexToRGB($this->ReadPropertyInteger("axes_color"));
            $output["grid"]["borderColor"] = "rgba(" . $rgbdata["R"] .", " . $rgbdata["G"] .", " . $rgbdata["B"].", " . number_format($this->ReadPropertyFloat("axes_colorAlpha"), 2, '.', '').")";
            $output["grid"]["borderWidth"] = $this->ReadPropertyInteger("axes_lineWidth");

            $output["grid"]["tickColor"] = "rgba(" . $rgbdata["R"] .", " . $rgbdata["G"] .", " . $rgbdata["B"].", " . number_format($this->ReadPropertyFloat("axes_colorAlpha"), 2, '.', '').")";
            $output["grid"]["tickWidth"] = $this->ReadPropertyInteger("axes_lineWidth");
        }

        $output["display"] = $this->ReadPropertyBoolean("axes_display");

        $output["grid"]["drawBorder"] = $this->ReadPropertyBoolean("axes_drawBorder");
        $output["grid"]["drawOnChartArea"] = $this->ReadPropertyBoolean("axes_drawOnChartArea");
        $output["grid"]["drawTicks"] = $this->ReadPropertyBoolean("axes_drawTicks");

        if($this->ReadPropertyInteger("axes_fontColor") >= 0) {
            $rgbdata = $this->HexToRGB($this->ReadPropertyInteger("axes_fontColor"));
            $output["ticks"]["color"] = "rgba(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ")";
        }


        return $output;
    }

    private function GetArchivData(int $varId, int $highRes, int $offset, int $date_start, int $date_end, int $Aggregationsstufe, int $lastDatasets = 0, bool $jsconfig = true){
        $archiveControlID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
        $period = $this->GetValue("Period");
        $relativ = $this->GetValue("Relativ");
        $highResSteps = $this->ReadPropertyInteger("data_highResSteps");
        $counter = false;
        $precision = $this->ReadPropertyInteger("data_precision");
        if(AC_GetAggregationType($archiveControlID, $varId) == 1){
            $precision = 5;
            $counter = true;
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
        if($highRes <= $period){
            //hohe auflösung
            $archivData = AC_GetLoggedValues($archiveControlID, $varId, $date_start, $date_end, 0);
            $mode = "Value";
        }else{
            $archivData = AC_GetAggregatedValues($archiveControlID, $varId, $Aggregationsstufe, $date_start, $date_end, 0);
            $mode = "Avg";
        }

        $i = $highResSteps;
        $oldVal = 0;


        foreach ($archivData as $item) {
            $i--;
            if($i <= 0){
                $i = $highResSteps;
            }

            //highres datenreduktion
            if($mode == "Value" && $period != 7 && $i < $highResSteps){
                if($this->ReadPropertyBoolean("Debug"))
                    $this->SendDebug("GetArchivData", "(". $i . "|".$highResSteps.") Skip Data: " .date('d.m.Y H:i:s', $item["TimeStamp"]) . " => " . $item[$mode],0);
                continue;
            }

            //counterdatenverabeitung
            $val = $item[$mode];
            if($counter && $mode == "Value"){
                if($oldVal > 0.0){
                    $val = $oldVal - $item[$mode];
                    //$this->SendDebug("TEST", "cur=>" . $item[$mode]. " | OV=>" . $oldVal . " | val=>" . $val, 0);
                }else{
                    $cur = GetValue($varId);
                    $val = $val - $cur;
                }
                if($val < 0) $val = $val * -1;

                $oldVal = $item[$mode];
            }

            $val = round($val, $precision);

            $timestamp = $item["TimeStamp"] + $intval_offset;
            $output[] = array("x" => ($timestamp * 1000), "y" => $val);
        }

        //füllen wenn nicht relativ

        //sortieren aufsteigend nach timestamp
        $arr_timestamp = array_column($output, 'x');
        array_multisort($arr_timestamp, SORT_ASC , $output);

        //count datensets zum letzten datensatz hinzufügen
        if(count($output) > 0 && $highRes > $period){
            $output[count($output) - 1]["c"] = $lastDatasets;
        }

        //$this->SendDebug("GetArchivData", json_encode($output), 0);

        /*if(count($archivData) > 0){
            if(!$relativ){
                $output = array_merge($output, $this->FillUpData($archivData[0]["TimeStamp"] , $date_end));
            }
        }*/
        if($this->ReadPropertyBoolean("Debug"))
            $this->SendDebug("GetArchivData", "OUTPUT " .json_encode($output), 0);


        return $output;
    }
    private function GetConfigurationData(){
        $output = json_decode(IPS_GetConfiguration($this->InstanceID), true);

        $output["InstanceID"] = $this->InstanceID;
        $output["Period"] = $this->GetValue("Period");
        $output["Now"] = $this->GetValue("Now");
        $output["StartDate"] = $this->GetValue("StartDate");
        $output["Relativ"] = $this->GetValue("Relativ");

        $output["ID_Period"] = IPS_GetObjectIDByIdent("Period", $this->InstanceID);
        $output["ID_Now"] = IPS_GetObjectIDByIdent("Now", $this->InstanceID);
        $output["ID_StartDate"] = IPS_GetObjectIDByIdent("StartDate", $this->InstanceID);
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

        $output["Var_List"] = array();
        $output["Has_Bar"] = false;

        foreach (json_decode($output["Datasets"], true) as $item){
            if(IPS_VariableExists($item["Variable"])) $output["Var_List"][] = $item["Variable"];
            if($item["Type"] == "bar") $output["Has_Bar"] = true;
        }

        //remove Dataset
        unset($output["Datasets"]);

        return $output;
    }
    private function GetCorrectStartDate(int $date = 0){
        $date_start = new DateTime();
        $date_end = new DateTime();
        $relativ = $this->GetValue("Relativ");
        $now = $this->GetValue("Now");
        $Aggregationsstufe = 0;

        $period = $this->GetValue("Period");

        if($date != 0){
            $date_start->setTimestamp($date);
        }
        elseif($now) {
            $date_start = new DateTime('NOW');
        }else{
            $starttime = date('U', $this->GetValue("StartDate"));
            $date_start->setTimestamp($starttime);
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

                    $Aggregationsstufe = 4;
                    break;
                case 1:
                    //Jahr
                    $date_end->setDate($date_start->format('Y'), $date_start->format('m'), 1);
                    $date_end->setTime(0, 0, 0);

                    $date_start->setDate(($date_end->format('Y')), $date_start->format('m')-11, 1);
                    $date_start->setTime(0, 0, 0);

                    $Aggregationsstufe = 3;
                    break;
                case 2:
                    //Quartal
                    $date_end->setDate($date_start->format('Y'), $date_start->format('m'), 1);
                    $date_end->setTime(0, 0, 0);

                    $date_start->setDate(($date_end->format('Y')), $date_start->format('m')-2, 1);
                    $date_start->setTime(0, 0, 0);

                    $Aggregationsstufe = 3;
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

                    $Aggregationsstufe = 1;
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
        }
        elseif(IPS_GetObject($id)["ObjectType"] == 5){
            //media
            $config = array();
            try {
                $chartData = json_decode(base64_decode(IPS_GetMediaContent($id)), true);


                $lineType = "line";
                if($chartData["type"] == "bar") $lineType = "bar";

                foreach ($chartData["datasets"] as $chartItem){
                    $c_Item = array();

                    $this->SendDebug("LoadOtherConfiguration", json_encode($chartItem), 0);

                    $title = "";
                    if(array_key_exists("title", $chartItem))$title = $chartItem["title"];

                    $c_Item["Order"] = 0;
                    $c_Item["Type"] = $lineType;
                    $c_Item["Variable"] = $chartItem["variableID"];
                    $c_Item["Title"] = $title;
                    $c_Item["Profile"] = $chartData["axes"][$chartItem["axis"]]["profile"];
                    $c_Item["Side"] = $chartData["axes"][$chartItem["axis"]]["side"];
                    $c_Item["Offset"] = $chartItem["timeOffset"];

                    $hex = str_replace('#', '',  $chartItem["fillColor"]);
                    if($hex == "clear"){
                        $c_Item["BackgroundColor"] = -1;
                    }else{
                        $c_Item["BackgroundColor"] = hexdec($hex);
                    }


                    $hex = str_replace('#', '',  $chartItem["strokeColor"]);
                    if($hex == "clear"){
                        $c_Item["BorderColor"] = -1;
                    }else {
                        $c_Item["BorderColor"] = hexdec($hex);
                    }
                    $c_Item["BackgroundColor Alpha"] = 0.5;
                    $c_Item["BorderColor Alpha"] = 1.0;
                    $c_Item["Border Width"] = 2;
                    $c_Item["Dash"] = "";
                    $c_Item["HighRes"] = 7;

                    $config[] = $c_Item;
                }

                $confData = json_decode(IPS_GetConfiguration($this->InstanceID), true);

                $confData["title_text"] = IPS_GetObject($id)["ObjectName"];
                $confData["Datasets"]= json_encode($config);

                IPS_SetConfiguration($this->InstanceID, json_encode($confData));
                IPS_ApplyChanges($this->InstanceID);
            } catch (Exception $e){
                echo 'Exception abgefangen: ',  $e->getMessage(), "\n";
            }
        }else return "A Instance/Chart must be selected!";
    }
}

?>