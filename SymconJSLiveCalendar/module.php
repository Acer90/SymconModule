<?php
include_once (__DIR__ . '/../SymconJSLive/libs/JSLiveModule.php');

class SymconJSLiveCalendar extends JSLiveModule{
    public function Create() {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent("{9FFF3FC0-FD51-C289-FA36-BC1C370946CF}");

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

    }
    public function ApplyChanges() {
        //Never delete this line!
        parent::ApplyChanges();

        $this->RegisterVariableString("Content", $this->Translate("Content"), "", 0);
        
    }

    public function ReceiveData($JSONString) {
        parent::ReceiveData($JSONString);
        $jsonData = json_decode($JSONString, true);
        $buffer = json_decode($jsonData['Buffer'], true);

        switch($buffer['cmd']) {
            case "exportConfiguration":
                return $this->ExportConfiguration();
            case "getContend":
                return $this->GetOutput();
            case "getData":
                return $this->GetData($buffer['queryData']);
            case "getFeed":
                return $this->GetFeed($buffer['queryData']);
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
            $scriptData = file_get_contents(__DIR__ ."/../SymconJSLive/templates/Calendar.html");
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
        $output["Variable"] = IPS_GetObjectIDByIdent("Content", $this->InstanceID);
        $output["Value"] = $this->GetValue("Content");
        return json_encode($output);
    }
    private function SetData(array $querydata){
        if(!array_key_exists("val", $querydata)){
            $this->SendDebug('SetData', "NO VALUE SET!", 0);
            return "NO VALUE SET!";
        }

        $val = urldecode($querydata["val"]);
        $this->SendDebug("SetData", "Update Content => " . $val, 0 );
        $this->SetValue("Content", $val);
        return "OK";
    }
    private function GetFeed(array $querydata){
        $output = array();
        if(!array_key_exists("id", $querydata)) return json_encode($output);

        $InstanceID = (int) $querydata['id'];
        if (!IPS_ObjectExists($InstanceID)) return json_encode($output);

        $InstanceInfo = IPS_GetInstance($InstanceID);
        switch ($InstanceInfo['ModuleInfo']['ModuleID']) {
            case '{5127CDDC-2859-4223-A870-4D26AC83622C}': // reader instance
                /** @noinspection PhpUndefinedFunctionInspection */
                $CalendarFeed = json_decode(ICCR_GetCachedCalendar($InstanceID), true);
                break;
            case '{F22703FF-8576-4AB1-A0E7-02E3116CD3BA}': // notifier instance
                /** @noinspection PhpUndefinedFunctionInspection */
                $CalendarFeed = json_decode(ICCN_GetNotifierPresenceReason($InstanceID), true);
                break;
            default:
                // no job for us
                doReturn();
        }

        if (empty($CalendarFeed )) return json_encode($output);

        foreach ($CalendarFeed as $Event)
        {
            $CalEvent = array();
            $CalEvent[ "id" ] = $Event[ "UID" ];
            $CalEvent[ "title" ] = $Event[ "Name" ];
            $CalEvent[ "start" ] = $Event[ "FromS" ];
            $CalEvent[ "end" ] = $Event[ "ToS" ];

            if (isset($Event['allDay'])) $CalEvent['allDay'] = $Event['allDay'];

            $output[] = $CalEvent;
        }
        return json_encode($output);
    }

    private function ReplacePlaceholder(string $htmlData){
        //configuration Data
        $htmlData = str_replace("{CONFIG}", $this->json_encode_advanced($this->GetConfigurationData()), $htmlData);

        //Value
        $htmlData = str_replace("{VALUE}", "'".addslashes($this->GetValue("Content"))."'", $htmlData);

        //Load Fonts
        //$arr = array($this->ReadPropertyString("style_fontFamily"));
        //$htmlData = str_replace("{FONTS}", $this->LoadFonts($arr), $htmlData);

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

        $output["Variable"] = IPS_GetObjectIDByIdent("Content", $this->InstanceID);

        return $output;
    }

    public function LoadOtherConfiguration(int $id){
        if(!IPS_ObjectExists($id)) return "Instance/Chart not found!";

        if(IPS_GetObject($id)["ObjectType"] == 1){
            //instance
            $intData = IPS_GetInstance($id);
            if($intData["ModuleInfo"]["ModuleID"] != IPS_GetInstance($this->InstanceID)["ModuleInfo"]["ModuleID"]) return "Only Allowed at the same Modul!";

            $confData = json_decode(IPS_GetConfiguration($id), true);

            IPS_SetConfiguration($this->InstanceID, json_encode($confData));
            IPS_ApplyChanges($this->InstanceID);
        }else return "A Instance must be selected!";
    }
}

?>