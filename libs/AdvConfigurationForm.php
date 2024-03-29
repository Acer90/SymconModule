<?php

class JSLiveModule extends IPSModule
{
    protected function LoadFonts(){
        $font_list = array();
        $html_str = "";

        //alle fonts settings finden
        $conf_Data = json_decode(IPS_GetConfiguration($this->InstanceID), true);
        foreach ($conf_Data as $conf_key => $conf_item){
            if(strpos($conf_key, "_fontFamily") === false) continue;

            if(!in_array($conf_item, $font_list) && !empty($conf_item)){
                $font_list[] = $conf_item;
                if($this->ReadPropertyBoolean("Debug"))
                    $this->SendDebug("LoadFonts", "New font found => " . $conf_item, 0);
            }
        }

        //htmlTag erstellen
        $start = true;
        foreach ($font_list as $font){
            if($start){
                $start =  false;
            }else{
                $html_str .= "\r\n";
            }

            $html_str .= '<link rel="stylesheet" type="text/css" href="/hook/JSLive/js/css/fonts/'.$font.'.css">';
        }

        return $html_str;
    }

    protected function json_encode_advanced(array $arr, $sequential_keys = false, $quotes = false, $beautiful_json = true, $decimals = 2) {

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
                if(is_float($value)){
                    $output .= number_format($value, $decimals, '.', '');
                }else{
                    $output .= $value;
                }
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
    protected function HexToRGB(int $Hex){
        $r   = floor($Hex/65536);
        $g  = floor(($Hex-($r*65536))/256);
        $b = $Hex-($g*256)-($r*65536);

        return array("R" => $r, "G" => $g, "B" => $b);
    }

    //confuguration and link
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
    public function GetLink(){
        $sendData = array("InstanceID" => $this->InstanceID, "Type" => "GetLink");
        $pData = $this->SendDataToParent(json_encode([
            'DataID' => "{751AABD7-E31D-024C-5CC0-82AC15B84095}",
            'Buffer' => utf8_encode(json_encode($sendData)),
        ]));

        return $pData;
    }
    public function GetLocalLink(){
        $sendData = array("InstanceID" => $this->InstanceID, "Type" => "GetLocalLink");
        $pData = $this->SendDataToParent(json_encode([
            'DataID' => "{751AABD7-E31D-024C-5CC0-82AC15B84095}",
            'Buffer' => utf8_encode(json_encode($sendData)),
        ]));

        return $pData;
    }
    public function ExportConfiguration(array $queryData = array()){
        $output = array();
        $withScript = false;

        if(array_key_exists("scripts", $queryData) && $queryData["scripts"] >= 1) $withScript = true;
        $withScript = true;

        //$output["queryData"] = json_encode($queryData);
        $output["ModulID"] = IPS_GetInstance($this->InstanceID)["ModuleInfo"]["ModuleID"];
        $output["ModuleName"] = IPS_GetInstance($this->InstanceID)["ModuleInfo"]["ModuleName"];

        $output["Config"] = json_decode(IPS_GetConfiguration($this->InstanceID), true);

        $scriptID = $this->ReadPropertyInteger("TemplateScriptID");
        if(IPS_ScriptExists($scriptID) && $withScript){
            $output["Script"] = IPS_GetScriptContent($scriptID);
            $output["ScriptName"] = IPS_GetObject($scriptID)["ObjectName"];
        }

        if(array_key_exists("Libraries", $output["Config"]) && $withScript){
            //export libery scripts
            $libs = array();
            $lib_data = json_decode($output["Config"]["Libraries"],true);

            foreach ($lib_data as $item){
                if(IPS_ScriptExists($item["Script"])){
                    $s_lib = array();
                    $s_lib["Ident"] = $item["Ident"];
                    $s_lib["Script"] = IPS_GetScriptContent($item["Script"]);
                    $s_lib["ScriptName"] = IPS_GetObject($item["Script"])["ObjectName"];

                    $libs[] = $s_lib;
                }
            }

            if(count($libs) > 0){
                $output["Libraries"] = $libs;
            }
        }

        return json_encode($output, JSON_PRETTY_PRINT);
    }
    public function LoadConfigurationFile(string $filename, bool $overrideScript = false){
        if(empty($filename)) return "File is Empty!";

        $confdata = json_decode(base64_decode($filename), true);
        //print_r($confdata);
        if(json_last_error() !== JSON_ERROR_NONE) return "Not valid json File!";
        if(!array_key_exists("Config", $confdata) || !array_key_exists("ModulID", $confdata) || !array_key_exists("ModuleName", $confdata)) return "Not valid json File!";
        if($confdata["ModulID"] != IPS_GetInstance($this->InstanceID)["ModuleInfo"]["ModuleID"]) return "Configuration only allowed for " . $confdata["ModuleName"];

        //echo json_encode($confdata["Config"]);

        $output = json_decode(IPS_GetConfiguration($this->InstanceID), true);

        foreach ($confdata["Config"] as $key => $item){
            if(in_array($key, $output)){
                $output[$key] = $item;
                $this->SendDebug("LoadConfigurationFile", "PARAMETER UPDATE ".$key." => " . $item , 0);
            }else{
                $this->SendDebug("LoadConfigurationFile", "PARAMETER => " . $key ." SKIP", 0);
            }
        }

        if($overrideScript && array_key_exists("Script", $confdata)){
            //Load with Skript
            if($output["TemplateScriptID"] == 0 || !$overrideScript || !IPS_ScriptExists($output["TemplateScriptID"])){
                //Neues skript anlegen
                $output["TemplateScriptID"] = IPS_CreateScript(0);
                IPS_SetParent($output["TemplateScriptID"], $this->InstanceID);
                IPS_SetName($output["TemplateScriptID"], $confdata["ScriptName"]);
            }

            IPS_SetScriptContent($output["TemplateScriptID"], $confdata["Script"]);
        }


        if($overrideScript && array_key_exists("Libraries", $confdata) && is_array($confdata["Libraries"])){
            $hasChanged = false;
            $Libraries = json_decode($output["Libraries"], true);
            foreach ($confdata["Libraries"] as $lib){
                //find index
                $key = array_search($lib["Ident"], array_column($Libraries, 'Ident'));

                if($key === false){
                    $this->SendDebug("LoadConfigurationFile", "SCRIPT => " . $lib["Ident"] ." SKIP", 0);
                    continue;
                }else{
                    if($Libraries[$key]["Script"] == 0 || !$overrideScript || !IPS_ScriptExists($Libraries[$key]["Script"])){
                        //Neues skript anlegen
                        $Libraries[$key]["Script"] = IPS_CreateScript(0);
                        IPS_SetParent($Libraries[$key]["Script"], $this->InstanceID);
                        IPS_SetName($Libraries[$key]["Script"], $lib["ScriptName"]);
                        $hasChanged = true;
                    }

                    IPS_SetScriptContent($Libraries[$key]["Script"], $lib["Script"]);
                }
            }

            if($hasChanged){
                $confdata["Libraries"] = json_encode($Libraries);
            }
        }

        IPS_SetConfiguration($this->InstanceID, json_encode($output));
        IPS_ApplyChanges($this->InstanceID);

    }
    public function GetConfigurationLink(bool $withScript){
        $sendData = array("InstanceID" => $this->InstanceID, "Type" => "GetConfigurationLink");
        $pData = $this->SendDataToParent(json_encode([
            'DataID' => "{751AABD7-E31D-024C-5CC0-82AC15B84095}",
            'Buffer' => utf8_encode(json_encode($sendData)),
        ]));

        if($withScript) $pData .= "&scripts=1";

        return $pData;
    }

    //Dynamic Configuration form
    public function GetConfigurationForm() {
        return json_encode($this->LoadConfigurationForm());
    }
    public function LoadConfigurationForm(){
        $formData = array();

        $jsonPath = realpath(__DIR__ . "/../../" . get_called_class() . "/form.json");
        if($this->ReadPropertyBoolean("Debug")) $this->SendDebug("GetConfigurationForm", $jsonPath, 0 );
        $formData = json_decode(file_get_contents($jsonPath), true);

        //Remove Confoniguration for Basic => 0; Advance => 1; Expert => 2
        //$this->SendDebug("BEFOR", json_encode($formData), 0 );

        $formData["elements"] = $this->RecursiveUpdateForm($formData["elements"]);
        $formData["actions"] = $this->RecursiveUpdateForm($formData["actions"]);

        //$this->SendDebug("AFTER", json_encode($formData), 0 );

        return $formData;
    }
    private function RecursiveUpdateForm($arr){
        foreach ($arr as $key => $item) {
            //$this->SendDebug("RecursiveUpdateForm", $key, 0 );

            //items Recusive for Items
            if (array_key_exists("items", $item)) $arr[$key]["items"] = $this->RecursiveUpdateForm($arr[$key]["items"]);
            if (array_key_exists("options", $item)) $arr[$key]["options"] = $this->RecursiveUpdateForm($arr[$key]["options"]);
            if (array_key_exists("columns", $item)) $arr[$key]["columns"] = $this->RecursiveUpdateForm($arr[$key]["columns"]);

            //list options
            if (array_key_exists("edit", $item) && is_array($item["edit"]) && array_key_exists("columns", $item["edit"])) $arr[$key]["edit"]["columns"] = $this->RecursiveUpdateForm($arr[$key]["edit"]["columns"]);
            if (array_key_exists("edit", $item) && is_array($item["edit"]) && array_key_exists("options", $item["edit"])) $arr[$key]["edit"]["options"] = $this->RecursiveUpdateForm($arr[$key]["edit"]["options"]);

            //ignore items without viewlevel element
            if (!array_key_exists("viewlevel", $item)) continue;

            //viewLevelExactly set value
            $viewLevelExactly = false;
            if (array_key_exists("viewlevelexactly", $item)) $viewLevelExactly = $arr[$key]["viewlevelexactly"];

            if ($viewLevelExactly == true && $arr[$key]["viewlevel"] != $this->ReadPropertyInteger("ViewLevel")) {
                //löschen wenn viewlevel nicht genau das level ist!!!
                unset($arr[$key]);
            }elseif($arr[$key]["viewlevel"] > $this->ReadPropertyInteger("ViewLevel")){
                if(array_key_exists("viewdisable", $item) && $arr[$key]["viewdisable"] == true){
                    //diable wenn viewdisable == 1
                    $arr[$key]["enabled"] = false;
                }else{
                    //löschen wenn viewlevel des elements zu hoch
                    unset($arr[$key]);

                }
            }else{
                if($this->ReadPropertyBoolean("Debug") && array_key_exists("caption", $item) && $arr[$key]["viewlevel"] > 0){
                    switch($arr[$key]["viewlevel"]){
                        case 0: $arr[$key]["caption"] = $arr[$key]["caption"] . " (Basic)";
                        case 1: $arr[$key]["caption"] = $arr[$key]["caption"] . " (Advance)";
                        case 2: $arr[$key]["caption"] = $arr[$key]["caption"] . " (Expert)";
                    }
                }
            }
        }

        return array_values($arr);
    }

    //Messagesink
    private function UpdateIdentList(){
        $arr_Idents = array("Period", "Offset", "Now", "StartDate", "Relativ");
        $identIdlist = array();

        foreach ($arr_Idents as $ident){
            $identID = @IPS_GetObjectIDByIdent($ident, $this->InstanceID);

            if($identID !== false){
                $identIdlist[] = $identID;
            }
        }

        $this->SetBuffer("IdentIDList", json_encode($identIdlist));
    }
    protected function GetVariableList(){
        $varList = array();
        $confData = json_decode(IPS_GetConfiguration($this->InstanceID), true);

        if(array_key_exists("Datasets", $confData)){
            $datasets = json_decode($confData["Datasets"], true);

            foreach ($datasets as $item){
                if(array_key_exists("Variable", $item)){
                    if(IPS_VariableExists($item["Variable"])){
                        $varList[] = $item["Variable"];
                    }
                }

                if(array_key_exists("Variables", $item) && is_array($item["Variables"])){
                    foreach ($item["Variables"] as $var) {
                        if (IPS_VariableExists($var["Variable"])) {
                            $varList[] = $var["Variable"];
                        }
                    }
                }

                //Custom Modul
                if(array_key_exists("Object", $item)){

                }


            }
        }

        //check if identlist exist then add
        if(in_array("IdentIDList", $this->GetBufferList())){
            $identList = json_decode($this->GetBuffer("IdentIDList"), true);
            foreach ($identList as $identID){
                $varList[] = $identID;
            }
        }

        if(array_key_exists("Variable", $confData)){
            if(IPS_VariableExists($confData["Variable"])){
                $varList[] = $confData["Variable"];
            }
        }

        if(array_key_exists("Variable", $confData)){
            if(IPS_VariableExists($confData["Variable"])){
                $varList[] = $confData["Variable"];
            }
        }

        //if($this->ReadPropertyBoolean("Debug"))
            $this->SendDebug("GetVariableList" , json_encode($varList), 0);
        return $varList;
    }
    protected function UpdateMessageSink(array $newVariables){
        if(in_array("MessageSink", $this->GetBufferList())){
            $oldVariables = json_decode($this->GetBuffer("MessageSink"), true);
        }else{
            $oldVariables = array();
        }

        //gateway connection registrieren!
        $gw_id = IPS_GetInstance($this->InstanceID)["ConnectionID"];
        $this->SendDebug("UpdateMessageSink", "Register Gateway for ReadyMessage (ID:".$gw_id." => 10503)", 0);
        $this->RegisterMessage($gw_id, 10503); //wenn verfügbar!


        foreach ($newVariables as $var){
            $oldVariables = array_diff($oldVariables, array($var));

            if(in_array($var , $oldVariables)){
                if($this->ReadPropertyBoolean("Debug"))
                    $this->SendDebug("UpdateMessageSink", "Skip => " . $var, 0);
                continue;
            }

            $this->RegisterMessage($var, 10602);
            $this->RegisterMessage($var, 10603);

            if($this->ReadPropertyBoolean("Debug"))
                $this->SendDebug("UpdateMessageSink", "RegisterMessage => " . $var, 0);
        }

        //alte entfernen
        foreach ($oldVariables as $var){
            $this->UnregisterMessage ($var, 10602);
            $this->UnregisterMessage ($var, 10603);

            if($this->ReadPropertyBoolean("Debug"))
                $this->SendDebug("UpdateMessageSink", "UnregisterMessage => " . $var, 0);
        }

        $this->SetBuffer("MessageSink", json_encode($newVariables));
    }
    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        //Never delete this line!
        parent::MessageSink($TimeStamp, $SenderID, $Message, $Data);

        $gw_id = IPS_GetInstance($this->InstanceID)["ConnectionID"];
        if ($SenderID == $gw_id && $Message == 10503) {
            //$this->SendDebug("MessageSink", "Update Output!",0);
            $this->UpdateOutput();
            $this->UpdateIframe();
            return;
        }

        if($this->ReadPropertyBoolean("Debug"))
            $this->SendDebug("MessageSink", $TimeStamp." | ".$SenderID." => ". $Message . "(". json_encode($Data).")",0);

        switch ($Message) {
            case 10602:
                //delete Message!
                //the unregister!
                $this->UnregisterMessage ($SenderID, 10602);
                $this->UnregisterMessage ($SenderID, 10603);

                $this->SendDebug("UpdateMessageSink", "UnregisterMessage => " . $SenderID . "(REMOVE)", 0);

                $RegistredVariables = json_decode($this->GetBuffer("MessageSink"), true);

                unset($RegistredVariables[$SenderID]);
                $this->SetBuffer("MessageSink", json_encode($RegistredVariables));
                break;
            case 10603:
                //on Update send

                if(in_array("IdentIDList", $this->GetBufferList())){
                    $identList = json_decode($this->GetBuffer("IdentIDList"), true);
                    if(in_array($SenderID, $identList)){
                        $this->SendDebug("UpdateMessageSink", "Send Data => " . $SenderID . " | DATA => " . json_encode($Data), 0);
                    }
                }

                $this->SendDataToSocketClient($SenderID, $Message, $Data);
                break;
            default:
                $this->SendDebug("MessageSink", $TimeStamp." | ".$SenderID." => ". $Message . "(". json_encode($Data).")",0);
                break;
        }
    }

