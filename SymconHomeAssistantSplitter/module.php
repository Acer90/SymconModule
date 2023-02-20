<?php

class HomeAssistantSplitter extends IPSModule
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
        $this->RegisterPropertyInteger("Port", 8123);
        $this->RegisterPropertyString("Token", "");

        $this->RequireParent("{D68FD31F-0E90-7019-F16C-1949BD3079EF}");
    }

    public function ApplyChanges()
    {
        $this->RegisterMessage(0, 10001 /* IPS_KERNELSTARTED */);
        // Diese Zeile nicht löschen
        parent::ApplyChanges();

        if (IPS_GetKernelRunlevel() == 10103 /* KR_READY */) {
            $this->UpdateConfigurationForParent();
        }

        $this->SetBuffer("IndexCounter", 0);
        $this->SetStatus(102);
    }

    public function SendData($jsonString, $updateIndex = true)
    {
        if($updateIndex){
            $data = json_decode($jsonString, true);
            $id = $this->GetBuffer("IndexCounter");
            $data["id"] = $id++;
            $jsonString = json_encode($data);
            $this->SetBuffer("IndexCounter", $id);
        }
        
        $this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "FrameTyp" => 1, "Fin" => true, "Buffer" => utf8_decode($jsonString))));
        $this->SendDebug(__FUNCTION__, $jsonString, 0);
    }
    private function SendDataToDevice($jsonString)
    {
        $this->SendDataToChildren(json_encode(Array("DataID" => "{A9C72BC2-FB94-8451-6DC9-9790AEFC11B3}", "FrameTyp" => 1, "Fin" => true, "Buffer" => utf8_decode($jsonString))));
        $this->SendDebug(__FUNCTION__, $jsonString, 0);
    }

    private function SendDataToConfigurator($jsonString)
    {
        $this->SendDataToChildren(json_encode(Array("DataID" => "{5DEC82E8-8FAB-AAB1-0BFF-B7BD222DE7E9}", "FrameTyp" => 1, "Fin" => true, "Buffer" => utf8_decode($jsonString))));
        $this->SendDebug(__FUNCTION__, $jsonString, 0);
    }

    public function ReceiveData($JSONString)
    {
        $data = json_decode($JSONString);
        $this->SendDebug(__FUNCTION__, $data->Buffer, 0);

        $j_data = json_decode($data->Buffer, true);

        if(!array_key_exists("type", $j_data)) {
            $this->SendDebug(__FUNCTION__, "TYPE NOT FOUND!", 0);
            return;
        }

        switch($j_data["type"]){
            case "auth_required":
                //token senden zum authentifizieren
                $token = $this->ReadPropertyString("Token");
                if(empty($token)){
                    $this->SetStatus(105);
                }else{
                    $data = array("type" => "auth", "access_token" => $token);
                    $this->SendData(json_encode($data), false);
                }
                break;
            case "auth_invalid":
                    $this->SetStatus(106);
                    $this->SendDebug(__FUNCTION__, "Authentication Failed! (" .$j_data["message"] . ")", 0);
                    break;
            case "auth_ok":
                //nach der Autentifizierung request initialisieren
                $this->SetBuffer("IndexCounter", 1);
                $this->initializeRequests();
                break;
            case "result":
                //{"id":1,"type":"result","success":true,"result":null}
                if(!$j_data["success"]){
                    $this->SendDebug(__FUNCTION__, "RESULT ERRPOR => " . $j_data["result"], 0);
                }
                break;
            case "event":
                //https://developers.home-assistant.io/docs/api/websocket/#subscribe-to-events
                $this->updateEvent($j_data["event"]);
                break;
            default:
                $this->SendDebug(__FUNCTION__, "TYPE => ". $j_data["type"]. " NOT DEFINED!", 0);
                break;
        }

    }

    public function ForwardData($JSONString) {
        $data = json_decode($JSONString);
        $this->SendDebug(__FUNCTION__, $data->Buffer, 0);
        $data = json_decode($data->Buffer, true);

    }

    private function initializeRequests(){
        //Subscribe all events
        $data = array("type"=> "subscribe_events");
        $this->SendData(json_encode($data));
    }

    private function updateEvent($eventData){
        if(array_key_exists("data",$eventData) && is_array($eventData["data"]) && count($eventData["data"]) > 0){
            // nur wenn data vorhanden sind daten verarbeiten
            $data = $eventData["data"];
            if(!array_key_exists("entity_id", $data) && empty($data["entity_id"])) {
                $this->SendDebug(__FUNCTION__, "NO ENTITY ID FOUND!", 0);
                return;
            }
            $entity_id = $data["entity_id"];

            $this->SendDataToConfigurator(json_encode(array("cmd" => "addDevice", "entity_id" => $entity_id)));

            $s_data = array();
            $s_data["id"] = $entity_id;
            $s_data["data"] = $data;

            $this->SendDataToDevice(json_encode($s_data));
        }
    }

    public function GetConfigurationForParent()
    {
        $this->SendDebug(__FUNCTION__, '', 0);
        $ipAdress = $this->ReadPropertyString("IPAddress");
        $port = $this->ReadPropertyInteger("Port");
        $active = $this->ReadPropertyBoolean("Active");
        $address = "ws://" . $ipAdress . ":" . $port. "/api/websocket";
        $token = $this->ReadPropertyString("Token");
        if(empty($token)){
            //$this->SetStatus(105);
            $active = false;
        }

        $Config = array(
            "Active"         => $active,
            "URL"          => $address,
            "VerifyCertificate"  => false
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
