<?
include_once (__DIR__ . '/../SymconJSLive/libs/WebHookModule.php');

class SymconJSLiveDateTimePicker extends JSLiveModule{
    public function Create() {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent("{9FFF3FC0-FD51-C289-FA36-BC1C370946CF}");

        $this->RegisterPropertyInteger("Variable", 0);
        $this->RegisterPropertyString("Template", "TimePicker1");

        //Expert
        $this->RegisterPropertyBoolean("Debug", false);
        $this->RegisterPropertyInteger("TemplateScriptID", 0);
        $this->RegisterPropertyInteger("DataUpdateRate", 50);

        //style
        $this->RegisterPropertyInteger("style_backgroundColor", 0);
        $this->RegisterPropertyInteger("style_borderRadius", 10);
        $this->RegisterPropertyInteger("style_borderWidth", 2);
        $this->RegisterPropertyInteger("style_borderColor", 0);
        $this->RegisterPropertyInteger("style_handleRadius", 8);
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
                return $this->SetData($buffer['queryData']);
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
            $scriptData = file_get_contents(__DIR__ ."/../SymconJSLive/templates/".$this->ReadPropertyString("Template"). ".html");
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
    public function SetData($querydata){
        if(!array_key_exists("var", $querydata) || !array_key_exists("val", $querydata)){
            $this->SendDebug('SetData', "NO VARIABLE, OR VALUE SET!", 0);
            return "NO VARIABLE, OR VALUE SET!";
        }

        $var_data = json_decode($this->ReadPropertyString("Variables"),true);
        $var_ids = array();
        foreach ($var_data as $varItem){
            if($varItem["Color"] > 0)
                $var_ids[] = $varItem["Color"];

            if($varItem["ColorTemperature"] > 0)
                $var_ids[] = $varItem["ColorTemperature"];
        }

        if(!in_array($querydata["var"], $var_ids)){
            $this->SendDebug('SetData', "VARIABLE NOT IN LIST SET!", 0);
            return "VARIABLE NOT IN LIST SET!";
        }

        $this->SendDebug("SetData", "Update Variable " . $querydata["var"] ." => " .$querydata["val"], 0 );
        RequestAction($querydata["var"], $querydata["val"]);
        $this->SendDebug("SetData", "Update Variable => OK", 0 );
        return "OK";
    }

    private function ReplacePlaceholder($htmlData){
        //configuration Data
        $htmlData = str_replace("{CONFIG}", $this->json_encode_advanced($this->GetConfigurationData()), $htmlData);

        //variables
        if(IPS_VariableExists($this->ReadPropertyInteger("Variable"))){
            $htmlData = str_replace("{VALUE}", GetValue($this->ReadPropertyInteger("Variable")), $htmlData);
        }else{
            $htmlData = str_replace("{VALUE}", "", $htmlData);
        }

        //Layout
        //$htmlData = str_replace("{LAYOUT}", $this->json_encode_advanced($this->GenerateLayout()), $htmlData);

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