    //websocket
    protected function SendDataToSocketClient(int $SenderID, int $Message, array $Data)
    {
        $senddata = array();
        $senddata["SenderID"] = $SenderID;
        $senddata["Message"] = $Message;
        $senddata["Data"] = $Data;

        if($this->ReadPropertyBoolean("Debug"))
            $this->SendDebug("MessageSink", "Send Data to WS-Client => " . json_encode($senddata), 0);

        $hcID = IPS_GetInstanceListByModuleID('{015A6EB8-D6E5-4B93-B496-0D3F77AE9FE1}')[0];
        WC_PushMessage($hcID, '/hook/JSLive/WS/' . $this->InstanceID, json_encode($senddata));
    }

    //Cache and Htmlbox
    public function ReceiveData($JSONString)
    {
        $jsonData = json_decode($JSONString, true);
        $buffer = json_decode($jsonData['Buffer'], true);

        if($buffer["cmd"] === "UpdateCache"){
            $this->SetBuffer("Output", "");
            $this->SendDataToSocketClient($this->InstanceID, 10506, array());
            $this->UpdateIframe();
        }
    }
    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();

        $this->SetReceiveDataFilter('.*instance\\\":[ \\\"]*('.$this->InstanceID.'|0)[\\\”]*.*');

        //updateindetnlist
        $this->UpdateIdentList();

