<?
    // Klassendefinition
    class IPSWINSNMP extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }

        public function Create() {
            parent::Create();

            // Modul-Eigenschaftserstellung
            $this->RegisterPropertyString("SNMPIPAddress", "192.168.178.1"); 
            $this->RegisterPropertyInteger("SNMPPort", 161);
            $this->RegisterPropertyInteger("SNMPTimeout", 1);
            $this->RegisterPropertyInteger("SNMPInterval", 10);
            $this->RegisterPropertyString("SNMPVersion", "2c");

            $this->RegisterPropertyString("SNMPCommunity", "public"); 

            $this->RegisterPropertyString("SNMPSecurityName", "SomeName");
            $this->RegisterPropertyString("SNMPAuthenticationProtocol", "SHA"); 
            $this->RegisterPropertyString("SNMPAuthenticationPassword", "SomeAuthPass"); 
            $this->RegisterPropertyString("SNMPPrivacyProtocol", "DES"); 
            $this->RegisterPropertyString("SNMPPrivacyPassword", "SomePrivPass"); 

            $this->RegisterPropertyInteger("SNMPEngineID", "0"); 
            $this->RegisterPropertyString("SNMPContextName", ""); 

            $this->RegisterPropertyInteger("ArchivID", "0");

            //create Skript
            $ScriptID = IPS_CreateScript(0);
            IPS_SetName($ScriptID, "Action Script");
            $data = file_get_contents(dirname(__FILE__). "\\action.php");
            IPS_SetScriptContent($ScriptID, $data);
            IPS_SetDisabled($ScriptID, true);
            IPS_SetHidden($ScriptID, true);
            IPS_SetParent($ScriptID, $this->InstanceID);

            $this->RegisterPropertyInteger("SkriptID", $ScriptID);

            $this->RegisterPropertyString("Devices", ""); 

            //event erstellen
            $this->RegisterTimer("SyncData", $this->ReadPropertyInteger("SNMPInterval"), 'IPSWINSNMP_SyncData($_IPS[\'TARGET\']);');

            //Profile erstellen
            if (!IPS_VariableProfileExists("SNMP_Watt")){
                IPS_CreateVariableProfile("SNMP_Watt", 2);
                IPS_SetVariableProfileDigits("SNMP_Watt", 1);
                IPS_SetVariableProfileText("SNMP_Watt", "", "W");
            }
        }

        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();
            //$this->RequireParent("{1A75660D-48AE-4B89-B351-957CAEBEF22D}");

            $this->SetTimerInterval("SyncData", $this->ReadPropertyInteger("SNMPInterval")*1000);
        }

        public function ReadSNMP($oid) {
            $Filedir = dirname(__FILE__). "\\bin\\". "SnmpGet.exe";
            $re = '/(?<typ>.+)=(?<value>.+)/m';

            $SNMPIPAddress = $this->ReadPropertyString("SNMPIPAddress");
            $SNMPPort = $this->ReadPropertyInteger("SNMPPort");
            $SNMPTimeout = $this->ReadPropertyInteger("SNMPTimeout");
            $SNMPVersion = $this->ReadPropertyString("SNMPVersion");

            if($SNMPVersion == "3") {
                $SNMPSecurityName = $this->ReadPropertyString("SNMPSecurityName");
                $SNMPAuthenticationProtocol = $this->ReadPropertyString("SNMPAuthenticationProtocol");
                $SNMPAuthenticationPassword = $this->ReadPropertyString("SNMPAuthenticationPassword");
                $SNMPPrivacyProtocol = $this->ReadPropertyString("SNMPPrivacyProtocol");
                $SNMPPrivacyPassword = $this->ReadPropertyString("SNMPPrivacyPassword");
                $SNMPEngineID = $this->ReadPropertyInteger("SNMPEngineID");
                $SNMPContextName = $this->ReadPropertyString("SNMPContextName");
                $SNMPContextEngine = $this->ReadPropertyInteger("SNMPContextEngine");
            }else{
                $SNMPCommunity = $this->ReadPropertyString("SNMPCommunity");

                $Parameters = '-r:' . $SNMPIPAddress.' -p:'.$SNMPPort.' -t:'.$SNMPTimeout.' -c:"'.$SNMPCommunity.'"' .' -o:.' . $oid;
                $out = IPS_Execute($Filedir , $Parameters, FALSE, TRUE);
            }

            switch (true){
                case stristr($out,'%Invalid parameter'):
                    $this->SetStatus(201);
                    return '';
                case stristr($out,'%Failed to get value of SNMP variable. Timeout.'):
                    $this->SetStatus(103);
                    return '';
                case stristr($out,'Variable does not exist'):
                    $this->SetStatus(202);
                    return '';
                default:
                    preg_match_all($re, $out, $out);
                    break;
            } 

            if(!array_key_exists("value", $out) && count($out["value"]) != 3) {
                $this->SetStatus(203);
                return "";
            }
            return $rdata = array("Type" => $out["value"][1], "Value" => $out["value"][2]);
            print_r($rdata);
        }
        public function WriteSNMP($oid, $value, $type = "str") {
            $Filedir = dirname(__FILE__). "\\bin\\". "SnmpSet.exe";

            $SNMPIPAddress = $this->ReadPropertyString("SNMPIPAddress");
            $SNMPPort = $this->ReadPropertyInteger("SNMPPort");
            $SNMPTimeout = $this->ReadPropertyInteger("SNMPTimeout");
            $SNMPVersion = $this->ReadPropertyString("SNMPVersion");

            if($SNMPVersion == "3") {
                $SNMPSecurityName = $this->ReadPropertyString("SNMPSecurityName");
                $SNMPAuthenticationProtocol = $this->ReadPropertyString("SNMPAuthenticationProtocol");
                $SNMPAuthenticationPassword = $this->ReadPropertyString("SNMPAuthenticationPassword");
                $SNMPPrivacyProtocol = $this->ReadPropertyString("SNMPPrivacyProtocol");
                $SNMPPrivacyPassword = $this->ReadPropertyString("SNMPPrivacyPassword");
                $SNMPEngineID = $this->ReadPropertyInteger("SNMPEngineID");
                $SNMPContextName = $this->ReadPropertyString("SNMPContextName");
                $SNMPContextEngine = $this->ReadPropertyInteger("SNMPContextEngine");
            }else{
                $SNMPCommunity = $this->ReadPropertyString("SNMPCommunity");

                $Parameters = '-r:' . $SNMPIPAddress.' -p:'.$SNMPPort.' -t:'.$SNMPTimeout.' -c:"'.$SNMPCommunity.'"' .' -o:.' . $oid.' -val:'.$value.' -tp:'.$type;
                $out = IPS_Execute($Filedir , $Parameters, FALSE, TRUE);
            }

            switch (true){
                case stristr($out,'%Invalid parameter'):
                    $this->SetStatus(201);
                    return FALSE;
                case stristr($out,'%Failed to get value of SNMP variable. Timeout.'):
                    $this->SetStatus(103);
                    return FALSE;
                case stristr($out,'Variable does not exist'):
                    $this->SetStatus(202);
                    return FALSE;
                case stristr($out,'Failed to set value to SNMP variable. Bad value'):
                    $this->SetStatus(204);
                    return FALSE;
                case stristr($out,'OK'):
                    $this->SetStatus(102);
                    return TRUE;    
                default:
                    return FALSE;
            } 
        }

        public function ChangeValue($instance, $value){
            $DevicesString = $this->ReadPropertyString("Devices");
            $Devices = json_decode($DevicesString, true);
            $id = $this->InstanceID;

            $key = array_search($instance, array_column($Devices, 'instanceID'));
            if(is_null($key)) return FALSE;

            switch ($Devices[$key]["typ"]){
                case "switch":
                    if($value) $value = 1; else $value = 0;
                    return IPSWINSNMP_WriteSNMP($id, $Devices[$key]["oid"], $value, $Devices[$key]["var"]);
                case "switch12":
                    if($value) $value = 1; else $value = 2;
                    return IPSWINSNMP_WriteSNMP($id, $Devices[$key]["oid"], $value, $Devices[$key]["var"]);
                case "mWtoW":
                    $value = (Int)($value * 1000);
                    return IPSWINSNMP_WriteSNMP($id, $Devices[$key]["oid"], $value, $Devices[$key]["var"]);
                default:
                    return IPSWINSNMP_WriteSNMP($id, $Devices[$key]["oid"], $value, $Devices[$key]["var"]);
            }
        }

        public function SyncData(){
            $id = $this->InstanceID;
            $this->SetStatus(102);
            $change = false;
            $DevicesString = $this->ReadPropertyString("Devices");
            $ArchivId = $this->ReadPropertyInteger("ArchivID");
            $Devices = json_decode($DevicesString, true);
            //print_r($Devices);
            foreach ($Devices as &$Device) {
                $instanceID = $Device["instanceID"];
                $name = $Device["name"];
                $oid = $Device["oid"];
                $typ = $Device["typ"];

                if(!empty($name) && !empty($oid)){
                    $rdata = IPSWINSNMP_ReadSNMP($id, $oid);

                    if(!is_array($rdata)) continue;
                    if(!IPS_VariableExists($instanceID)){
                        $vartyp = "";
                        $varid = 0;
                        $change = true;
                        $allow_use = false;
                        
                        switch (true){
                            case stristr($rdata["Type"],'NsapAddress'):
                                //Boolean anlegen
                                $varid = IPS_CreateVariable(3);
                                $vartyp = "str";
                                break;
                            case stristr($rdata["Type"],'IpAddress'):
                                //Boolean anlegen
                                $varid = IPS_CreateVariable(3);
                                $vartyp = "ip";
                                break;
                            case stristr($rdata["Type"],'Bit String'):
                                //Boolean anlegen
                                $varid = IPS_CreateVariable(3);
                                $vartyp = "hex";
                                break;
                            case stristr($rdata["Type"],'Integer') && !stristr($typ,'UInteger'):
                                //Integer anlegen
                                switch($typ){
                                    case "mWtoW":
                                        $varid = IPS_CreateVariable(2);
                                        IPS_SetVariableCustomProfile($varid, "SNMP_Watt");
                                        if(IPS_InstanceExists($ArchivId)){
                                            AC_SetLoggingStatus($ArchivId, $varid, true);
                                            IPS_ApplyChanges($ArchivId);
                                        } 
                                    break;
                                    case "switch"  || "switch12":
                                        $varid = IPS_CreateVariable(0);
                                        IPS_SetVariableCustomProfile($varid, "~Switch");
                                        $allow_use = true;
                                    break;
                                    default:
                                        $varid = IPS_CreateVariable(1);
                                    break;
                                }
                                $vartyp = "int";
                                break;
                            case stristr($rdata["Type"],'Gauge'):
                                //Integer anlegen
                                $varid = IPS_CreateVariable(1);
                                $vartyp = "uint";
                                break;
                            case stristr($rdata["Type"],'Counter'):
                                //Integer anlegen
                                $varid = IPS_CreateVariable(1);
                                $vartyp = "int";
                                break;
                            case stristr($rdata["Type"],'UInteger'):
                                //Integer anlegen
                                $varid = IPS_CreateVariable(1);
                                $vartyp = "uint";
                                break;
                            case stristr($rdata["Type"],'Object Identifier'):
                                //Integer anlegen
                                $varid = IPS_CreateVariable(1);
                                $vartyp = "oid";
                                break;
                            case stristr($rdata["Type"],'TimeTicks'):
                                //Float anlegen
                                $varid = IPS_CreateVariable(3);
                                $vartyp = "uint";
                                break;
                            case stristr($rdata["Type"],'Octet String'):
                                //Float anlegen
                                $varid = IPS_CreateVariable(3);
                                $vartyp = "str";
                                break;
                        }

                        if(empty($vartyp) || $varid == 0) continue;
                        IPS_SetName($varid, $name); 
                        IPS_SetParent($varid, $id);
                        IPS_SetDisabled($varid, $allow_use);

                        $Device["instanceID"] = $varid;
                        $Device["var"] = $vartyp;
                        $instanceID = $varid;
                    }
                    switch($typ){
                        case "mWtoW":
                            $value = $rdata["Value"] / 1000;
                            if(is_numeric($rdata["Value"])) {
                                SetValue($instanceID, $value);

                                if($value == 0) IPS_SetHidden($instanceID, true); else IPS_SetHidden($instanceID, false);
                            }
                        break;
                        case "switch" || "switch12":
                            if(is_numeric($rdata["Value"]) && $rdata["Value"] == 1) SetValue($instanceID, true); else SetValue($instanceID, false);
                        break;
                        default:
                            if(GetValue($instanceID) != $rdata["Value"]) SetValue($instanceID, $rdata["Value"]);
                        break;
                    }
                }

                if($change){
                    IPS_SetProperty($id, "Devices", json_encode($Devices));
                    IPS_ApplyChanges($id);
                } 
            }       
        }
    }
?>