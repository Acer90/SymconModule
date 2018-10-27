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
        $this->RegisterPropertyInteger("Timeout", 10);
        $this->RegisterPropertyString("VarList", "");
        $this->RegisterPropertyBoolean("Debug", false);

        //event erstellen
        $this->RegisterTimer("CheckAlarm", $this->ReadPropertyInteger("Interval"), 'ViesmannOpenV_SyncData($_IPS[\'TARGET\']);');
    }

    public function ApplyChanges() {
        // Diese Zeile nicht löschen
        parent::ApplyChanges();

        $this->SetStatus(102);

        $this->SetTimerInterval("CheckAlarm", $this->ReadPropertyInteger("Interval")*1000);
    }

    public function Test()
    {
        //return $this->SendData($hexstamp, $bytes, $read_only, $return_data, $value);

    }

    public function SyncData()
    {
        $this->SetStatus(102);
        $VarList = $this->ReadPropertyString("VarList");
        $VarList = json_decode($VarList, true);


        foreach ($VarList as $item) {
            $name = $item["Name"];

        }
    }

}