        //HTMLBOX and Chache!
        $this->SetBuffer("Output", "");
        $this->UpdateOutput();
        $this->UpdateIframe();

        //Update MessageSink
        $this->UpdateMessageSink($this->GetVariableList());

        //send refresh website to client
        $this->SendDataToSocketClient($this->InstanceID, 10506, array());
    }
    protected function UpdateOutput(){
        if (IPS_GetInstance($this->InstanceID)["InstanceStatus"] != 102) return;

        //setLastModifed buffer
        $this->SetBuffer("LastModifed", gmdate("D, d M Y H:i:s", time())." GMT");

        $sendData = array();
        $sendData["Html"] = $this->GetWebpage();
        $sendData["InstanceID"] = $this->InstanceID;
        $sendData["Type"] = "UpdateHtml";
        $sendData["ViewPort"] = $this->ReadPropertyBoolean("EnableViewport");

        $pData = json_decode($this->SendDataToParent(json_encode([
            'DataID' => "{751AABD7-E31D-024C-5CC0-82AC15B84095}",
            'Buffer' => utf8_encode(json_encode($sendData)),
        ])), true);

        if ($this->ReadPropertyBoolean("EnableCache")) {
            $this->SetBuffer("Output", $pData["output"]);
        } else {
            $this->SetBuffer("Output", "");
        }

        if ($this->ReadPropertyBoolean("CreateIPSView") && $pData["ipsview"]) {
            //Überschreibe Iframe wenn nativMode aktive
            if(@IPS_GetObjectIDByIdent("IPSView", $this->InstanceID) === false){
                $this->RegisterVariableString("IPSView", $this->Translate("IPSView"), "~HTMLBox", 0);

                //An Symcon bitte nicht meckern, aber im webfront geht die native integration nicht, deshalb blende ich dieses element hier aus.
                IPS_SetHidden(@IPS_GetObjectIDByIdent("IPSView", $this->InstanceID),true);
            }

            $this->SetValue("IPSView", $pData["output"]);
        }else{
            $this->UnregisterVariable("IPSView");
        }


        //send refresh website to client
        //$this->SendDataToSocketClient($this->InstanceID, 10506, array());
    }
    protected function UpdateIframe(){
        //Gateway not ready
        if (IPS_GetInstance($this->InstanceID)["InstanceStatus"] != 102) return;

        $htmlStr = "";
        $scrolling = "no";
        $height = $this->ReadPropertyInteger("IFrameHeight");

        if($this->ReadPropertyBoolean("CreateOutput")) {
            $this->RegisterVariableString("Output", $this->Translate("Output"), "~HTMLBox", 0);

            $link = $this->GetLocalLink();
            //$link = $this->GetLink();

            if($height == 0){
                //$link="https://wiki.selfhtml.org/extensions/Selfhtml/frickl.php/Beispiel:JS-window-abmessungen.html#view_result";
                $htmlStr .= '<iframe src="' . $link . '" frameborder="0" scrolling="'.$scrolling.'"></iframe>';
            }else{
                $htmlStr .= '<iframe src="' . $link . '" width="100%" frameborder="0" scrolling="'.$scrolling.'" height="'.$height.'"></iframe>';
            }

            $this->SetValue("Output", $htmlStr);
        }else{
            //remove old valeue
            $oldID = IPS_GetObjectIDByIdent("Output", $this->InstanceID);

            if($oldID !== false){
                $this->UnregisterVariable("Output");
            }
        }
    }
    protected function GetOutput(){
        $EnableCache = $this->ReadPropertyBoolean("EnableCache");
        $EnableViewport = $this->ReadPropertyBoolean("EnableViewport");
        if($this->ReadPropertyBoolean("EnableCache")){
            //Load data from Cache
            if(empty($this->GetBuffer("Output"))){
                //updateCache when empty
                $this->UpdateOutput();
                $this->UpdateIframe();
            }

            if($this->ReadPropertyBoolean("Debug"))
                $this->SendDebug("GetOutput", "Get Data form Cache!", 0);
            return json_encode(array("Contend" => $this->GetBuffer("Output"), "lastModify" => $this->GetBuffer("LastModifed"), "EnableCache" => $EnableCache, "EnableViewport" => $EnableViewport, "InstanceID" => $this->InstanceID));
        }else{
            return json_encode(array("Contend" => $this->GetWebpage(), "lastModify" => $this->GetBuffer("LastModifed"), "EnableCache" => $EnableCache, "EnableViewport" => $EnableViewport, "InstanceID" => $this->InstanceID));
        }
    }

    //Advance Debug!
    private function ClearLogFile(){
        file_put_contents(IPS_GetLogDir()."logfile.log", "");
    }
    public function Debug_LoadLogFile(int $intID){
        $file_arr = file(IPS_GetLogDir()."logfile.log");

        print_r($file_arr);
    }
}