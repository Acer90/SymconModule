<?php

class SymconHomeAssistantDevice extends IPSModule
{
    // Buffer sind immer String. Kovertierungen durch integer, float oder bool können Probleme verursachen.
    // Also wird mit serialize immer alles typensicher in einen String gewandelt.

    public function Create()
    {
        parent::Create();
        $this->SendDebug(__FUNCTION__, '', 0);

        // Modul-Eigenschaftserstellung
        $this->RegisterPropertyString("entity_id", "");
        $this->RequireParent("{2A23C9D6-AB2C-E818-3D51-E29E446FAF69}");

    }
    public function ApplyChanges()
    {
        $this->RegisterMessage(0, 10001 /* IPS_KERNELSTARTED */);
        // Diese Zeile nicht löschen
        parent::ApplyChanges();
        $this->SetReceiveDataFilter('.*id\\\":[ \\\"]*('.$this->ReadPropertyString("entity_id").')[\\\"]*.*');

        $this->SetStatus(102);
    }

    public function SendData($jsonString)
    {
        @$this->SendDataToParent(json_encode(Array("DataID" => "{7B4E5B18-F847-8F8A-F148-3FB3F482E295}", "FrameTyp" => 1, "Fin" => true, "Buffer" =>  utf8_decode($jsonString))));
        $this->SendDebug(__FUNCTION__, $jsonString, 0);
    }

    public function ReceiveData($JSONString)
    {
        $data = json_decode($JSONString);
        $data = json_decode($data->Buffer, true);

        if($this->ReadPropertyString("entity_id") != $data["id"]) {
            return;
        }
        $this->SendDebug(__FUNCTION__, $data->Buffer, 0);

        if(!array_key_exists("data", $data)) {
            $this->SendDebug(__FUNCTION__, "NO DATA FOUND! ", 0);
            return;
        }

        $data = $data["data"];

        if(!array_key_exists("new_state", $data)) {
            $this->SendDebug(__FUNCTION__, "NO NEW STATE FOUND! ", 0);
            return;
        }

        $new_state = $data["new_state"];

        foreach($new_state as $name => $value){
            if($name == "attributes" || $name == "entity_id" || $name == "context") continue;
            if($name == "last_changed" || $name == "last_updated" || $name == "icon") continue;

            $this->updateVariable($name, $value);
        }

        if(array_key_exists("attributes", $new_state)){
            foreach($new_state["attributes"] as $name => $value){
                $this->updateVariable($name, $value);
            }
        }
    }

    private function updateVariable($name, $value){
        $this->SendDebug(__FUNCTION__, $name ." => ". $value, 0);
        $isTime = strtotime($value);
        if(strtolower($value) == "off" || strtolower($value) == "on") $isBool = true; else $isBool = false;


        if($this->GetIDForIdent($name) === false){
            if(is_numeric($value)){
                if(is_float($value)){
                    $this->SendDebug(__FUNCTION__,  $name. " TYPE => FLOAT", 0);
                    $this->RegisterVariableFloat($name, $name, "", 0);
                }else{
                    $this->SendDebug(__FUNCTION__,  $name. " TYPE => INT", 0);
                    $this->RegisterVariableInteger($name, $name, "", 0);
                }
            }elseif($isTime !== false){
                $this->SendDebug(__FUNCTION__,  $name. " TYPE => TIME(INT)", 0);
                $this->RegisterVariableInteger($name, $name, "", 0);
            }elseif($isBool){
                $this->SendDebug(__FUNCTION__,  $name. " TYPE => Bool", 0);
                $this->RegisterVariableBoolean($name, $name, "", 0);
            }else{
                $this->SendDebug(__FUNCTION__,  $name. " TYPE => String", 0);
                $this->RegisterVariableString($name, $name, "", 0);
            }

        }

        if($isTime !== false){
            $this->SetValue($name, $isTime);
        }elseif($isBool){
            if(strtolower($value) == "on"){
                $this->SetValue($name, true);
            }else{
                $this->SetValue($name, false);
            }
        }else{
            if(is_numeric($value)){
                $this->SetValue($name, $value);
            }else{
                @$this->SetValue($name, utf8_decode($value));
            }
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
}
