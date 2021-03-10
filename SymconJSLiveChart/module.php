<?

class SymconJSLiveChart extends IPSModule{

    public function Create() {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent("{9FFF3FC0-FD51-C289-FA36-BC1C370946CF}");


        $this->RegisterPropertyInteger("TemplateScriptID", 0);
        $this->RegisterPropertyString("title_text", "");

        //title
        $this->RegisterPropertyBoolean("title_display", false);
        $this->RegisterPropertyString("title_position", "top");
        $this->RegisterPropertyInteger("title_fontSize", 12);
        $this->RegisterPropertyInteger("title_fontColor", 0);

        //Axes
        $this->RegisterPropertyBoolean("axes_display", true);
        $this->RegisterPropertyInteger("axes_lineWidth", 1);
        $this->RegisterPropertyInteger("axes_color", 0);

        //Legend
        $this->RegisterPropertyBoolean("legend_display", true);
        $this->RegisterPropertyString("legend_position", "top");
        $this->RegisterPropertyString("legend_align", "center");
        $this->RegisterPropertyInteger("legend_fontSize", 12);
        $this->RegisterPropertyInteger("legend_fontColor", 0);

        //Tooltips
        $this->RegisterPropertyBoolean("tooltips_enabled", true);
        $this->RegisterPropertyString("tooltips_position", "average");
        $this->RegisterPropertyString("tooltips_mode", "index");
        $this->RegisterPropertyInteger("tooltips_fontSize", 12);
        $this->RegisterPropertyInteger("tooltips_fontColor", 65536);
        $this->RegisterPropertyInteger("tooltips_backgroundColor", 0);
        $this->RegisterPropertyInteger("tooltips_cornerRadius", 5);

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

        $this->RegisterVariableInteger("StartDate", $this->Translate("Start Date"), "~UnixTimestamp", 99);

        $this->EnableAction("Now");
        $this->EnableAction("StartDate");
    }

    public function UpdateTemplates(){
        $templates = glob(__DIR__ ."/templates/*.html");
        foreach($templates as $template_path){
            $template_name = basename($template_path);

            $this->SendDebug('UpdateTemplates', 'FileName: ' . basename($template_name), 0);


        }
    }

    public function GetWebpage(){
        $scriptID = $this->ReadPropertyInteger("TemplateScriptID");
        if(!IPS_ScriptExists($scriptID)){
            $this->SendDebug('GetWebpage', 'SCRIPT NOT FOUND!', 0);
            return "";
        }

        $scriptData = IPS_GetScriptContent($scriptID);
        $scriptData = $this->ReplacePlaceholder($scriptData);

        return $scriptData;
    }

    private function ReplacePlaceholder($htmlData){
        $htmlData = str_replace("{TITLE_TEXT}", $this->ReadPropertyString("title_text"), $htmlData);
        $htmlData = str_replace("{TITLE_DISPLAY}", ($this->ReadPropertyBoolean("title_display") ? 'true' : 'false'), $htmlData);

        //Legend
        $htmlData = str_replace("{LEGEND_DISPLAY}", ($this->ReadPropertyBoolean("legend_display") ? 'true' : 'false'), $htmlData);
        $htmlData = str_replace("{LEGEND_POSITION}", $this->ReadPropertyString("legend_position"), $htmlData);
        $htmlData = str_replace("{LEGEND_ALIGN}", $this->ReadPropertyString("legend_align"), $htmlData);
        $htmlData = str_replace("{LEGEND_FONTSIZE}", $this->ReadPropertyInteger("legend_fontSize"), $htmlData);
        $rgbdata = $this->HexToRGB($this->ReadPropertyInteger("legend_fontColor"));
        $htmlData = str_replace("{LEGEND_FONTCOLOR}", "rgb(" . $rgbdata["R"] .", " . $rgbdata["G"] .", " . $rgbdata["B"]. ")", $htmlData);
        //rgb(255, 99, 132)
        return $htmlData;
    }

    private function HexToRGB(int $Hex){
        $r   = floor($Hex/65536);
        $b  = floor(($Hex-($r*65536))/256);
        $g = $Hex-($b*256)-($r*65536);

        return array("R" => $r, "G" => $g, "B" => $b);
    }

    public function ReceiveData($JSONString) {
        $jsonData = json_decode($JSONString, true);
        $buffer = json_decode($jsonData['Buffer'], true);
        $this->SendDebug("ReceiveData", $jsonData['Buffer']. " =>" . $buffer["instance"], 0);

        if($buffer["instance"] != $this->InstanceID) return;


        switch($buffer['cmd']){
            case "getContend":
                return $this->GetWebpage();
        }
    }
}

?>