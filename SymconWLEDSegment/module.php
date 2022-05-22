<?php

class WLEDSegment extends IPSModule
{
    // Buffer sind immer String. Kovertierungen durch integer, float oder bool können Probleme verursachen.
    // Also wird mit serialize immer alles typensicher in einen String gewandelt.
    protected function SetBuffer($Name, $Daten)
    {
        parent::SetBuffer($Name, serialize($Daten));
    }
    protected function GetBuffer($Name)
    {
        return unserialize(parent::GetBuffer($Name));
    }

    public function Create()
    {
        parent::Create();
        $this->SendDebug(__FUNCTION__, '', 0);

        // Modul-Eigenschaftserstellung
        $this->RegisterPropertyInteger("SegmentID", 0);
        $this->RegisterPropertyBoolean("MoreColors", false);
        $this->RegisterPropertyBoolean("ShowTemperature", false);
        $this->RegisterPropertyBoolean("ShowEffects", false);
        $this->RegisterPropertyBoolean("ShowPalettes", false);


        $this->ConnectParent("{F2FEBC51-7E07-3D45-6F71-3D0560DE6375}");
    }
    public function ApplyChanges()
    {
        $this->RegisterMessage(0, 10001 /* IPS_KERNELSTARTED */);
        // Diese Zeile nicht löschen
        parent::ApplyChanges();
        $this->SendDebug(__FUNCTION__, '', 0);

        $this->SetReceiveDataFilter('.*id\\\":[ \\\"]*('.$this->ReadPropertyInteger("SegmentID").')[\\\”]*.*');

        $this->SetBuffer("UpdateVariables", true);
        $this->GetUpdate();
        $this->SetStatus(102);
    }

