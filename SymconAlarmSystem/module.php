<?
// Klassendefinition
class ViesmannOpenV extends IPSModule {

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

        $this->ConnectParent("{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}");
        $this->GetConfigurationForParent();

        $BufferList = $this->GetBufferList();
        if(!in_array("Return", $BufferList)) $this->SetBuffer ("Return", json_encode(array()));

        //event erstellen
        $this->RegisterTimer("SyncData", $this->ReadPropertyInteger("Interval"), 'ViesmannOpenV_SyncData($_IPS[\'TARGET\']);');
    }

    public function ApplyChanges() {
        // Diese Zeile nicht löschen
        parent::ApplyChanges();

        $this->SetStatus(102);

        $this->ConnectParent("{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}");
        $this->GetConfigurationForParent();

        $BufferList = $this->GetBufferList();
        if(!in_array("Return", $BufferList)) $this->SetBuffer ("Return", json_encode(array()));

        $this->SetTimerInterval("SyncData", $this->ReadPropertyInteger("Interval")*1000);

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