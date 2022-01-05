<?php
    // Klassendefinition
    class SymconBlueIrisSystem extends IPSModule {
        public function __construct(int $InstanceID) {
            parent::__construct($InstanceID);
        }

        public function Create() {
            parent::Create();

            $this->ConnectParent("{E138AFDC-D1E0-B462-A5E5-AF24F57D4686}");

            // Modul-Eigenschaftserstellung
            $this->RegisterPropertyBoolean("GetClips", false);
            $this->RegisterPropertyBoolean("GetAlerts", false);
            $this->RegisterPropertyBoolean("GetLog", false);

            $this->RegisterPropertyInteger("Interval", 60);

            $this->RegisterTimer("SyncData", $this->ReadPropertyInteger("Interval"), 'SymconBlueIrisSystem_SyncData($_IPS[\'TARGET\']);');
        }

        public function ApplyChanges()
        {
            //Never delete this line!
            parent::ApplyChanges();

            if (!IPS_VariableProfileExists("BlueIris.Mb")){
                IPS_CreateVariableProfile("BlueIris.Mb", 2);
                IPS_SetVariableProfileDigits("BlueIris.Mb", 1);
                IPS_SetVariableProfileText("BlueIris.Mb", "", " MByte");
            }

            if (!IPS_VariableProfileExists("BlueIris.Profil")) {
                IPS_CreateVariableProfile("BlueIris.Profil", 1);
                IPS_SetVariableProfileAssociation("BlueIris.Profil", 0,  $this->Translate("Inactive"), "", 0xff0000);
                IPS_SetVariableProfileAssociation("BlueIris.Profil", 1, $this->Translate("Profile"). " 1", "", 0x00ff00);
                IPS_SetVariableProfileAssociation("BlueIris.Profil", 2, $this->Translate("Profile"). " 2", "", 0x00ff00);
                IPS_SetVariableProfileAssociation("BlueIris.Profil", 3, $this->Translate("Profile"). " 3", "", 0x00ff00);
                IPS_SetVariableProfileAssociation("BlueIris.Profil", 4, $this->Translate("Profile"). " 4", "", 0x00ff00);
                IPS_SetVariableProfileAssociation("BlueIris.Profil", 5, $this->Translate("Profile"). " 5", "", 0x00ff00);
                IPS_SetVariableProfileAssociation("BlueIris.Profil", 6, $this->Translate("Profile"). " 6", "", 0x00ff00);
                IPS_SetVariableProfileAssociation("BlueIris.Profil", 7, $this->Translate("Profile"). " 7", "", 0x00ff00);
            }

            $this->RegisterVariableInteger("Profile",  $this->Translate("Profile"), "BlueIris.Profil", 1);
            $this->RegisterVariableInteger("CPU",  $this->Translate("CPU"), "~Intensity.100", 2);
            $this->RegisterVariableFloat("Mem",  $this->Translate("Memory"), "BlueIris.Mb", 3);
            $this->RegisterVariableString("Uptime",  $this->Translate("Uptime"), "", 4);
            $this->RegisterVariableString("Schedule",  $this->Translate("Schedule"), "", 4);

            if($this->ReadPropertyBoolean("GetClips")){
                $this->RegisterVariableString("Clips",  $this->Translate("Clips"), "", 11);
            }

            if($this->ReadPropertyBoolean("GetAlerts")){
                $this->RegisterVariableString("Alerts",  $this->Translate("Alerts"), "", 12);
            }

            if($this->ReadPropertyBoolean("GetLog")){
                $this->RegisterVariableString("Log",  $this->Translate("Logs"), "", 13);
            }

            //$this->EnableAction("Profile"); //Aktuelle ohne Funktion deshalb deaktiviert!!!

            if($this->ReadPropertyBoolean("GetClips") || $this->ReadPropertyBoolean("GetAlerts") || $this->ReadPropertyBoolean("GetLog")){
                $this->SetTimerInterval("SyncData", $this->ReadPropertyInteger("Interval")*1000);
            }else{
                //timer daktivieren wenn alle 3 Schaltflächen False sind!
                $this->SetTimerInterval("SyncData", 0);
            }

            $this->SetStatus(102);
        }

        public function RequestAction($Ident, $Value) {
            switch($Ident) {
                case "Profile":
                    //Aktuelle ohne Funktion deshalb deaktiviert!!!
                    $result = json_decode($this->Status(null, $Value), true);
                    $this->SendDebug(__FUNCTION__, json_encode($result), 0);
                    $this->SetValue($Ident, $result["profile"]);
                    break;
                default:
                    throw new Exception("Invalid Ident");
            }
        }

        public function ReceiveData($JSONString)
        {
            //$this->SendDebug(__FUNCTION__, $JSONString, 0);
            $rData = json_decode($JSONString, true);
            $buffer = json_decode($rData["Buffer"], true);

            switch($buffer['cmd']) {
                case "Status":
                    //Update status
                    $this->SetValue("Profile", $buffer["payload"]["profile"]);
                    $this->SetValue("CPU", $buffer["payload"]["cpu"]);
                    $this->SetValue("Mem", ($buffer["payload"]["ram"] / 1024 / 1024));
                    $this->SetValue("Uptime", $buffer["payload"]["uptime"]);
                    $this->SetValue("Schedule", $buffer["payload"]["schedule"]);
                    break;
                default:
                    $this->SendDebug(__FUNCTION__, "ACTION " . $buffer['cmd'] . " FOR THIS MODULE NOT DEFINED!", 0);
                    break;
            }
        }


        public function AlertList(int $startdate = null, bool $reset = null){
            $camera = $this->ReadPropertyString("ShortName");

            $data = array();
            $data["camera"] = "index";
            if(is_null($camera)) $camera = "index";
            if(is_null($startdate)) $startdate = 0;
            if(is_null($reset)) $reset = false;

            $sendData = array("cmd" => "AlertList", "data" => $data);
            return $this->SendDataToParent(json_encode([
                'DataID' => "{8AB1599B-63C9-6E0E-864E-98A562E1CBC9}",
                'Buffer' => utf8_encode(json_encode($sendData))
            ]));
        }
        public function ClipList(int $startdate = null, int $enddate = null, bool $tiles = null){
            $data = array();
            $data["camera"] = "index";
            if(is_null($startdate)) $startdate = 0;
            if(is_null($enddate)) $enddate = time();
            if(is_null($tiles)) $tiles = false;

            $sendData = array("cmd" => "ClipList","data" => $data);
            return $this->SendDataToParent(json_encode([
                'DataID' => "{8AB1599B-63C9-6E0E-864E-98A562E1CBC9}",
                'Buffer' => utf8_encode(json_encode($sendData))
            ]));
        }
        public function Status(int $signal = null, int $profil = null, string $dio = null, string $play = null){
            $data = array();

            if(!is_null($profil)) $data["profile"] = $profil;
            if(!is_null($signal)) $data["signal"] = $signal;
            if(!is_null($dio)) $data["dio"] = $dio;
            if(!is_null($play)) $data["play"] = $play;

            $sendData = array("cmd" => "Status","data" => $data);
            return $this->SendDataToParent(json_encode([
                'DataID' => "{8AB1599B-63C9-6E0E-864E-98A562E1CBC9}",
                'Buffer' => utf8_encode(json_encode($sendData))
            ]));
        }
        public function Log(){
            $sendData = array("cmd" => "Log");
            return $this->SendDataToParent(json_encode([
                'DataID' => "{8AB1599B-63C9-6E0E-864E-98A562E1CBC9}",
                'Buffer' => utf8_encode(json_encode($sendData))
            ]));
        }
        public function SysConfig(bool $archive = null, bool $schedule = null){
            $data = array();
            $data["camera"] = "index";
            if(is_null($archive)) $data["archive"] = $archive;
            if(is_null($schedule)) $data["schedule"] = $schedule;

            $sendData = array("cmd" => "SysConfig", "data" => $data);
            return $this->SendDataToParent(json_encode([
                'DataID' => "{8AB1599B-63C9-6E0E-864E-98A562E1CBC9}",
                'Buffer' => utf8_encode(json_encode($sendData))
            ]));
        }

        public function SyncData(){
            if($this->ReadPropertyBoolean("GetClips")){
                $this->SetValue("Clips", $this->ClipList());
            }

            if($this->ReadPropertyBoolean("GetAlerts")){
                $this->SetValue("Alerts", $this->AlertList());
            }

            if($this->ReadPropertyBoolean("GetLog")){
                $this->SetValue("Log", $this->Log());
            }
        }
    }
