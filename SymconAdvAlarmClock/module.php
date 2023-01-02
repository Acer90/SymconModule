<?php
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
        $this->RegisterPropertyInteger("ICalInstanceID", 0);
        $this->RegisterPropertyInteger("GoogleMapsInstanceID", 0);
        $this->RegisterPropertyInteger("AlternativVariableID", 0);

        $this->RegisterPropertyString("List_Actions", "");
        $this->RegisterPropertyBoolean("Debug", false);

        $this->RegisterVariableBoolean("Aktive", "Eingeschaltet", "~Switch",1);
        $this->RegisterVariableInteger("Time", "Weckzeit", "~UnixTimestampTime",2);

        $this->RegisterVariableBoolean("Day_Mo", "Montag", "~Switch",3);
        $this->RegisterVariableBoolean("Day_Di", "Dienstag", "~Switch",4);
        $this->RegisterVariableBoolean("Day_Mi", "Mittwoch", "~Switch",5);
        $this->RegisterVariableBoolean("Day_Do", "Donnerstag", "~Switch",6);
        $this->RegisterVariableBoolean("Day_Fr", "Freitag", "~Switch",7);
        $this->RegisterVariableBoolean("Day_Sa", "Samstag", "~Switch",8);
        $this->RegisterVariableBoolean("Day_So", "Sonntag", "~Switch",9);

        $this->RegisterAttributeBoolean("runNow", false);

        //event erstellen
        $this->RegisterTimer("CheckStatus", $this->ReadPropertyInteger("Interval"), 'SymconAlarmClock_CheckStatus($_IPS[\'TARGET\']);');
    }

    public function ApplyChanges(){
        parent::ApplyChanges();

        $this->SetTimerInterval("CheckStatus", $this->ReadPropertyInteger("Interval") * 1000);
    }

    public function Test()
    {

        $this->SetStatus(102);
    }

    public function CheckStatus()
    {

    }


}