<?
// Klassendefinition
class SymconAlarmSystem extends IPSModule {

    public function __construct($InstanceID) {
        parent::__construct($InstanceID);
    }

    public function Create()
    {
        parent::Create();

        $this->RegisterPropertyInteger("Interval", 10);
        $this->RegisterPropertyInteger("P_Alarm", 10);
        $this->RegisterPropertyInteger("P_Warn", 5);
        $this->RegisterPropertyString("VarList", "");
        $this->RegisterPropertyBoolean("Debug", false);

        if (!IPS_VariableProfileExists("SymconAlarmSystem_Level") || !IPS_VariableProfileExists("SymconAlarmSystem_Status")) $this->UpdateProfil();
        $this->RegisterVariableFloat("AlarmLevel", "AlarmLevel", "SymconAlarmSystem_Level", 0);
        $this->RegisterVariableFloat("AlarmStatus", "AlarmStatus", "SymconAlarmSystem_Status", 0);

        $this->UpdateMessageSink();

        //event erstellen
        $this->RegisterTimer("UpdateData", $this->ReadPropertyInteger("Interval"), 'SymconAlarmSystem_UpdateData($_IPS[\'TARGET\']);');
    }

    public function ApplyChanges() {
        // Diese Zeile nicht löschen
        parent::ApplyChanges();

        $this->SetStatus(102);

        $this->SetTimerInterval("UpdateData", $this->ReadPropertyInteger("Interval")*1000);
    }

    public function Test()
    {
        $this->UpdateMessageSink();

    }

    public function UpdateProfil(){
        $alarm = $this->ReadPropertyInteger("P_Alarm");
        $warn = $this->ReadPropertyInteger("P_Warn");


        if (!IPS_VariableProfileExists("SymconAlarmSystem_Level")) {
            IPS_CreateVariableProfile("SymconAlarmSystem_Level", 2);
            IPS_SetVariableProfileDigits("SymconAlarmSystem_Level", 0);
        }

        if (!IPS_VariableProfileExists("SymconAlarmSystem_Status")){
            IPS_CreateVariableProfile("SymconAlarmSystem_Status", 2);
            IPS_SetVariableProfileDigits("SymconAlarmSystem_Status", 0);
            IPS_SetVariableProfileAssociation("SymconAlarmSystem_Status", 0,  "OK", "", 0x00cc00);
            IPS_SetVariableProfileAssociation("SymconAlarmSystem_Status", $warn,  "Warnung", "", 0xff9900);
            IPS_SetVariableProfileAssociation("SymconAlarmSystem_Status", $alarm,  "Alarm", "", 0xff3300);
        }else{
            $p_data = IPS_GetVariableProfile("SymconAlarmSystem_Status");

            foreach($p_data["Associations"] as $item){
                IPS_SetVariableProfileAssociation("SymconAlarmSystem_Status", $item["Value"],  "", "", 0x00cc00);
            }

            IPS_SetVariableProfileAssociation("SymconAlarmSystem_Status", 0,  "OK", "", 0x00cc00);
            IPS_SetVariableProfileAssociation("SymconAlarmSystem_Status", $warn,  "Warnung", "", 0xff9900);
            IPS_SetVariableProfileAssociation("SymconAlarmSystem_Status", $alarm,  "Alarm", "", 0xff3300);

        }
    }

    public function UpdateMessageSink(){
        $update = false;
        $BufferList = $this->GetBufferList();
        if(!in_array("OldData", $BufferList)) $this->SetBuffer ("OldData", $this->ReadPropertyString("VarList"));

        $old_data = json_decode($this->GetBuffer("OldData"), true);
        $new_data = json_decode($this->ReadPropertyString("VarList"), true);


        foreach ($new_data as $key => $item){
            if(empty($item["Name"]) || empty($item["Variable"]) || !IPS_VariableExists($item["Variable"])){
                $new_data[$key]["rowColor"] = "#ff0000";
                $update = true;
                continue;
            }

            if(array_key_exists("rowColor", $item)){
                unset($new_data[$key]["rowColor"]);
                $update = true;
            }

            $keys = array_keys(array_combine(array_keys($old_data), array_column($old_data, 'Variable')),$item["Variable"]);
            foreach ($keys as $key2 => $value){
                unset($old_data[$value]);
            }
            $this->SendDebug("RegisterMessage", $item["Variable"],0);
            $this->RegisterMessage ( $item["Variable"], 10603);

        }

        print_r($old_data);
        print_r($new_data);

        foreach ($old_data as $key => $item) {
            $this->UnregisterMessage($item["Variable"], 10603);
            $this->SendDebug("UnregisterMessage", $item["Variable"],0);
        }


        if($update){
            IPS_SetProperty($this->InstanceID, "VarList" , json_encode($new_data, true));
            IPS_ApplyChanges($this->InstanceID);
        }

        $this->SetBuffer("OldData", json_encode($new_data, true));
    }

    public function UpdateData()
    {
        $this->SetStatus(102);
        $VarList = $this->ReadPropertyString("VarList");
        $VarList = json_decode($VarList, true);


        foreach ($VarList as $item) {
            $name = $item["Name"];

        }
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {

        //$this->SendDebug("MessageSink", "Message from SenderID ".$SenderID." with Message ".$Message."\r\n Data: ".print_r($Data, true), 0);

        switch ($Message)
        {
            case "VM_UPDATE":
                if ($SenderID != $this->ReadPropertyInteger('VarID'))
                    break;
                // $Data[0] Neuer Wert
                // $Data[1] true/false ob Änderung oder Aktualisierung.
                // $Data[2] Alter Wert
                break;
        }
    }

}