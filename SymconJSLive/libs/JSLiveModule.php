<?php

class JSLiveModule extends IPSModule
{
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

    public function ExportConfiguration(){
        $output = array();

        $output["ModulID"] = IPS_GetInstance($this->InstanceID)["ModuleInfo"]["ModuleID"];
        $output["ModuleName"] = IPS_GetInstance($this->InstanceID)["ModuleInfo"]["ModuleName"];

        $output["Config"] = json_decode(IPS_GetConfiguration($this->InstanceID), true);

        return json_encode($output);
    }
    public function LoadConfigurationFile(string $filename){
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
                $this->SendDebug("LoadConfigurationFile", "PARAMETER => " . $key ."SKIP", 0);
            }
        }


        IPS_SetConfiguration($this->InstanceID, json_encode($output));
        IPS_ApplyChanges($this->InstanceID);

    }
    public function GetConfigurationLink(){
        $sendData = array("InstanceID" => $this->InstanceID, "Type" => "GetConfigurationLink");
        $pData = $this->SendDataToParent(json_encode([
            'DataID' => "{751AABD7-E31D-024C-5CC0-82AC15B84095}",
            'Buffer' => utf8_encode(json_encode($sendData)),
        ]));

        return $pData;
    }


    private function ClearLogFile(){
        file_put_contents(IPS_GetLogDir()."logfile.log", "");
    }
    public function Debug_LoadLogFile(int $intID){
        $file_arr = file(IPS_GetLogDir()."logfile.log");

        print_r($file_arr);
    }
}