    public function GetUpdate(){
        $this->SendData(json_encode(array("cmd" => "update")));
    }
    public function SendData($jsonString)
    {

        @$this->SendDataToParent(json_encode(Array("DataID" => "{7B4E5B18-F847-8F8A-F148-3FB3F482E295}", "FrameTyp" => 1, "Fin" => true, "Buffer" =>  utf8_decode($jsonString))));
        $this->SendDebug(__FUNCTION__, $jsonString, 0);
    }
    public function ReceiveData($JSONString)
    {
        $data = json_decode($JSONString);
        $this->SendDebug(__FUNCTION__, $data->Buffer, 0);
        $data = json_decode($data->Buffer, true);

        //variablen anlegen, wenn diese fehlen
        if($this->GetBuffer("UpdateVariables")){
            $this->RegisterVariableBoolean("VariablePower", "Power", "~Switch", 0);
            $this->RegisterVariableInteger("VariableBrightness", "Brightness", "~Intensity.255", 10);
            $this->EnableAction("VariablePower");
            $this->EnableAction("VariableBrightness");

            $this->RegisterVariableInteger("VariableColor1", "Color", "~HexColor", 20);
            $this->EnableAction("VariableColor1");
            if($this->ReadPropertyBoolean("MoreColors")) {
                $this->RegisterVariableInteger("VariableColor2", "Color 2", "~HexColor", 21);
                $this->RegisterVariableInteger("VariableColor3", "Color 3", "~HexColor", 22);
                $this->EnableAction("VariableColor2");
                $this->EnableAction("VariableColor3");
            }else{
                @$this->DisableAction('VariableColor2');
                @$this->DisableAction('VariableColor3');
                $this->UnregisterVariable('VariableColor2');
                $this->UnregisterVariable('VariableColor3');
            }

            if(count($data["col"][0]) > 3){ //weiskanal
                $this->RegisterVariableInteger("VariableWhite", "White", "~Intensity.255", 23);
                $this->EnableAction("VariableWhite");
            }
            if($this->ReadPropertyBoolean("ShowTemperature")) {
                $this->RegisterVariableInteger("VariableTemperature", "Temperature", "WLED.Temperature", 24);
                $this->EnableAction("VariableTemperature");
            }else{
                @$this->DisableAction('VariableTemperature');
                $this->UnregisterVariable('VariableTemperature');
            }

            if($this->ReadPropertyBoolean("ShowEffects")){
                $this->RegisterVariableInteger("VariableEffects", "Effects", "WLED.Effects", 50);
                $this->RegisterVariableInteger("VariableEffectsSpeed", "Effect Speed", "~Intensity.255", 51);
                $this->RegisterVariableInteger("VariableEffectsIntensity", "Effect Intensity", "~Intensity.255", 52);
                $this->EnableAction("VariableEffects");
                $this->EnableAction("VariableEffectsSpeed");
                $this->EnableAction("VariableEffectsIntensity");
            }else{
                @$this->DisableAction('VariableEffects');
                @$this->DisableAction('VariableEffectsSpeed');
                @$this->DisableAction('VariableEffectsIntensity');
                $this->UnregisterVariable('VariableEffects');
                $this->UnregisterVariable('VariableEffectsSpeed');
                $this->UnregisterVariable('VariableEffectsIntensity');
            }

            if($this->ReadPropertyBoolean("ShowPalettes")) {
                $this->RegisterVariableInteger("VariablePalettes", "Palettes", "WLED.Palettes", 50);
                $this->EnableAction("VariablePalettes");
            }
            else{
                @$this->DisableAction('VariablePalettes');
                $this->UnregisterVariable('VariablePalettes');
            }

            $this->SetBuffer("UpdateVariables", false);
        }

        //daten verarbeiten!
        if(array_key_exists("on", $data)){
            $this->SetValue("VariablePower", $data["on"]);
        }
        if(array_key_exists("bri", $data)){
            $this->SetValue("VariableBrightness", $data["bri"]);
        }

        if(array_key_exists("col", $data)){
            $this->SetValue("VariableColor1", $this->RGBToHex($data["col"][0]));

            if($this->ReadPropertyBoolean("MoreColors")) {
                $this->SetValue("VariableColor2", $this->RGBToHex($data["col"][1]));
                $this->SetValue("VariableColor3", $this->RGBToHex($data["col"][2]));
            }

            if(count($data["col"][0]) > 3) { //weiskanal
                $this->SetValue("VariableWhite", $data["col"][0][3]);
            }
        }

        if(array_key_exists("cct", $data)){
            if($this->ReadPropertyBoolean("ShowTemperature")) {
                $this->SetValue("VariableTemperature", $data["cct"]);
            }
        }

        if(array_key_exists("pal", $data)){
            if($this->ReadPropertyBoolean("ShowPalettes")) {
                $this->SetValue("VariablePalettes", $data["pal"]);
            }
        }

        if(array_key_exists("fx", $data)){
            if($this->ReadPropertyBoolean("ShowEffects")) {
                $this->SetValue("VariableEffects", $data["fx"]);
            }
        }
        if(array_key_exists("sx", $data)){
            if($this->ReadPropertyBoolean("ShowEffects")) {
                $this->SetValue("VariableEffectsSpeed", $data["sx"]);
            }
        }
        if(array_key_exists("ix", $data)){
            if($this->ReadPropertyBoolean("ShowEffects")) {
                $this->SetValue("VariableEffectsIntensity", $data["ix"]);
            }
        }
    }
    public function RequestAction($Ident, $Value) {
        $sendArr = array();
        $segArr = array();
        $segArr["id"] = $this->ReadPropertyInteger("SegmentID");

        switch($Ident) {
            case "VariablePower":
                $segArr["on"] = $Value;

                $sendArr["seg"][] = $segArr;
                $sendStr = json_encode($sendArr);
                $this->SendData($sendStr);
                $this->SetValue($Ident, $Value);
                break;
            case "VariableBrightness":
                $segArr["bri"] = $Value;

                $sendArr["seg"][] = $segArr;
                $sendStr = json_encode($sendArr);
                $this->SendData($sendStr);
                $this->SetValue($Ident, $Value);
                break;
            case "VariableColor1":
            case "VariableColor2":
            case "VariableColor3":
            case "VariableWhite":
                $this->SetValue($Ident, $Value);

                $segArr["col"][0] = $this->HexToRGB($this->GetValue("VariableColor1"));

                if($this->ReadPropertyBoolean("MoreColors")) {
                    $segArr["col"][1] = $this->HexToRGB($this->GetValue("VariableColor2"));
                    $segArr["col"][2] = $this->HexToRGB($this->GetValue("VariableColor3"));
                }else{
                    $segArr["col"][1] = array(0,0,0);
                    $segArr["col"][2] = array(0,0,0);
                }

                $wID = @$this->GetIDForIdent("VariableWhite");
                if($wID !== false){
                    $white = $this->GetValue("VariableWhite");
                    $segArr["col"][0][3] = $white;
                    $segArr["col"][1][3] = $white;
                    $segArr["col"][2][3] = $white;
                }

                $sendArr["seg"][] = $segArr;
                $sendStr = json_encode($sendArr);
                $this->SendData($sendStr);
                break;
            case "VariableTemperature":
                $segArr["cct"] = $Value;

                $sendArr["seg"][] = $segArr;
                $sendStr = json_encode($sendArr);
                $this->SendData($sendStr);
                $this->SetValue($Ident, $Value);
                break;
            case "VariablePalettes":
                $segArr["pal"] = $Value;

                $sendArr["seg"][] = $segArr;
                $sendStr = json_encode($sendArr);
                $this->SendData($sendStr);
                $this->SetValue($Ident, $Value);
                break;
            case "VariableEffects":
                $segArr["fx"] = $Value;

                $sendArr["seg"][] = $segArr;
                $sendStr = json_encode($sendArr);
                $this->SendData($sendStr);
                $this->SetValue($Ident, $Value);
                break;
            case "VariableEffectsSpeed":
                $segArr["sx"] = $Value;

                $sendArr["seg"][] = $segArr;
                $sendStr = json_encode($sendArr);
                $this->SendData($sendStr);
                $this->SetValue($Ident, $Value);
                break;
            case "VariableEffectsIntensity":
                $segArr["ix"] = $Value;

                $sendArr["seg"][] = $segArr;
                $sendStr = json_encode($sendArr);
                $this->SendData($sendStr);
                $this->SetValue($Ident, $Value);
                break;
            default:
                throw new Exception("Invalid Ident");
        }
    }

