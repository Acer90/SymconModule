<?
// Klassendefinition
class SymconAlarmClock extends IPSModule
{

    public function __construct($InstanceID)
    {
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
        $this->RegisterVariableBoolean("AlarmON", "Eingeschaltet", "~Switch",0);
        $this->EnableAction("AlarmON");

        //$this->UpdateMessageSink();
        //$this->UpdateGroups();

        //event erstellen
        $this->RegisterTimer("CheckStatus", $this->ReadPropertyInteger("Interval"), 'SymconAlarmClock_CheckStatus($_IPS[\'TARGET\']);');
        $this->SetTimerInterval("CheckStatus", $this->ReadPropertyInteger("Interval") * 1000);

        $this->UpdateMessageSink();
        $this->UpdateGroups();

    }

    public function Test()
    {
        $this->UpdateMessageSink();

        //$this->UpdateData();
        $this->SetStatus(102);
    }

    public function CheckStatus()
    {
    }
}