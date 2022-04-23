<?php

class WLEDSplitter extends IPSModule
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
        $this->RegisterPropertyBoolean("Active", false);
        $this->RegisterPropertyString("IPAddress", "192.168.178.1");

        $this->RequireParent("{D68FD31F-0E90-7019-F16C-1949BD3079EF}");
    }

    public function ApplyChanges()
    {
        $this->RegisterMessage(0, 10001 /* IPS_KERNELSTARTED */);
        // Diese Zeile nicht löschen
        parent::ApplyChanges();
        $this->SendDebug(__FUNCTION__, '', 0);

        if (IPS_GetKernelRunlevel() == 10103 /* KR_READY */) {
            $this->UpdateConfigurationForParent();
        }

        if(Sys_Ping($this->ReadPropertyString("IPAddress"), 3000)){
            if(!IPS_VariableProfileExists("WLED.Effects")){
                IPS_CreateVariableProfile("WLED.Effects", 1);

                $eff_arr = json_decode(file_get_contents("http://".$this->ReadPropertyString("IPAddress")."/json/eff"), true);

                foreach ($eff_arr as $item => $key) {
                    IPS_SetVariableProfileAssociation("WLED.Effects", $key, $item, "", -1);
                }
            }

            if(!IPS_VariableProfileExists("WLED.Palettes")){
                IPS_CreateVariableProfile("WLED.Palettes", 1);

                $eff_arr = json_decode(file_get_contents("http://".$this->ReadPropertyString("IPAddress")."/json/pal"), true);

                foreach ($eff_arr as $item => $key) {
                    IPS_SetVariableProfileAssociation("WLED.Palettes", $key, $item, "", -1);
                }
            }
        }

        if(!IPS_VariableProfileExists("WLED.Temperature")){
            IPS_CreateVariableProfile("WLED.Temperature", 1);
            IPS_SetVariableProfileValues("WLED.Temperature", 1900, 10091, 1);
        }

        $this->SetStatus(102);
    }

    private function SendData($jsonString)
    {
        $this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "FrameTyp" => 1, "Fin" => true, "Buffer" => utf8_decode($jsonString))));
        $this->SendDebug(__FUNCTION__, $jsonString, 0);
    }
    private function SendDataToSegment($jsonString)
    {
        $this->SendDataToChildren(json_encode(Array("DataID" => "{D2353839-DA64-DF79-7CD5-4DD827DCE82A}", "FrameTyp" => 1, "Fin" => true, "Buffer" => utf8_decode($jsonString))));
        $this->SendDebug(__FUNCTION__, $jsonString, 0);
    }

    public function ReceiveData($JSONString)
    {
        $data = json_decode($JSONString);
        $this->SendDebug(__FUNCTION__, $data->Buffer, 0);

        //sicher, falls ein Modul die aktuellen daten anfordert.
        $this->SetBuffer("Buffer", $data->Buffer);

        $j_data = json_decode($data->Buffer, true);

        if(!array_key_exists("state", $j_data)) return;
        if(!array_key_exists("seg", $j_data["state"])) return;
        if(!is_array($j_data["state"]["seg"])) return;

        foreach($j_data["state"]["seg"] as $item){
            $this->SendDataToSegment(json_encode($item));
        }
    }

    public function ForwardData($JSONString) {
        $data = json_decode($JSONString);
        $this->SendDebug(__FUNCTION__, $data->Buffer, 0);
        $buffer = $data->Buffer;
        $data = json_decode($data->Buffer, true);

        if(array_key_exists("cmd", $data)) {
            switch (strtolower($data["cmd"])){
                case "update":
                    $this->SendData('{}');
                    break;
                default:
                    throw new Exception("CMD NOT SET!");
            }
        }else{
            //alles weiterleiten wenn kein CMD gesetzt!
            $this->SendData($buffer);
        }
    }

    public function GetConfigurationForParent()
    {
        $this->SendDebug(__FUNCTION__, '', 0);
        $ipAdress = $this->ReadPropertyString("IPAddress");
        $active = $this->ReadPropertyBoolean("Active");
        $address = "ws://" . $ipAdress . ":/ws";

        $Config = array(
            "Active"         => $active,
            "URL"          => $address //,
            //"VerifyCertificate"     => $VerifyCertificate
        );

        return json_encode($Config);
    }

    Private function UpdateConfigurationForParent()
    {
        $this->SendDebug(__FUNCTION__, '', 0);
        $ParentId = @IPS_GetInstance($this->InstanceID)['ConnectionID'];
        $this->SendDebug("Force Update Websocket", $ParentId, 0);
        $Script = 'IPS_SetConfiguration(' . $ParentId . ', \'' . $this->GetConfigurationForParent() . '\');' . PHP_EOL;
        $Script .= 'IPS_ApplyChanges(' . $ParentId . ');';
        IPS_RunScriptText($Script);
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