    /**
     * Ergänzt SendDebug um Möglichkeit Objekte und Array auszugeben.
     *
     * @param string                                           $Message Nachricht für Data.
     * @param mixed $Data    Daten für die Ausgabe.
     *
     * @return int $Format Ausgabeformat für Strings.
     */
    protected function SendDebug($Message, $Data, $Format)
    {
        if (is_array($Data)) {
            if (count($Data) > 25) {
                $this->SendDebug($Message, array_slice($Data, 0, 20), 0);
                $this->SendDebug($Message . ':CUT', '-------------CUT-----------------', 0);
                $this->SendDebug($Message, array_slice($Data, -5, null, true), 0);
            } else {
                foreach ($Data as $Key => $DebugData) {
                    $this->SendDebug($Message . ':' . $Key, $DebugData, 0);
                }
            }
        } elseif (is_object($Data)) {
            foreach ($Data as $Key => $DebugData) {
                $this->SendDebug($Message . '->' . $Key, $DebugData, 0);
            }
        } elseif (is_bool($Data)) {
            parent::SendDebug($Message, ($Data ? 'TRUE' : 'FALSE'), 0);
        } else {
            if (IPS_GetKernelRunlevel() == KR_READY) {
                parent::SendDebug($Message, (string) $Data, $Format);
            } else {
                $this->LogMessage($Message . ':' . (string) $Data, KL_DEBUG);
            }
        }
    }
    private function HexToRGB($hexInt){
        $arr = array();
        $arr[0]   = floor($hexInt/65536);
        $arr[1]  = floor(($hexInt-($arr[0]*65536))/256);
        $arr[2] = $hexInt-($arr[1]*256)-($arr[0]*65536);

        return $arr;
    }
    private function RGBToHex($rgb_arr){
        return $rgb_arr[0]*256*256 + $rgb_arr[1]*256 + $rgb_arr[2];
    }

}
