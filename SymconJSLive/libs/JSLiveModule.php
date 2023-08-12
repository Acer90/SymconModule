<?php

class JSLiveModule extends IPSModule
{
    protected function GetFonts(){
        $font_list = array();
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
        return $font_list;
    }
    
    protected function LoadFonts(){
        $font_list = $this->GetFonts();
        
        //htmlTag erstellen
        $html_str = "";
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
    public function ExportConfiguration(bool $export_all = false, array $queryData = array()){
        $output = array();
        $withScript = false;

        if(array_key_exists("scripts", $queryData) && $queryData["scripts"] >= 1) $withScript = true;
        $withScript = true;

        //$output["queryData"] = json_encode($queryData);
        $output["ModuleID"] = IPS_GetInstance($this->InstanceID)["ModuleInfo"]["ModuleID"];
        $output["ModuleName"] = IPS_GetInstance($this->InstanceID)["ModuleInfo"]["ModuleName"];

        //$output["Config"] = json_decode(IPS_GetConfiguration($this->InstanceID), true);
        $jsonPath = realpath(__DIR__ . "/../../" . get_called_class() . "/form.json");
        $output["Config"] = json_decode(file_get_contents($jsonPath), true);

        $scriptID = $this->ReadPropertyInteger("TemplateScriptID");
        if(IPS_ScriptExists($scriptID) && $withScript){
            $output["Script"] = IPS_GetScriptContent($scriptID);
            $output["ScriptName"] = IPS_GetObject($scriptID)["ObjectName"];
        }

        if(!$export_all){
            $config = $output["Config"];
            $formData = json_decode(IPS_GetConfigurationForm($this->InstanceID), true)["elements"];
            $allowedItems = $this->GetAllowConfigurationExportList($formData);

            foreach($config as $key => $item){
                if(array_key_exists($key, $allowedItems) && $allowedItems[$key]["ignore"] == true){
                    unset($config[$key]);
                    continue;
                }

                if(!is_string($item)) continue;

                $jsonData = json_decode($item, true);
                if (json_last_error() !== JSON_ERROR_NONE) continue;

                foreach($jsonData as $j_key => $j_item){
                    foreach($j_item as $s_key => $s_item){
                        $name = $key."_".$s_key;
                        if(array_key_exists($name, $allowedItems) && $allowedItems[$name]["ignore"] == true){
                            unset($jsonData[$j_key][$s_key]);
                            continue;
                        }
                    }
                }

                $config[$key] = json_encode($jsonData);

            }

            $output["Config"] = $config;
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
    public function GetAllowConfigurationExportList(array $arr, array $r_arr = array(), string $column_name = ""){
        foreach ($arr as $key => $item) {
            //items Recusive for Items
            if (array_key_exists("items", $item)){
                $r_arr = $this->GetAllowConfigurationExportList($arr[$key]["items"], $r_arr);
            }
            if (array_key_exists("columns", $item)){
                if(array_key_exists("name", $item)){
                    $r_arr = $this->GetAllowConfigurationExportList($arr[$key]["columns"], $r_arr, $item["name"]);
                }
            }

            if (array_key_exists("name", $item)){
                $name = $arr[$key]["name"];
                $is_column = false;
                $ignore = false;

                if (array_key_exists("ignoreExport", $item) && $arr[$key]["ignoreExport"] == true) {
                    $ignore = true;
                }

                if(!empty($column_name)){
                    $name =  $column_name . "_" . $name;
                    $is_column = true;
                }

                $r_arr[$name] = array("is_column" => $is_column, "ignore" => $ignore);
            } 
        }

        return $r_arr;
    }
    public function LoadConfigurationFile(string $filename, bool $overrideScript = false){
        if(empty($filename)) return "File is Empty!";

        $confdata = json_decode(base64_decode($filename), true);
        //print_r($confdata);
        if(json_last_error() !== JSON_ERROR_NONE) return "Not valid json File!(1)";
        if(!array_key_exists("Config", $confdata) || !array_key_exists("ModuleID", $confdata) || !array_key_exists("ModuleName", $confdata)) return "Not valid json File!(2)";
        if($confdata["ModuleID"] != IPS_GetInstance($this->InstanceID)["ModuleInfo"]["ModuleID"]) return "Configuration only allowed for " . $confdata["ModuleName"];

        //echo json_encode($confdata["Config"]);
        //$allowedItems = $this->GetAllowConfigurationExportList($formData);
        $output = json_decode(IPS_GetConfiguration($this->InstanceID), true);
        $output["LastUploadedConfig"] = $filename;

        foreach ($confdata["Config"] as $key => $item){
            if(in_array($key, $output)){
                $i_data = $item;
                if(is_string($item)){
                    $jsonData = json_decode($item, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)){
                        $i_data = json_decode($output[$key], true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($i_data)){
                            foreach($jsonData as $l_key => $l_item){
                                if(array_key_exists($l_key, $i_data)){
                                    foreach($l_item as $s_key => $s_item){
                                        $i_data[$l_key][$s_key] = $s_item;
                                    }
                                }else{
                                    $i_data[$l_key] = $l_item;
                                }
                            } 
                            
                            $i_data = json_encode($i_data);
                        }else{
                            $i_data = json_encode($item);
                            $this->SendDebug("LoadConfigurationFile", "JSON OPTION ERROR (" .json_last_error() . ") ".$key." => " . $item  , 0);
                        }
                    }else{
                        //$this->SendDebug("LoadConfigurationFile", "JSON ERROR (" .json_last_error() . ") ".$key." => " . $item  , 0);
                    }
                }
                
                $output[$key] = $i_data;
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
    public function GetGlobalConfiguration(){
        $sendData = array("InstanceID" => $this->InstanceID, "Type" => "GetGlobalConfiguartion");
        $pData = $this->SendDataToParent(json_encode([
            'DataID' => "{751AABD7-E31D-024C-5CC0-82AC15B84095}",
            'Buffer' => utf8_encode(json_encode($sendData)),
        ]));

        return json_decode($pData, true);
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

        $addFunctions = array();
        $r_val = $this->RecursiveUpdateForm($formData["elements"], $addFunctions);
        $formData["elements"] = $r_val["arr"];
        $addFunctions = $r_val["func"];

        $r_val = $this->RecursiveUpdateForm($formData["actions"], $addFunctions);
        $formData["actions"] = $r_val["arr"];
        $addFunctions = $r_val["func"];

        $formData["elements"] = $this->RecursiveAddFormFunction($formData["elements"], $addFunctions);
        $formData["actions"] = $this->RecursiveAddFormFunction($formData["actions"], $addFunctions);


        $listVisibility = array("elements" => $formData["elements"], "actions" => $formData["actions"]);
        $this->SetBuffer("ConfigurationBuffer", json_encode($listVisibility));

        //this->SendDebug("AFTER", json_encode($formData), 0 );

        return $formData;
    }
    private function RecursiveUpdateForm($arr, $addFunctions){
        foreach ($arr as $key => $item) {
            //$this->SendDebug("RecursiveUpdateForm", $key, 0 );
            if(!array_key_exists("visible", $item)) $arr[$key]["visible"] = true;

            //items Recusive for Items
            if (array_key_exists("items", $item)){
                $r_val = $this->RecursiveUpdateForm($arr[$key]["items"], $addFunctions);
                $arr[$key]["items"] = $r_val["arr"];
                $addFunctions = $r_val["func"];
            }
            if (array_key_exists("options", $item)){
                $r_val = $this->RecursiveUpdateForm($arr[$key]["options"], $addFunctions);
                $arr[$key]["options"] = $r_val["arr"];
                $addFunctions = $r_val["func"];
            }
            if (array_key_exists("columns", $item)){
                $r_val = $this->RecursiveUpdateForm($arr[$key]["columns"], $addFunctions);
                $arr[$key]["columns"] = $r_val["arr"];
                $addFunctions = $r_val["func"];
            }

            //list options
            if (array_key_exists("edit", $item) && is_array($item["edit"]) && array_key_exists("columns", $item["edit"])) {
                $r_val = $this->RecursiveUpdateForm($arr[$key]["edit"]["columns"], $addFunctions);
                $arr[$key]["edit"]["columns"] = $r_val["arr"];
                $addFunctions = $r_val["func"];
            }
            if (array_key_exists("edit", $item) && is_array($item["edit"]) && array_key_exists("options", $item["edit"])){
                $r_val = $this->RecursiveUpdateForm($arr[$key]["edit"]["options"], $addFunctions);
                $arr[$key]["edit"]["options"] = $r_val["arr"];
                $addFunctions = $r_val["func"];
            }

            //alles bekommt einen Namen, um elemente später leiter verändern zu können!
            //ignore items without viewlevel element
            if (array_key_exists("viewlevel", $item)) {
                //viewLevelExactly set value

                if(!array_key_exists("name", $item)){
                    $arr[$key]["name"] = $this->getUniqueID();
                    $arr[$key]["disableExport"] = true; 
                    //$this->SendDebug(__FUNCTION__, $item["type"],0);
                }

                $viewLevelExactly = false;
                if (array_key_exists("viewlevelexactly", $item)) $viewLevelExactly = $arr[$key]["viewlevelexactly"];

                if ($viewLevelExactly == true && $arr[$key]["viewlevel"] != $this->ReadPropertyInteger("ViewLevel")) {
                    //ausblenden wenn viewlevel nicht genau das level ist!!!
                    //unset($arr[$key]);
                    $arr[$key]["visible"] = false;
                } elseif ($arr[$key]["viewlevel"] > $this->ReadPropertyInteger("ViewLevel")) {
                    if (array_key_exists("viewdisable", $item) && $arr[$key]["viewdisable"] == true) {
                        //diable wenn viewdisable == 1
                        $arr[$key]["enabled"] = false;
                    } else {
                        //ausblenden wenn viewlevel des elements zu hoch
                        //unset($arr[$key]);
                        $arr[$key]["visible"] = false;
                    }
                } else {
                    if (array_key_exists("caption", $item) && $arr[$key]["viewlevel"] > 0) { //$this->ReadPropertyBoolean("Debug") &&
                        switch ($arr[$key]["viewlevel"]) {
                            //case 0: $arr[$key]["caption"] = $this->translate($arr[$key]["caption"]) . " (Basic)";
                            case 1:
                                $arr[$key]["caption"] = $this->translate($arr[$key]["caption"]) . " (" . $this->translate("Advance") . ")";
                                break;
                            case 2:
                                $arr[$key]["caption"] = $this->translate($arr[$key]["caption"]) . " (" . $this->translate("Expert") . ")";
                                break;
                            default:
                                //do nothing
                                break;
                        }
                    }
                }
            }

            //checkbox zum ausbelden von objeckten
            if(array_key_exists("requireItem", $item)) {
                $propItem = $item["requireItem"];

                if(!in_array($propItem, $addFunctions)){
                    $addFunctions[] = $propItem;
                }

                if(!array_key_exists("name", $item)) {
                    $arr[$key]["name"] = $this->getUniqueID(); 
                    $arr[$key]["disableExport"] = true;
                }

                if($arr[$key]["visible"] && !$this->ReadPropertyBoolean($propItem)){
                    //ausbelden wenn noch eingeblendet, aber checkbox false
                    $arr[$key]["visible"] = false;
                    $arrVisibility[$key]["visible"] = false;
                }
            }

            //add function to viewlevel
            if(array_key_exists("name", $item) && $item["name"] == "ViewLevel"){
                //$arr[$key]["onChange"] = " ".IPS_GetInstance($this->InstanceID)["ModuleInfo"]["ModuleName"]."_ReloadConfigurationForm(\$id, 'ViewLevel', \$ViewLevel);";
                //$this->SendDebug("TEST", IPS_GetInstance($this->InstanceID)["ModuleInfo"]["ModuleName"]."_ReloadConfigurationForm(\$id, 'ViewLevel', \$ViewLevel);", 0);
            }

        }

        return array("arr" => array_values($arr), "func" => $addFunctions);
    }
    private function RecursiveAddFormFunction($arr, $addFunctions){
        foreach ($arr as $key => $item){
            if (array_key_exists("items", $item)) $arr[$key]["items"] = $this->RecursiveAddFormFunction($arr[$key]["items"], $addFunctions);
            if (array_key_exists("options", $item)) $arr[$key]["options"] = $this->RecursiveAddFormFunction($arr[$key]["options"], $addFunctions);
            if (array_key_exists("columns", $item)) $arr[$key]["columns"] = $this->RecursiveAddFormFunction($arr[$key]["columns"], $addFunctions);

            //list options
            if (array_key_exists("edit", $item) && is_array($item["edit"]) && array_key_exists("columns", $item["edit"])) $arr[$key]["edit"]["columns"] = $this->RecursiveAddFormFunction($arr[$key]["edit"]["columns"], $addFunctions);
            if (array_key_exists("edit", $item) && is_array($item["edit"]) && array_key_exists("options", $item["edit"])) $arr[$key]["edit"]["options"] = $this->RecursiveAddFormFunction($arr[$key]["edit"]["options"], $addFunctions);

            if(array_key_exists("name", $item) && in_array($item["name"], $addFunctions)) {
                $arr[$key]["onChange"] = IPS_GetInstance($this->InstanceID)["ModuleInfo"]["ModuleName"]."_ReloadConfigurationForm(\$id, '".$item["name"]."', \$".$item["name"].");";
                //$this->SendDebug("##TEST##", IPS_GetInstance($this->InstanceID)["ModuleInfo"]["ModuleName"]."_ReloadConfigurationForm(\$id, '".$item["name"]."', \$".$item["name"].");", 0);
            }
        }
        return array_values($arr);
    }
    public function ReloadConfigurationForm($name, $value){
        $configData = json_decode($this->GetBuffer("ConfigurationBuffer"), true);

        $this->RecursiveReloadForm($name, $value, $configData["elements"]);
        $this->RecursiveReloadForm($name, $value, $configData["actions"]);
    }
    private function RecursiveReloadForm($name, $value, $arr){
        foreach ($arr as $key => $item){
            if (array_key_exists("items", $item)) $arr[$key]["items"] = $this->RecursiveReloadForm($name, $value, $arr[$key]["items"]);
            if (array_key_exists("options", $item)) $arr[$key]["options"] = $this->RecursiveReloadForm($name, $value, $arr[$key]["options"]);
            if (array_key_exists("columns", $item)) $arr[$key]["columns"] = $this->RecursiveReloadForm($name, $value, $arr[$key]["columns"]);

            //list options
            if (array_key_exists("edit", $item) && is_array($item["edit"]) && array_key_exists("columns", $item["edit"])) $arr[$key]["edit"]["columns"] = $this->RecursiveReloadForm($name, $value, $arr[$key]["edit"]["columns"]);
            if (array_key_exists("edit", $item) && is_array($item["edit"]) && array_key_exists("options", $item["edit"])) $arr[$key]["edit"]["options"] = $this->RecursiveReloadForm($name, $value, $arr[$key]["edit"]["options"]);

            if(array_key_exists("requireItem", $item)) {
                if(!array_key_exists("name", $item)) {
                    if(!isset($item["type"])) $item["type"] = "";
                    $this->SendDebug(__FUNCTION__,"NO Name Set for => ". $item["type"], 0);
                }else{
                    if($item["requireItem"] == $name){
                        $this->UpdateFormField($item["name"], "visible", $value);
                    }
                }
            }

            if($name == "ViewLevel"){
                if (array_key_exists("viewlevel", $item)) {
                    //viewLevelExactly set value
                    $viewLevelExactly = false;
                    if (array_key_exists("viewlevelexactly", $item)) $viewLevelExactly = $arr[$key]["viewlevelexactly"];

                    if ($viewLevelExactly == true && $arr[$key]["viewlevel"] != $value) {
                        //ausblenden wenn viewlevel nicht genau das level ist!!!
                        $this->UpdateFormField($item["name"], "visible", false);
                        $this->UpdateFormField($item["name"], "enabled", true);
                    } elseif ($arr[$key]["viewlevel"] > $value) {
                        if (array_key_exists("viewdisable", $item) && $arr[$key]["viewdisable"] == true) {
                            //diable wenn viewdisable == 1
                            $this->UpdateFormField($item["name"], "visible", true);
                            $this->UpdateFormField($item["name"], "enabled", false);
                        } else {
                            //ausblenden wenn viewlevel des elements zu hoch
                            $this->UpdateFormField($item["name"], "visible", false);
                            $this->UpdateFormField($item["name"], "enabled", true);
                        }
                    } else {
                        $this->UpdateFormField($item["name"], "visible", true);
                        $this->UpdateFormField($item["name"], "enabled", true);
                    }
                }
            }
        }
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

    public function Create() {
        //Never delete this line!
        parent::Create();

        //Expert
        $this->RegisterPropertyBoolean("ShowDefault", true);
        $this->RegisterPropertyString("LastUploadedConfig", "");
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
        $this->SetBuffer("ConfigurationBuffer", "{}");
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

            /*$g_config = $this->GetGlobalConfiguration();
            $htmlStr = file_get_contents(__DIR__ ."/../htmlbox/HtmlBox-Chart.html");
            $htmlStr = str_replace("{INSTANCEID}", $this->InstanceID, $htmlStr);
            $htmlStr = str_replace("{BOXID}", $this->InstanceID.$this->getUniqueID(), $htmlStr);
            $htmlStr = str_replace("{PW}", $g_config["Password"], $htmlStr);
            $htmlStr = str_replace("{LINK}", $g_config["Address"]."/hook/JSLive", $htmlStr);
            $this->SetValue("Output", $htmlStr);  */
        }else{
            //remove old valeue
            $oldID = @IPS_GetObjectIDByIdent("Output", $this->InstanceID);

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
    private function getUniqueID($length=12)
    {
        $str='';
        // Passwortanforderung - von allen Zeichen: [a-z], [A-Z] und [0-9] - je Eines
        while(!(preg_match('/[a-z]/',$str)&&
            preg_match('/[A-Z]/',$str)&&
            preg_match('/[0-9]/',$str)))
        {
            srand((double)microtime()*1000000);
            $c = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $str = '';
            while (strlen($str) < $length) $str .= substr($c, (rand() % (strlen($c))),1);
        }

        //$this->SendDebug(__FUNCTION__, $str, 0);
        return $str;
    }

    protected function GET_PathList($arr, $paths = array(), $curPath = array())
    {
        if (array_key_exists("name", $arr)) {
            $paths[] = array("path" => $curPath, "name" => $arr["name"]);
        }
        foreach ($arr as $key => $subarr)
        {
            $newPath = $curPath;
            $newPath[] = $key;

            //echo $key. "\r\n";

            if (is_array($subarr))
            {
                $paths = $this->GET_PathList($subarr, $paths, $newPath);
            }
        }
        return $paths;
    }
    protected function SET_By_KEYPATH($path, &$array=array(), $value=null)
    {
        $temp =& $array;

        foreach ($path as $key) {
            $temp =& $temp[$key];
        }
        $temp = $value;
    }
    protected function GET_By_KEYPATH($array, $path){
        $temp =& $array;

        foreach($path as $key) {
            //print_r($temp[$key]);
            $temp =& $temp[$key];
        }
        return $temp;
    }
}