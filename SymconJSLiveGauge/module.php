<?
include_once (__DIR__ . '/../SymconJSLive/libs/JSLiveModule.php');

class SymconJSLiveGauge extends JSLiveModule{
    public function Create() {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent("{9FFF3FC0-FD51-C289-FA36-BC1C370946CF}");

        $this->RegisterPropertyInteger("variable", 0);
        $this->RegisterPropertyInteger("min", 0);
        $this->RegisterPropertyInteger("max", 1000);
        $this->RegisterPropertyString("template", "CanvasGauges-Radial");

        //Expert
        $this->RegisterPropertyBoolean("Debug", false);
        $this->RegisterPropertyInteger("TemplateScriptID", 0);

        //title
        $this->RegisterPropertyString("title_text", "");
        $this->RegisterPropertyBoolean("title_display", false);
        $this->RegisterPropertyInteger("title_fontSize", 20);
        $this->RegisterPropertyInteger("title_fontColor", 0);

        //Plate
        $this->RegisterPropertyBoolean("plate_display", false);
        $this->RegisterPropertyString("plate_unit", "");
        $this->RegisterPropertyInteger("plate_colorPlate", 16777215);
        $this->RegisterPropertyInteger("plate_colorPlate_Alpha", 1);
        $this->RegisterPropertyInteger("plate_colorPlateEnd", 16777215);
        $this->RegisterPropertyInteger("plate_colorPlateEnd_Alpha", 1);

        //Needle
        $this->RegisterPropertyBoolean("needle_display", false);
        $this->RegisterPropertyString("needle_Type", "arrow"); //"line"
        $this->RegisterPropertyInteger("needle_fontSize", 14);
        $this->RegisterPropertyInteger("needle_start", 0);
        $this->RegisterPropertyInteger("needle_end", 80);
        $this->RegisterPropertyInteger("needle_width", 2);
        $this->RegisterPropertyInteger("needle_colorNeedle", 0);
        $this->RegisterPropertyInteger("needle_colorNeedle_Alpha", 1);
        $this->RegisterPropertyInteger("needle_colorNeedleEnd", 0);
        $this->RegisterPropertyInteger("needle_colorNeedleEnd_Alpha", 1);

        //Valuebox
        $this->RegisterPropertyBoolean("valuebox_display", false);
        $this->RegisterPropertyInteger("valuebox_fontSize", 14);
        $this->RegisterPropertyInteger("valuebox_colorValueBoxBackground", 0);
        $this->RegisterPropertyInteger("valuebox_colorValueBoxBackground_Alpha", 1);

        //Progressbar
        $this->RegisterPropertyBoolean("progressbar_display", false);
        $this->RegisterPropertyInteger("progressbar_barWidth", 5);
        $this->RegisterPropertyInteger("progressbar_barShadow", 1);
        $this->RegisterPropertyInteger("progressbar_colorBar", 0);
        $this->RegisterPropertyInteger("progressbar_colorBar_Alpha", 1);
        $this->RegisterPropertyInteger("progressbar_colorBarProgress", 0);
        $this->RegisterPropertyInteger("progressbar_colorBarProgress_Alpha", 1);

        //Ticks
        $this->RegisterPropertyBoolean("ticks_strokeTicks", false);
        $this->RegisterPropertyInteger("ticks_highlightsWidth", 5);
        $this->RegisterPropertyInteger("ticks_minorTicks", 5);
        $this->RegisterPropertyInteger("ticks_colorMajorTick", 0);
        $this->RegisterPropertyInteger("ticks_colorMinorTicks", 0);
        $this->RegisterPropertyInteger("ticks_colorUnits", 0);
        $this->RegisterPropertyInteger("ticks_colorNumbers", 0);

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

        $this->SetReceiveDataFilter('.*instance\\\":[ \\\"]*'.$this->InstanceID.'[\\\”]*.*');
    }

    public function ReceiveData($JSONString) {
        $jsonData = json_decode($JSONString, true);
        $buffer = json_decode($jsonData['Buffer'], true);


        //if($buffer["instance"] != $this->InstanceID) return;
        //$this->SendDebug("ReceiveData", $jsonData['Buffer']. " =>" . $this->InstanceID, 0);

        switch($buffer['cmd']) {
            case "exportConfiguration":
                return $this->ExportConfiguration();
            case "getContend":
                return json_encode(array("output" => $this->GetWebpage(), "viewport" => $this->ReadPropertyBoolean("viewport_enable")));
            case "getData":
                return $this->GetData($buffer['queryData']);
            default:
                $this->SendDebug("ReceiveData", "ACTION " . $buffer['cmd'] . " FOR THIS MODULE NOT DEFINED!", 0);
                break;
        }

    }
    public function GetWebpage(){
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

            $o_item["Value"] = GetValue($var);

            if($this->ReadPropertyBoolean("Debug"))
                $this->SendDebug("GetData", json_encode($querydata), 0);

            $output[] = $o_item;
        }

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
        if(IPS_VariableExists($this->ReadPropertyInteger("variable"))){
            $val = GetValue($this->ReadPropertyInteger("variable"));
        }else{
            $this->SendDebug("ReplacePlaceholder", "Variable (".$this->ReadPropertyInteger("variable").") NOT EXIST!", 0);
        }
        $htmlData = str_replace("{VALUE}", number_format($val, 2, '.', ''), $htmlData);

        return $htmlData;
    }

    private function GenerateTicks(){
        $ticks = json_decode($this->ReadPropertyString("Ticks"), true);
        $min = $this->ReadPropertyInteger("min");
        $max = $this->ReadPropertyInteger("max");
        $majorticks = array();

        $majorticks[] = $min;
        foreach ($ticks as $tick) {
            $majorticks[] = $tick["Value"];
        }
        $majorticks[] = $max;
        return $majorticks;
    }

    private function GenerateHighlights(){
        $arr = json_decode($this->ReadPropertyString("Highlights"), true);
        $highlights = array();

        foreach ($arr as $item){
            $highlight_item = array();
            $highlight_item["from"] = $item["From"];
            $highlight_item["to"] = $item["To"];

            $rgbdata = $this->HexToRGB($item["HighlightColor"]);
            $highlight_item["color"] = "rgba(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ", " . number_format($item["HighlightColor_Alpha"], 2, '.', '') . ")";

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
                    $output[$key] = "rgba(" . $rgbdata["R"] . ", " . $rgbdata["G"] . ", " . $rgbdata["B"] . ", ".$output[$key."_Alpha"].")";
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