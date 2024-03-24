<?php

class WLEDMaster extends IPSModule
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
        $this->RegisterPropertyBoolean("ShowNightlight", false);
        $this->RegisterPropertyBoolean("ShowPresets", false);
        $this->RegisterPropertyBoolean("ShowPlaylist", false);


        $this->ConnectParent("{F2FEBC51-7E07-3D45-6F71-3D0560DE6375}");
    }
    public function ApplyChanges()
    {
        $this->RegisterMessage(0, 10001 /* IPS_KERNELSTARTED */);
        // Diese Zeile nicht löschen
        parent::ApplyChanges();
        $this->SendDebug(__FUNCTION__, '', 0);

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

            $this->RegisterVariableFloat("VariableTransition", "Transition", "WLED.Transition", 20);
            $this->EnableAction("VariableTransition");


            if($this->ReadPropertyBoolean("ShowPresets")) {
                $this->RegisterVariableInteger("VariablePresetsID", "Presets ID", "", 30);
                $this->EnableAction("VariablePresetsID");
            }
            else{
                @$this->DisableAction('VariablePresetsID');
                $this->UnregisterVariable('VariablePresetsID');
            }

            if($this->ReadPropertyBoolean("ShowPlaylist")) {
                $this->RegisterVariableInteger("VariablePlaylistID", "Playlist ID", "", 35);
                $this->EnableAction("VariablePlaylistID");
            }
            else{
                @$this->DisableAction('VariablePlaylistID');
                $this->UnregisterVariable('VariablePlaylistID');
            }

            if($this->ReadPropertyBoolean("ShowNightlight")){
                $this->RegisterVariableBoolean("VariableNightlightOn", "Nightlight On", "~Switch", 50);
                $this->RegisterVariableInteger("VariableNightlightDuration", "Nightlight Duration", "WLED.NightlightDuration", 51);
                $this->RegisterVariableInteger("VariableNightlightMode", "Nightlight Mode", "WLED.NightlightMode", 52);
                $this->RegisterVariableInteger("VariableNightlightTargetBrightness", "Nightlight Target Brightness", "~Intensity.255", 53);
                $this->EnableAction("VariableNightlightOn");
                $this->EnableAction("VariableNightlightDuration");
                $this->EnableAction("VariableNightlightMode");
                $this->EnableAction("VariableNightlightTargetBrightness");

                //restdauer
                $this->RegisterVariableInteger("VariableNightlightRemainingDuration", "Remaining Nightlight Duration", "~UnixTimestampTime", 54);
            }else{
                @$this->DisableAction('VariableNightlightOn');
                @$this->DisableAction('VariableNightlightDuration');
                @$this->DisableAction('VariableNightlightMode');
                @$this->DisableAction('VariableNightlightTargetBrightness');
                $this->UnregisterVariable('VariableNightlightOn');
                $this->UnregisterVariable('VariableNightlightDuration');
                $this->UnregisterVariable('VariableNightlightMode');
                $this->UnregisterVariable('VariableNightlightTargetBrightness');
                $this->UnregisterVariable('VariableNightlightRemainingDuration');
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
        if(array_key_exists("transition", $data)){
            $this->SetValue("VariableTransition", ($data["transition"] / 10));
        }

        if($this->ReadPropertyBoolean("ShowPresets") && array_key_exists("ps", $data)) {
            $this->SetValue("VariablePresetsID", $data["ps"]);
        }

        if($this->ReadPropertyBoolean("ShowPlaylist") && array_key_exists("pl", $data)) {
            $this->SetValue("VariablePlaylistID", $data["pl"]);
        }

        if($this->ReadPropertyBoolean("ShowNightlight") && array_key_exists("nl", $data)) {
            if(array_key_exists("on", $data["nl"])){
                $this->SetValue("VariableNightlightOn", $data["nl"]["on"]);
            }

            if(array_key_exists("dur", $data["nl"])){
                $this->SetValue("VariableNightlightDuration", $data["nl"]["dur"]);
            }

            if(array_key_exists("mode", $data["nl"])){
                $this->SetValue("VariableNightlightMode", $data["nl"]["mode"]);
            }

            if(array_key_exists("tbri", $data["nl"])){
                $this->SetValue("VariableNightlightTargetBrightness", $data["nl"]["tbri"]);
            }

            if(array_key_exists("rem", $data["nl"])){
                if($data["nl"]["rem"] < 0) {
                    $data["nl"]["rem"] = 0;
                }

                $s = $data["nl"]["rem"]%60;
                $m = floor(($data["nl"]["rem"]%3600)/60);
                $h = floor(($data["nl"]["rem"]%86400)/3600);

                $time = new DateTime('2001-01-01');
                $time->setTime($h, $m, $s);

                $this->SetValue("VariableNightlightRemainingDuration", $time->getTimestamp());
            }
        }
    }
    public function RequestAction($Ident, $Value) {
        $sendArr = array();

        switch($Ident) {
            case "VariablePower":
                $sendArr["on"] = $Value;

                $sendStr = json_encode($sendArr);
                $this->SendData($sendStr);
                $this->SetValue($Ident, $Value);
                break;
            case "VariableBrightness":
                $sendArr["bri"] = $Value;

                $sendStr = json_encode($sendArr);
                $this->SendData($sendStr);
                $this->SetValue($Ident, $Value);
                break;
            case "VariableTransition":
                $sendArr["transition"] = $Value*10;

                $sendStr = json_encode($sendArr);
                $this->SendData($sendStr);
                $this->SetValue($Ident, $Value);
                break;
            case "VariablePresetsID":
                $sendArr["ps"] = $Value;

                $sendStr = json_encode($sendArr);
                $this->SendData($sendStr);
                $this->SetValue($Ident, $Value);
                break;
            case "VariablePlaylistID":
                $sendArr["pl"] = $Value;

                $sendStr = json_encode($sendArr);
                $this->SendData($sendStr);
                $this->SetValue($Ident, $Value);
                break;
            case "VariableNightlightOn":
                $sendArr["nl"]["on"] = $Value;

                $sendStr = json_encode($sendArr);
                $this->SendData($sendStr);
                $this->SetValue($Ident, $Value);
                break;
            case "VariableNightlightDuration":
                $sendArr["nl"]["dur"] = $Value;

                $sendStr = json_encode($sendArr);
                $this->SendData($sendStr);
                $this->SetValue($Ident, $Value);
                break;
            case "VariableNightlightMode":
                $sendArr["nl"]["mode"] = $Value;

                $sendStr = json_encode($sendArr);
                $this->SendData($sendStr);
                $this->SetValue($Ident, $Value);
                break;
            case "VariableNightlightTargetBrightness":
                $sendArr["nl"]["tbri"] = $Value;

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
