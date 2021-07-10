<?php
include_once (__DIR__ . '/../SymconJSLive/libs/JSLiveModule.php');

class SymconJSLiveProgressbar extends JSLiveModule{
    public function Create() {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent("{9FFF3FC0-FD51-C289-FA36-BC1C370946CF}");

        $this->RegisterPropertyInteger("Variable", 0);
        $this->RegisterPropertyString("Type", "stroke");

        //date
        $this->RegisterPropertyFloat("data_min", 0);
        $this->RegisterPropertyFloat("data_max", 100);
        $this->RegisterPropertyFloat("data_precision", 1);
        $this->RegisterPropertyFloat("data_precisionCustom", 0);
        $this->RegisterPropertyFloat("data_animationDuration", 0.5);
        $this->RegisterPropertyFloat("data_animationTransitionIn", 0.5);

        //Shape
        $this->RegisterPropertyString("shape_preset", "line");
        $this->RegisterPropertyString("shape_svg", "");
        $this->RegisterPropertyString("shape_path", "");

        //Stroke
        $this->RegisterPropertyString("stroke_dir", "normal");
        $this->RegisterPropertyString("stroke_lincap", "");
        $this->RegisterPropertyInteger("stroke_width", 5);
        $this->RegisterPropertyInteger("stroke_color", 0);
        $this->RegisterPropertyFloat("stroke_color_Alpha", 1.0);
        $this->RegisterPropertyInteger("stroke_trailWidth", 5);
        $this->RegisterPropertyInteger("stroke_trailColor", 0);
        $this->RegisterPropertyFloat("stroke_trailColor_Alpha", 1.0);

        $this->RegisterPropertyInteger("stroke_Dash1", 0);
        $this->RegisterPropertyInteger("stroke_Dash2", 0);

        //Fill
        $this->RegisterPropertyString("fill_backgroundFile", "");
        $this->RegisterPropertyString("fill_backgroundFileType", "image/jpeg");
        $this->RegisterPropertyString("fill_dir", "ltr");
        $this->RegisterPropertyInteger("fill_color", 0);
        $this->RegisterPropertyFloat("fill_color_Alpha", 1.0);
        $this->RegisterPropertyInteger("fill_backgroundColor", 150000);
        $this->RegisterPropertyFloat("fill_backgroundColor_Alpha", 1.0);
        $this->RegisterPropertyInteger("fill_backgroundExtrude", 1);

        //fonts
        $this->RegisterPropertyBoolean("style_fontDisplay", true);
        $this->RegisterPropertyString("style_fontPosition", "bottom");
        $this->RegisterPropertyInteger("style_fontSize", 12);
        $this->RegisterPropertyInteger("style_fontColor", 0);
        $this->RegisterPropertyString("style_fontFamily", "");

        //Expert
        $this->RegisterPropertyBoolean("Debug", false);
        $this->RegisterPropertyBoolean("EnableCache", true);
        $this->RegisterPropertyBoolean("CreateOutput", true);
        $this->RegisterPropertyBoolean("CreateIPSView", true);
        $this->RegisterPropertyInteger("TemplateScriptID", 0);
        $this->RegisterPropertyBoolean("EnableViewport", true);
        $this->RegisterPropertyInteger("IFrameHeight", 0);

        $this->RegisterPropertyString("override_stroke", "");
        $this->RegisterPropertyString("override_fill", "");
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
            case "getSVG":
                return $this->GetSVG();
            case "getFillImg":
                return $this->GetFillImg();
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
            $scriptData = file_get_contents(__DIR__ ."/../SymconJSLive/templates/Progressbar.html");
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
    private function GetData(array $querydata){
        $output = array();
        $output["Variable"] = $this->ReadPropertyInteger("Variable");
        if(IPS_VariableExists($output["Variable"])){
            $output["Value"] =  GetValue($this->ReadPropertyInteger("Variable"));
        }else{
            $this->SendDebug('SetData', "VARIABLE NOT EXIST!", 0);
            $output["Value"] = 0;
        }
        return json_encode($output);
    }
    private function GetSVG(){
        $output = $this->ReadPropertyString("shape_svg");
        if(!empty($output)){
            $output = base64_decode($output);
        }

        return $output;
    }
    private function GetFillImg(){
        $output = $this->ReadPropertyString("fill_backgroundFile");
        $type = $this->ReadPropertyString("fill_backgroundFileType");

        return json_encode(array("Contend" => $output, "Type" => $type));
    }

    private function ReplacePlaceholder(string $htmlData){
        //configuration Data
        $htmlData = str_replace("{CONFIG}", $this->json_encode_advanced($this->GetConfigurationData()), $htmlData);

        //Value
        $Variable = $this->ReadPropertyInteger("Variable");
        if(IPS_VariableExists($Variable)){
            $value = GetValue($Variable);
            if(is_float($value)){
                $precision = $this->ReadPropertyFloat("data_precision");
                $current = $precision - floor($precision);
                for ($decimals = 0; ceil($current); $decimals++) {
                    $current = ($value * pow(10, $decimals + 1)) - floor($value * pow(10, $decimals + 1));
                }
                $value = number_format($value, $decimals, '.', '');
            }
            $htmlData = str_replace("{VALUE}", $value, $htmlData);
        }else {
            $htmlData = str_replace("{VALUE}", 0, $htmlData);
        }

        $arr = array($this->ReadPropertyString("style_fontFamily"));
        $htmlData = str_replace("{FONTS}", $this->LoadFonts($arr), $htmlData);

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
                    $output[$key] = "rgba(" . $rgbdata["R"] . "," . $rgbdata["G"] . "," . $rgbdata["B"] . ",".number_format($output[$key."_Alpha"], 2, '.', '').")";
                }else{
                    $rgbdata = $this->HexToRGB($val);
                    $output[$key] = "rgb(" . $rgbdata["R"] . "," . $rgbdata["G"] . "," . $rgbdata["B"] . ")";
                }
            }
        }

        $rgbdata = $this->HexToRGB($this->ReadPropertyInteger("fill_color"));
        $output["fill_color_rgb"] = $rgbdata;

        $rgbdata = $this->HexToRGB($this->ReadPropertyInteger("stroke_color"));
        $output["stroke_color_rgb"] = $rgbdata;

        //sufix und Prefix abrufen
        $output["suffix"] = "";
        $output["prefix"] = "";

        if(!empty($output["shape_svg"])){
            $output["shape_svg"] = true;
        }else{
            $output["shape_svg"] = false;
        }


        if(!empty($output["fill_backgroundFile"])){
            $output["fill_backgroundFile"] = true;
        }else{
            $output["fill_backgroundFile"] = false;
        }

        //$output["Test"] = IPS_GetVariable($output["Variable"]);
        if(IPS_VariableExists($output["Variable"]) && !empty(IPS_GetVariable($output["Variable"])["VariableProfile"])){
            $profil = IPS_GetVariable($output["Variable"])["VariableProfile"];
            //$output["profil"] = $profil;

            if(IPS_VariableProfileExists($profil)){
                $profildata = IPS_GetVariableProfile($profil);
                $output["suffix"] = $profildata["Suffix"];
                $output["prefix"] = $profildata["Prefix"];
            }
        }


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
            $confData["Variable"] = $this->ReadPropertyInteger("Variable");

            IPS_SetConfiguration($this->InstanceID, json_encode($confData));
            IPS_ApplyChanges($this->InstanceID);
        }else return "A Instance must be selected!";
    }

    public function LoadSvg(string $base64){
        $string = base64_decode($base64);

        if(empty($string)) return "No file selected!";

        $confData = json_decode(IPS_GetConfiguration($this->InstanceID), true);


        $xml = simplexml_load_string($string);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);

        $output = $this->LoadPathDataRecursiv($array);
        //echo $output;


        $confData["shape_path"] = $output;

        IPS_SetConfiguration($this->InstanceID, json_encode($confData));
        IPS_ApplyChanges($this->InstanceID);
    }
    private function LoadPathDataRecursiv($array){
        $path = "";

        //print_r($array);

        foreach ($array as $item){
            if(!is_array($item)) continue;
            //print_r($item);
            if(array_key_exists("path", $item)) {
                foreach ($item["path"] as $pathitem) {
                    //print_r($pathitem);

                    if (array_key_exists("d", $pathitem)) {
                        $path = $path . " " . $pathitem["d"];
                    } else {
                        if (!array_key_exists("@attributes", $pathitem)) continue;
                        if (!array_key_exists("d", $pathitem["@attributes"])) continue;

                        $path = $path . " " . $pathitem["@attributes"]["d"];
                    }
                }
            }elseif(array_key_exists("d", $item)){
                $path = $path . " " . $item["d"];
            }else{
                $r_val = $this->LoadPathDataRecursiv($item);
                if(!empty($r_val))
                    $path = $path . " " . $this->LoadPathDataRecursiv($item);
            }
        }

        return $path;
    }
}

?>