<?php
include_once (__DIR__ . '/../SymconJSLive/libs/JSLiveModule.php');

class SymconJSLiveColorPicker extends JSLiveModule{
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
        $this->RegisterPropertyInteger("DataUpdateRate", 250);
        $this->RegisterPropertyInteger("manWidth", 0);
        $this->RegisterPropertyBoolean("EnableViewport", true);
        $this->RegisterPropertyInteger("IFrameHeight", 0);
        $this->RegisterPropertyInteger("overrideWidth", 0);
        $this->RegisterPropertyInteger("overrideHeight", 0);


        //style
        $this->RegisterPropertyInteger("style_borderWidth", 2);
        $this->RegisterPropertyInteger("style_borderColor", 0);
        $this->RegisterPropertyInteger("style_handleRadius", 8);

        //wheel
        $this->RegisterPropertyBoolean("wheel_Lightness", true);
        $this->RegisterPropertyInteger("wheel_Angle", 0);
        $this->RegisterPropertyString("wheel_Direction", "anticlockwise");

        //layout
        $this->RegisterPropertyString("layout_Direction", "horizontal");
        $this->RegisterPropertyString("Layout", "[]");

        //variables
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


        //if($buffer["instance"] != $this->InstanceID) return;
        //$this->SendDebug("ReceiveData", $jsonData['Buffer']. " =>" . $this->InstanceID, 0);

        switch($buffer['cmd']) {
            case "exportConfiguration":
                return $this->ExportConfiguration();
            case "getContend":
                return $this->GetOutput();
            case "getData":
                return $this->GetData($buffer['queryData']);
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
            $scriptData = file_get_contents (__DIR__ ."/../SymconJSLive/templates/ColorPicker.html");
        }else{
            if(!IPS_ScriptExists($scriptID)){
                $this->SendDebug('GetWebpage', 'Template NOT FOUND!', 0);
                return 'Template NOT FOUND!';
            }

            $scriptData = IPS_GetScriptContent($scriptID);
            if($scriptData == ""){
                return 'Template IS EMPTY!';
                $this->SendDebug('GetWebpage', 'Template IS EMPTY!', 0);
            }
        }

        $scriptData = $this->ReplacePlaceholder($scriptData);

        if($this->ReadPropertyBoolean("Debug"))
            $this->SendDebug('GetWebpage', $scriptData, 0);

        return $scriptData;
    }
    private function GetData(array $querydata){
        return json_encode($this->GenerateVariabels());
    }
    private function SetData(array $querydata){
        if(!array_key_exists("var", $querydata) || !array_key_exists("val", $querydata)){
            $this->SendDebug('SetData', "NO VARIABLE, OR VALUE SET!", 0);
            return "NO VARIABLE, OR VALUE SET!";
        }

        $var_data = json_decode($this->ReadPropertyString("Datasets"),true);
        $var_ids = array();
        foreach ($var_data as $varItem){
            if($varItem["Variable"] > 0)
                $var_ids[] = $varItem["Variable"];
        }

        if(!in_array($querydata["var"], $var_ids)){
            $this->SendDebug('SetData', "VARIABLE NOT IN LIST SET!", 0);
            return "VARIABLE NOT IN LIST SET!";
        }

        $this->SendDebug("SetData", "Update Variable " . $querydata["var"] ." => " .$querydata["val"], 0 );
        RequestAction($querydata["var"], $querydata["val"]);
        //$this->SendDebug("SetData", "Update Variable => OK", 0 );
        return "OK";
    }

    private function ReplacePlaceholder(string $htmlData){

        //configuration Data
        $htmlData = str_replace("{CONFIG}", $this->json_encode_advanced($this->GetConfigurationData()), $htmlData);

        //variables
        $htmlData = str_replace("{VARIABELS}", $this->json_encode_advanced($this->GenerateVariabels()), $htmlData);

        //Layout
        $htmlData = str_replace("{LAYOUT}", $this->json_encode_advanced($this->GenerateLayout()), $htmlData);

        return $htmlData;
    }
    private function GenerateVariabels(){
        $output = array();
        $variables = json_decode($this->ReadPropertyString("Datasets"), true);

        //load all variables
        foreach ($variables as $item){
            if(IPS_VariableExists($item["Variable"])){
                $s_output = array();

                $s_output["Variable"] = $item["Variable"];
                $s_output["Value"] = GetValue($item["Variable"]);
                $s_output["Mode"] = $item["Mode"];

                $output[] = $s_output;
            }
        }

        return $output;
    }
    private function GenerateLayout(){
        $layout_data = json_decode($this->ReadPropertyString("Layout"),true);

        $arr_order = array_column($layout_data, 'Order');
        array_multisort($arr_order, SORT_ASC , $layout_data);

        return $layout_data;
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

        unset($output["Layout"]);
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
            $confData["Variables"]= $this->ReadPropertyString("Datasets");

            IPS_SetConfiguration($this->InstanceID, json_encode($confData));
            IPS_ApplyChanges($this->InstanceID);
        }else return "A Instance must be selected!";
    }
}

?>