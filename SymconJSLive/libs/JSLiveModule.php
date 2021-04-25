<?php

class JSLiveModule extends IPSModule
{
    protected function LoadFonts(array $fonts){
        $font_list = array();
        $html_str = "";

        //fonts bereinigen!
        foreach ($fonts as $font){
            if(!in_array($font, $font_list) && !empty($font)){
                $font_list[] = $font;
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

            $html_str .= '<link rel="stylesheet" type="text/css" href="{ADDRESS}/hook/JSLive/js/css/fonts/'.$font.'.css">';
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
    public function GetLink(bool $local = true){
        $sendData = array("InstanceID" => $this->InstanceID, "Type" => "GetLink", "local" => $local);
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
                    if($output["TemplateScriptID"] == 0 || !$overrideScript || !IPS_ScriptExists($output["TemplateScriptID"])){
                        //Neues skript anlegen
                        $Libraries[$key]["Script"] = IPS_CreateScript(0);
                        IPS_SetParent($Libraries[$key]["Script"], $this->InstanceID);
                        IPS_SetName($Libraries[$key]["Script"], $confdata["ScriptName"]);
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
    public function GetConfigurationLink($withScript){
        $sendData = array("InstanceID" => $this->InstanceID, "Type" => "GetConfigurationLink");
        $pData = $this->SendDataToParent(json_encode([
            'DataID' => "{751AABD7-E31D-024C-5CC0-82AC15B84095}",
            'Buffer' => utf8_encode(json_encode($sendData)),
        ]));

        if($withScript) $pData .= "&scripts=1";

        return $pData;
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