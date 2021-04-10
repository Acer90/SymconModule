<?
include_once (__DIR__ . '/../SymconJSLive/libs/WebHookModule.php');

class SymconJSLiveColorPicker extends JSLiveModule{
    public function Create() {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent("{9FFF3FC0-FD51-C289-FA36-BC1C370946CF}");

        //Expert
        $this->RegisterPropertyBoolean("Debug", false);
        $this->RegisterPropertyInteger("TemplateScriptID", 0);
        $this->RegisterPropertyInteger("DataUpdateRate", 50);

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
        $this->RegisterPropertyString("Variables", "[]");

    }
    public function ApplyChanges() {
        //Never delete this line!
        parent::ApplyChanges();
    }

    public function ReceiveData($JSONString) {
        $jsonData = json_decode($JSONString, true);
        $buffer = json_decode($jsonData['Buffer'], true);


        if($buffer["instance"] != $this->InstanceID) return;
        //$this->SendDebug("ReceiveData", $jsonData['Buffer']. " =>" . $this->InstanceID, 0);

        switch($buffer['cmd']) {
            case "getContend":
                return $this->GetWebpage();
            case "getData":
                return $this->GetData($buffer['queryData']);
            case "setData":
                return ""; //$this->GetData($buffer['queryData']);
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
            $scriptData = file_get_contents (__DIR__ ."/../SymconJSLive/templates/ColorPicker.html");
        }else{
            if(!IPS_ScriptExists($scriptID)){
                $this->SendDebug('GetWebpage', 'Template NOT FOUND!', 0);
                return "";
            }

            $scriptData = IPS_GetScriptContent($scriptID);
            if($scriptData = ""){
                $this->SendDebug('GetWebpage', 'Template IS EMPTY!', 0);
            }
        }

        //$this->SendDebug('GetWebpage', $scriptData, 0);
        $scriptData = $this->ReplacePlaceholder($scriptData);

        return $scriptData;
    }
    public function GetData($querydata){
        return json_encode($this->GenerateVariabels());
    }

    private function ReplacePlaceholder($htmlData){
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
        $variables = json_decode($this->ReadPropertyString("Variables"), true);

        //load all variables
        foreach ($variables as $item){
            $s_output = array();
            $s_output["Color"]["Variable"] = $item["Color"];
            $s_output["Color"]["Value"] = GetValue($item["Color"]);

            $s_output["Temperature"]["Variable"] = $item["ColorTemperature"];
            $s_output["Temperature"]["Value"] = GetValue($item["ColorTemperature"]);
            $s_output["Temperature"]["isMired"] = $item["isMired"];

            $s_output["Mode"]["Variable"] = $item["SwitchTemperature"];
            $s_output["Mode"]["Value"]  = boolval(GetValue($item["SwitchTemperature"]));
            //$this->SendDebug("Test", "Val => ". $s_output["Mode"]["Value"]. " |ITEM => ". GetValue($item["SwitchTemperature"]),0 );

            $output[] = $s_output;
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
        unset($output["Variables"]);
        return $output;
    }

    public function LoadOtherConfiguration($id){
        if(!IPS_ObjectExists($id)) return "Instance/Chart not found!";

        if(IPS_GetObject($id)["ObjectType"] == 1){
            //instance
            $intData = IPS_GetInstance($id);
            if($intData["ModuleInfo"]["ModuleID"] != IPS_GetInstance($this->InstanceID)["ModuleInfo"]["ModuleID"]) return "Only Allowed at the same Modul!";

            $confData = json_decode(IPS_GetConfiguration($id), true);

            //bestimmte aktuelle einstellungen beibehalten
            $confData["Variables"]= $this->ReadPropertyString("Variables");

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