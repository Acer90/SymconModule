<?

class SymconJSLiveChart extends IPSModule{

    public function Create() {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent("{9FFF3FC0-FD51-C289-FA36-BC1C370946CF}");
        $this->RegisterPropertyBoolean("Debug", false);


        $this->RegisterPropertyInteger("TemplateScriptID", 0);
        $this->RegisterPropertyString("title_text", "");

        //title
        $this->RegisterPropertyBoolean("title_display", false);
        $this->RegisterPropertyString("title_position", "top");
        $this->RegisterPropertyInteger("title_fontSize", 12);
        $this->RegisterPropertyInteger("title_fontColor", 0);

        //Axes
        $this->RegisterPropertyBoolean("axes_display", true);
        $this->RegisterPropertyBoolean("axes_showLabel", true);
        $this->RegisterPropertyBoolean("axes_drawBorder", true);
        $this->RegisterPropertyInteger("axes_lineWidth", 1);
        $this->RegisterPropertyInteger("axes_labelText", 2);
        $this->RegisterPropertyInteger("axes_color", 0);
        $this->RegisterPropertyFloat("axes_colorAlpha", 0);

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
        $this->RegisterPropertyInteger("legend_boxWidth", 40);

        //Tooltips
        $this->RegisterPropertyBoolean("tooltips_enabled", true);
        $this->RegisterPropertyString("tooltips_position", "average");
        $this->RegisterPropertyString("tooltips_mode", "index");
        $this->RegisterPropertyInteger("tooltips_fontSize", 12);
        $this->RegisterPropertyInteger("tooltips_fontColor", 65535);
        $this->RegisterPropertyInteger("tooltips_backgroundColor", 0);
        $this->RegisterPropertyInteger("tooltips_cornerRadius", 5);

        //Data
        $this->RegisterPropertyInteger("data_highResSteps", 1);


        //dataset
        $this->RegisterPropertyString("Datasets", "[]");

    }
    public function ApplyChanges() {
        //Never delete this line!
        parent::ApplyChanges();

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

        $this->RegisterVariableInteger("Period",  $this->Translate("Period"), "JSLive_Periode", 0);
        $this->RegisterVariableBoolean("Now",  $this->Translate("Now"), "JSLive_Now", 1);
        $this->RegisterVariableBoolean("Relativ",  $this->Translate("Relativ"), "~Switch", 1);

        $this->RegisterVariableInteger("Offset", $this->Translate("Offset"), "", 98);
        $this->RegisterVariableInteger("StartDate", $this->Translate("Start Date"), "~UnixTimestamp", 99);

        $this->EnableAction("Offset");
        $this->EnableAction("Now");
        $this->EnableAction("StartDate");
        $this->EnableAction("Relativ");
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
        $jsonData = json_decode($JSONString, true);
        $buffer = json_decode($jsonData['Buffer'], true);
        //$this->SendDebug("ReceiveData", $jsonData['Buffer']. " =>" . $buffer["instance"], 0);

        if($buffer["instance"] != $this->InstanceID) return;


        switch($buffer['cmd']){
            case "getContend":
                return $this->GetWebpage();
            case "getUpdate":
                return  $this->GetUpdate();
            case "getData":
                return $this->GetData($buffer['querydata']);
        }

    }
    public function GetWebpage(){
        $scriptID = $this->ReadPropertyInteger("TemplateScriptID");
        if(!IPS_ScriptExists($scriptID)){
            $this->SendDebug('GetWebpage', 'SCRIPT NOT FOUND!', 0);
            return "";
        }

        $scriptData = IPS_GetScriptContent($scriptID);
        if($scriptData = ""){
            $this->SendDebug('GetWebpage', 'SCRIPT IS EMPTY!', 0);
        }

        $scriptData = $this->ReplacePlaceholder($scriptData);

        return $scriptData;
    }
    public function GetUpdate(){
        $scriptID = $this->ReadPropertyInteger("TemplateScriptID");
        if(!IPS_ScriptExists($scriptID)){
            $this->SendDebug('GetWebpage', 'SCRIPT NOT FOUND!', 0);
            return "";
        }

        $updateData = array();

        $data = $this->GenerateDataSet();
        $updateData["DATASETS"] = $data["datasets"];
        $updateData["AXES"] = $data["charts"];
        $updateData["CONFIG"] = $this->GetConfigurationData();

        $updateData["XAXES"] = $this->GenerateXAxesData();


        return json_encode($updateData);
    }
    public function GetData($querydata){
        $output = array();
        $datasets = json_decode($this->ReadPropertyString("Datasets"), true);
        if(!array_key_exists("var", $querydata)) {
            $this->SendDebug("GetData", "PARAMETER VARIABLE NOT SET!(" . json_encode($querydata). ")", 0);
            return json_encode($output);
        }
        if(!IPS_VariableExists($querydata["var"])){
            $this->SendDebug("GetData", "VARIABLE NOT EXIST!", 0);
            return json_encode($output);
        }

        $key = array_search($querydata["var"], array_column($datasets, 'Variable'));
        if($key === false){
            $this->SendDebug("GetData", "VARIABLE NOT IN INSTANCE!", 0);
            return json_encode($output);
        }

        $output["Value"] = GetValue($querydata["var"]);

        if($this->ReadPropertyBoolean("Debug"))
            $this->SendDebug("GetData", json_encode($querydata), 0);

        if(array_key_exists("start", $querydata) && array_key_exists("end", $querydata)){
            $hires = 7;
            $offset = 0;
            $start = 0;
            $end = 0;
            $Aggregationsstufe = 7;
            $period = $this->GetValue("Period");

            if(array_key_exists("hires", $querydata)) $hires = $querydata["hires"];
            if(array_key_exists("offset", $querydata)) $offset = $querydata["offset"];
            if(array_key_exists("start", $querydata)) $start = $querydata["start"];
            if(array_key_exists("end", $querydata)) $end = $querydata["end"];

            if($hires != 7){
                switch ($period){
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

            $output["archiv"] = $this->GetArchivData($querydata["var"], $hires, $offset, $start, $end, $Aggregationsstufe, false);

            return json_encode($output);
        }
    }


    private function ReplacePlaceholder($htmlData){
        $htmlData = str_replace("{TITLE_TEXT}", $this->ReadPropertyString("title_text"), $htmlData);

        //Title
        $htmlData = str_replace("{TITLE}", $this->json_encode_advanced($this->GenerateTitleData()), $htmlData);

        //datasets and axis
        $data = $this->GenerateDataSet();
        $htmlData = str_replace("{DATASETS}", $this->json_encode_advanced($data["datasets"]), $htmlData);
        $htmlData = str_replace("{AXES}", $this->json_encode_advanced($data["charts"]), $htmlData);

        //Legend
        $htmlData = str_replace("{LEGEND}", $this->json_encode_advanced($this->GenerateLegendData()), $htmlData);

        //Legend
        $htmlData = str_replace("{TOOLTIPS}", $this->json_encode_advanced($this->GenerateTooltipData()), $htmlData);

        //Legend
        $htmlData = str_replace("{CONFIG}", $this->json_encode_advanced($this->GetConfigurationData()), $htmlData);

        //xAxes
        $htmlData = str_replace("{XAXES}", $this->json_encode_advanced($this->GenerateXAxesData()), $htmlData);

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
            $output["labels"]["fontColor"] = "rgb(" . $rgbdata["R"] .", " . $rgbdata["G"] .", " . $rgbdata["B"]. ")";
        }
        $output["labels"]["fontSize"] = $this->ReadPropertyInteger("legend_fontSize");
        $output["labels"]["boxWidth"] = $this->ReadPropertyInteger("legend_boxWidth");

        return $output;

    }
    private function GenerateDataSet(){
        $output["datasets"] = array();
        $output["charts"] = array();
        $datasets = json_decode($this->ReadPropertyString("Datasets"),true);
        if(!is_array($datasets)) return "{}";

        foreach($datasets as $item){
            $singelOutput = array();
            $singelOutput["variable"] = $item["Variable"];
            $singelOutput["label"] = $item["Title"];
            $singelOutput["type"] = $item["Type"];
            $singelOutput["order"] = $item["Order"];

            $rgbdata = $this->HexToRGB($item["BackgroundColor"]);
            $singelOutput["backgroundColor"] = "rgba(" . $rgbdata["R"] .", " . $rgbdata["G"] .", " . $rgbdata["B"].", " . number_format($item["BackgroundColor_Alpha"], 2, '.', '').")";

            $rgbdata = $this->HexToRGB($item["BorderColor"]);
            $singelOutput["borderColor"] = "rgba(" . $rgbdata["R"] .", " . $rgbdata["G"] .", " . $rgbdata["B"].", " . number_format($item["BorderColor_Alpha"], 2, '.', '').")";
            $singelOutput["borderWidth"] = $item["BorderWidth"];

            $singelOutput["highRes"] = $item["HighRes"];
            $singelOutput["offset"] = $item["Offset"];

            //datenabrufen
            $singelOutput["data"] = $this->GetArchivData($item["Variable"], $item["HighRes"], $item["Offset"]);
            //$singelOutput["data"][] = array("x" => 1615732860000, "y" => 658,78);
            //$singelOutput["data"][] = array("x" => 1615732900000, "y" => 758,78);

            if(!empty($item["Dash"])){
                $dashData = @json_decode($item["Dash"], true);
                if ($dashData !== null){
                    $singelOutput["borderDash"] = $dashData;
                }
            }

            //Axis
            $singelOutput["yAxisID"] = $item["Profile"];
            $digits = 2;

            //Pointstyle
            $singelOutput["pointRadius"] = $this->ReadPropertyInteger("point_radius");
            $singelOutput["pointHoverRadius"] = $this->ReadPropertyInteger("point_hoverRadius");
            $singelOutput["pointStyle"] = $this->ReadPropertyString("points_Style");

            //falls noch nicht vorhanden anlegen
            if(!key_exists($item["Profile"], $output["charts"])){
                $axisoutput = array();
                $axisoutput["type"] = "linear";
                $axisoutput["display"] = $this->ReadPropertyBoolean("axes_display");
                $axisoutput["id"] = $item["Profile"];
                $axisoutput["position"] = $item["Side"];

                $axisoutput["MinValue"] = 0;
                $axisoutput["MaxValue"] = 0;

                if($this->ReadPropertyInteger("axes_color") >= 0){
                    $rgbdata = $this->HexToRGB($this->ReadPropertyInteger("axes_color"));
                    $axisoutput["gridLines"]["color"] = "rgba(" . $rgbdata["R"] .", " . $rgbdata["G"] .", " . $rgbdata["B"].", " . number_format($this->ReadPropertyFloat("axes_colorAlpha"), 2, '.', '').")";
                    $axisoutput["gridLines"]["LineWidth"] = $this->ReadPropertyInteger("axes_lineWidth");
                }

                if(!$this->ReadPropertyBoolean("axes_drawBorder")){
                    $axisoutput["gridLines"]["display"] = false;
                    $axisoutput["gridLines"]["drawBorder"] = false;
                }

                if(IPS_VariableProfileExists($item["Profile"])){
                    $profilData = IPS_GetVariableProfile($item["Profile"]);
                    if($profilData["MaxValue"] != 0 || $profilData["MinValue"]  != 0){
                        $axisoutput["ticks"]["suggestedMax"] = $profilData["MaxValue"];
                        $axisoutput["ticks"]["suggestedMin"] = $profilData["MinValue"];

                        $axisoutput["MinValue"] = $profilData["MaxValue"];
                        $axisoutput["MaxValue"] = $profilData["MinValue"];
                    }

                    if($profilData["StepSize"] > 0){
                        $axisoutput["ticks"]["stepSize"] = $profilData["StepSize"];
                    }

                    $digits = $profilData["Digits"];

                    $axisoutput["scaleLabel"]["display"] = $this->ReadPropertyBoolean("axes_showLabel");
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
                    $axisoutput["scaleLabel"]["labelString"] = $l_text;

                }
                $output["charts"][] = $axisoutput;
            }

            $singelOutput["digits"] = $digits;


            $output["datasets"][] = $singelOutput;
        }

        return $output;
    }
    private function GenerateXAxesData(){
        $output = array();

        //
        //$output["distribution"] = "series";
        $output["time"]["tooltipFormat"] = "DD.MM.YYYY HH:mm:ss";


        $period = $this->GetValue("Period");
        switch ($period) {
            case 0:
                //Dekade
                $output["type"] = "time";
                $output["time"]["unit"] = "year";
                $output["time"]["unitStepSize"] = 1;
                break;
            case 1:
                //Jahr
                $output["type"] = "time";
                $output["time"]["unit"] = "month";
                $output["time"]["unitStepSize"] = 2;
                break;
            case 2:
                //Quartal
                $output["type"] = "time";
                $output["time"]["unit"] = "month";
                $output["time"]["unitStepSize"] = 1;
                break;
            case 3:
                //Monat
                $output["type"] = "time";
                $output["time"]["unit"] = "day";
                $output["time"]["unitStepSize"] = 3;
                break;
            case 4:
                //Woche
                $output["type"] = "time";
                $output["time"]["unit"] = "day";
                $output["time"]["unitStepSize"] = 1;
                break;
            case 5:
                //Tag
                $output["type"] = "time";
                $output["time"]["unit"] = "hour";
                $output["time"]["unitStepSize"] = 3;
                break;
            case 6:
                //Stunde
                $output["type"] = "time";
                $output["time"]["unit"] = "minute";
                $output["time"]["unitStepSize"] = 5;

                $output["realtime"]["duration"] = 3600000;
                $output["realtime"]["delay"] = 0;
                $output["realtime"]["ttl"] = $output["realtime"]["duration"]+30000;
                break;
            case 7:
                //Minute
                $output["type"] = "time";
                $output["time"]["unit"] = "second";
                $output["time"]["unitStepSize"] = 10;

                $output["realtime"]["duration"] = 60000;
                $output["realtime"]["delay"] = 0;
                $output["realtime"]["ttl"] = $output["realtime"]["duration"]+30000;
                break;

        }

        $output["time"]["displayFormats"]["second"] = "ss";
        $output["time"]["displayFormats"]["minute"] = "HH:mm";
        $output["time"]["displayFormats"]["hour"] = "HH";
        $output["time"]["displayFormats"]["day"] = "DD.MM";
        $output["time"]["displayFormats"]["week"] = "ll";
        $output["time"]["displayFormats"]["month"] = "MMM YYYY";
        $output["time"]["displayFormats"]["quarter"] = "[Q]Q - YYYY";
        $output["time"]["displayFormats"]["year"] = "YYYY";

        //$output["realtime"]["pause"] = false;
        //$output["realtime"]["refresh"] = 180000;


        return array($output);
    }
    private function GetArchivData($varId, $highRes, $offset = 0, $startdate = 0, $enddate = 0, $agstufe = 7, $jsconfig = true){
        $archiveControlID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
        $period = $this->GetValue("Period");
        $relativ = $this->GetValue("Relativ");
        $highResSteps = $this->ReadPropertyInteger("data_highResSteps");

        if($startdate > 0 && $enddate > 0){
            $date_start = $startdate;
            $date_end = $enddate;
            $Aggregationsstufe = $agstufe;
        }else{
            $starData = $this->GetCorrectStartDate();
            $date_start = $starData["start"];
            $date_end = $starData["end"];
            $Aggregationsstufe = $starData["stufe"];
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
        foreach ($archivData as $item) {
            $i--;
            if($i <= 0){
                $i = $highResSteps;
            }

            //highres datenreduktion
            if($mode == "Value" && $period != 7 && $i < $highResSteps){
                $this->SendDebug("GetArchivData", "(". $i . "|".$highResSteps.") Skip Data: " .date('d.m.Y H:i:s', $item["TimeStamp"]) . " => " . $item[$mode],0);
                continue;
            }

            if ($jsconfig){
                $val = number_format($item[$mode], 2, '.', '');
            }else{
                $val = round($item[$mode], 2);
            }


            $timestamp = $item["TimeStamp"] + $intval_offset;
            $output[] = array("x" => ($timestamp * 1000), "y" => $val);
        }

        //füllen wenn nicht relativ

        //sortieren aufsteigend nach timestamp
        $arr_timestamp = array_column($output, 'x');
        array_multisort($arr_timestamp, SORT_ASC , $output);

        if(count($archivData) > 0){
            if(!$relativ){
                $output = array_merge($output, $this->FillUpData($archivData[0]["TimeStamp"] , $date_end));
            }
        }

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

        //remove Dataset
        unset($output["Datasets"]);

        return $output;
    }
    private function GetCorrectStartDate($date = 0){
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
        if($this->ReadPropertyBoolean("Debug"))
            $this->SendDebug("GetCorrectStartDate", "Realtiv: ".$relativ." | Period: ".$period." | Start_Date: ".$date_start->format('d.m.Y H:i:s'). " | End_Date: ".$date_end->format('d.m.Y H:i:s'), 0);

        return array("start" => $date_start->getTimestamp(), "end" => $date_end->getTimestamp(), "stufe" => $Aggregationsstufe);
    }
    private function FillUpData($start_unixTimeStamp, $end_unixTimeStamp){
        $period = $this->GetValue("Period");
        $date_start = new DateTime();
        $date_end = new DateTime();
        $intval = new DateInterval("P10Y");
        $output = array();

        $date_start->setTimestamp($start_unixTimeStamp);
        $date_end->setTimestamp($end_unixTimeStamp);


        //bereiingen des letzten eintrages und intervall bestimmen
        switch($period){
            case 0:
                //Dekade
                $date_start->setDate(($date_start->format('Y') + 1), 1, 1);
                $date_start->setTime(0, 0, 0);
                $intval = new DateInterval("P1Y");
                break;
            case 1:
            case 2:
                //Jahr
                //Quartal
                $date_start->setDate(($date_end->format('Y')), ($date_start->format('m') + 1), 1);
                $date_start->setTime(0, 0, 0);
                $intval = new DateInterval("P1M");
                break;
            case 3:
            case 4:
                //Monat
                //Woche
                $date_start->setDate(($date_end->format('Y')), $date_start->format('m'), ($date_start->format('d') + 1));
                $date_start->setTime(0, 0, 0);
                $intval = new DateInterval("P1D");
                break;
            case 5:
                //Tag
                $date_start->setDate(($date_end->format('Y')), $date_start->format('m'), $date_start->format('d'));
                $date_start->setTime(($date_start->format('H') + 1), 0, 0);
                $intval = new DateInterval("PT1H");
                break;
            case 6:
                //Stunde
                $date_start->setDate(($date_end->format('Y')), $date_start->format('m'), $date_start->format('d'));
                $date_start->setTime($date_start->format('H'), ($date_start->format('i') + 1), 0);
                $intval = new DateInterval("PT1M");
                break;
            case 7:
                //Minute
                $date_start->setDate(($date_end->format('Y')), $date_start->format('m'), $date_start->format('d'));
                $date_start->setTime($date_start->format('H'), $date_start->format('i'), ($date_start->format('s')+ 1));
                $intval = new DateInterval("PT1S");
                break;
        }

        if($this->ReadPropertyBoolean("Debug")) $this->SendDebug("FillUpData", "Start_Date: ".$date_start->format('d.m.Y H:i:s')."(".$date_start->getTimestamp().") | End_Date".$date_end->format('d.m.Y H:i:s')."(".$date_end->getTimestamp().")", 0);

        while($date_start <= $date_end){
            //array füllen
            $output[] = array("x" => ($date_start->getTimestamp() * 1000), "y" => 0, "dummy" => true);

            //datum inkrementieren
            $date_start->add($intval);
        }

        return $output;
    }
    private function GetOffsetDate($start_unixTimeStamp, $end_unixTimeStamp, $offset){
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


    private function json_encode_advanced(array $arr, $sequential_keys = false, $quotes = false, $beautiful_json = true) {

        $output = $this->isAssoc($arr) ? "{" : "[";
        $count = 0;
        foreach ($arr as $key => $value) {

            if ($this->isAssoc($arr) || (!$this->isAssoc($arr) && $sequential_keys == true )) {
                $output .= ($quotes ? '"' : '') . $key . ($quotes ? '"' : '') . ' : ';
            }

            if (is_array($value)) {
                $output .= $this->json_encode_advanced($value, $sequential_keys, $quotes, $beautiful_json);
            }
            else if (is_bool($value)) {
                $output .= ($value ? 'true' : 'false');
            }
            else if (is_numeric($value)) {
                $output .= $value;
            }
            else {
                $output .= ($quotes || $beautiful_json ? '"' : '') . $value . ($quotes || $beautiful_json ? '"' : '');
            }

            if (++$count < count($arr)) {
                $output .= ', ';
            }
        }

        $output .= $this->isAssoc($arr) ? "}" : "]";

        return $output;
    }
    private function isAssoc(array $arr) {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
    private function HexToRGB(int $Hex){
        $r   = floor($Hex/65536);
        $g  = floor(($Hex-($r*65536))/256);
        $b = $Hex-($g*256)-($r*65536);

        return array("R" => $r, "G" => $g, "B" => $b);
    }

    public function LoadOtherConfiguration($id){
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
    public function GetLink(bool $local = true){
        $sendData = array("InstanceID" => $this->InstanceID, "Type" => "GetLink", "local" => $local);

        $pData = $this->SendDataToParent(json_encode([
            'DataID' => "{751AABD7-E31D-024C-5CC0-82AC15B84095}",
            'Buffer' => utf8_encode(json_encode($sendData)),
        ]));

        return $pData;
    }
}

?>