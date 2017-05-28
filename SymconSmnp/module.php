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

            $this->RegisterPropertyBoolean("status", false); 
            $this->RegisterPropertyBoolean("utilization", false);
            $this->RegisterPropertyString("utilizationtyp", ""); 

            //create Skript
            $script_found = IPS_GetScriptIDByName("Action Script", $this->InstanceID);
            if($script_found === FALSE){
                $ScriptID = IPS_CreateScript(0);
                IPS_SetName($ScriptID, "Action Script");
                $data = file_get_contents(dirname(__FILE__). "\\action.php");
                IPS_SetScriptContent($ScriptID, $data);
                IPS_SetDisabled($ScriptID, true);
                IPS_SetHidden($ScriptID, true);
                IPS_SetParent($ScriptID, $this->InstanceID);
            }else{
                $ScriptID = $script_found;
            }
            

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
            if (!IPS_VariableProfileExists("SNMP_PortStatus_100")){
                IPS_CreateVariableProfile("SNMP_PortStatus_100", 1);
                IPS_SetVariableProfileAssociation("SNMP_PortStatus_100", -1, "Offline", "", 0xff0000);
                IPS_SetVariableProfileAssociation("SNMP_PortStatus_100", 0, "Waiting", "", -1);
                IPS_SetVariableProfileAssociation("SNMP_PortStatus_100", 10, "10 Mbit", "", 0xffff00);
                IPS_SetVariableProfileAssociation("SNMP_PortStatus_100", 100, "100 Mbit", "", 0x00cc00);
            }
            if (!IPS_VariableProfileExists("SNMP_PortStatus_1000")){
                IPS_CreateVariableProfile("SNMP_PortStatus_1000", 1);
                IPS_SetVariableProfileAssociation("SNMP_PortStatus_1000", -1, "Offline", "", 0xff0000);
                IPS_SetVariableProfileAssociation("SNMP_PortStatus_1000", 0, "Waiting", "", -1);
                IPS_SetVariableProfileAssociation("SNMP_PortStatus_1000", 10, "10 Mbit", "", 0xffff00);
                IPS_SetVariableProfileAssociation("SNMP_PortStatus_1000", 100, "100 Mbit", "", 0x00cc00);
                IPS_SetVariableProfileAssociation("SNMP_PortStatus_1000", 1000, "1 Gbit", "", 0x0000cc);
            }
            if (!IPS_VariableProfileExists("SNMP_PortUtilization")){
                IPS_CreateVariableProfile("SNMP_PortUtilization", 2);
                IPS_SetVariableProfileDigits("SNMP_PortUtilization", 1);
                IPS_SetVariableProfileText("SNMP_PortUtilization", "", "%");
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
                    $this->SetStatus(205);
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
            if(stristr($Devices[$key]["oid"],'|')){
                $strarr = explode("|", $Devices[$key]["oid"]);
                if(count($strarr) < 2) return FALSE;
                $port_id = $strarr[1];
                if(!is_numeric($port_id)) return FALSE;

                switch($Devices[$key]["oid"]){
                    case stristr($Devices[$key]["oid"],'PortStatus100') || stristr($Devices[$key]["oid"],'PortStatus1000'):
                            if($value == -1){
                                return IPSWINSNMP_WriteSNMP($id, "1.3.6.1.2.1.2.2.1.7." .$port_id, 2, $Devices[$key]["var"]);
                            }else{
                                return IPSWINSNMP_WriteSNMP($id, "1.3.6.1.2.1.2.2.1.7." .$port_id, 1, $Devices[$key]["var"]);
                            }
                        break;
                }
            }else{
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
        }

        public function SyncData(){
            $id = $this->InstanceID;
            $this->SetStatus(102);
            $DevicesString = $this->ReadPropertyString("Devices");
            $ArchivId = $this->ReadPropertyInteger("ArchivID");
            $ScriptID = $this->ReadPropertyInteger("SkriptID");
            $Devices = json_decode($DevicesString, true);
            //print_r($Devices);
            foreach ($Devices as &$Device) {
                $instanceID = $Device["instanceID"];
                $name = $Device["name"];
                $oid = $Device["oid"];
                $typ = $Device["typ"];
                if(isset($Device["lastvalue"])) $lastvalue = $Device["lastvalue"]; else $lastvalue = 0;
                if(isset($Device["lastchange"])) $lastchange = $Device["lastchange"]; else $lastchange = 0;
                if(isset($Device["speed"])) $speed = $Device["speed"]; else $speed = 100;

                if(!empty($name) && !empty($oid)){
                    if(stristr($oid,'|')){
                        
                        $strarr = explode("|", $oid);
                        if(count($strarr) < 2) continue;
                        $port_id = $strarr[1];
                        if(!is_numeric($port_id)) continue;

                        if(!IPS_VariableExists($instanceID)){
                            switch($oid){
                                case stristr($oid,'PortStatus100') && !stristr($oid,'PortStatus1000'):
                                        $varid = IPS_CreateVariable(1);
                                        $vartyp = "int";
                                        IPS_SetName($varid, $name); 
                                        IPS_SetParent($varid, $id);
                                        IPS_SetVariableCustomProfile($varid, "SNMP_PortStatus_100");
                                        if(IPS_ScriptExists($ScriptID)) IPS_SetVariableCustomAction($varid, $ScriptID);

                                        $instanceID = $varid;
                                    break;
                                case stristr($oid,'PortStatus1000'):
                                        $varid = IPS_CreateVariable(1);
                                        $vartyp = "int";
                                        IPS_SetName($varid, $name); 
                                        IPS_SetParent($varid, $id);
                                        IPS_SetVariableCustomProfile($varid, "SNMP_PortStatus_1000");
                                        if(IPS_ScriptExists($ScriptID)) IPS_SetVariableCustomAction($varid, $ScriptID);

                                        $instanceID = $varid;
                                    break;
                                case stristr($oid,'PortUtilizationRX') || stristr($oid,'PortUtilizationTX') || stristr($oid,'PortUtilizationTRX') || stristr($oid,'PortUtilizationFD-TRX'):
                                        $varid = IPS_CreateVariable(2);
                                        $vartyp = "int";
                                        IPS_SetName($varid, $name); 
                                        IPS_SetParent($varid, $id);
                                        IPS_SetVariableCustomProfile($varid, "SNMP_PortUtilization");
                                        IPS_SetDisabled($varid, true);

                                        if(IPS_InstanceExists($ArchivId)){
                                            AC_SetLoggingStatus($ArchivId, $varid, true);
                                            IPS_ApplyChanges($ArchivId);
                                        } 

                                        $instanceID = $varid;
                                    break;
                                default:
                                    continue;
                            }
                            $Device["instanceID"] = $varid;
                            $Device["var"] = $vartyp;

                            IPS_SetProperty($id, "Devices", json_encode($Devices));
                            IPS_ApplyChanges($id);
                        }
                        
                        switch($oid){
                            case stristr($oid,'PortStatus100') && !stristr($oid,'PortStatus1000'):
                                    $rdata = IPSWINSNMP_ReadSNMP($id, "1.3.6.1.2.1.2.2.1.7." .$port_id); //read is Port Online
                                    if(!is_array($rdata)) continue;  
                                    if($rdata["Value"] == 2){
                                        SetValue($instanceID, -1);
                                        continue;
                                    }
                                    $rdata = IPSWINSNMP_ReadSNMP($id, "1.3.6.1.2.1.2.2.1.8." .$port_id); //read is Port Used
                                    if(!is_array($rdata)) continue;  
                                    if($rdata["Value"] == 2){
                                        SetValue($instanceID, 0);
                                        continue;
                                    }
                                    $rdata = IPSWINSNMP_ReadSNMP($id, "1.3.6.1.2.1.2.2.1.5." .$port_id); //read is Port Speed
                                    if(!is_array($rdata)) continue;
                                    switch($rdata["Value"]){
                                        case 10000000:
                                            SetValue($instanceID, 10);
                                            break;
                                        case 100000000:
                                            SetValue($instanceID, 100);
                                            break;
                                        default:
                                            SetValue($instanceID, -1);
                                    }  
                                break;
                                case stristr($oid,'PortStatus1000'):
                                    $rdata = IPSWINSNMP_ReadSNMP($id, "1.3.6.1.2.1.2.2.1.7." .$port_id); //read is Port Online
                                    if(!is_array($rdata)) continue;  
                                    if($rdata["Value"] == 2){
                                        SetValue($instanceID, -1);
                                        continue;
                                    }
                                    $rdata = IPSWINSNMP_ReadSNMP($id, "1.3.6.1.2.1.2.2.1.8." .$port_id); //read is Port Used
                                    if(!is_array($rdata)) continue;  
                                    if($rdata["Value"] == 2){
                                        SetValue($instanceID, 0);
                                        continue;
                                    }
                                    $rdata = IPSWINSNMP_ReadSNMP($id, "1.3.6.1.2.1.2.2.1.5." .$port_id); //read is Port Speed
                                    if(!is_array($rdata)) continue;
                                    switch($rdata["Value"]){
                                        case 10000000:
                                            SetValue($instanceID, 10);
                                            break;
                                        case 100000000:
                                            SetValue($instanceID, 100);
                                            break;
                                        case 1000000000:
                                            SetValue($instanceID, 1000);
                                            break;
                                        default:
                                            SetValue($instanceID, -1);
                                    }  
                                break;
                                case stristr($oid,'PortUtilizationRX'):
                                    $rdata = IPSWINSNMP_ReadSNMP($id, "1.3.6.1.2.1.2.2.1.10." .$port_id); //ifInOctets
                                    if(!is_array($rdata)) continue;  
                                    if(empty($lastchange) || empty($lastvalue) || !is_numeric($lastvalue)){
                                        $Device["lastvalue"] = $rdata["Value"];
                                        $Device["lastchange"] = time();

                                        IPS_SetProperty($id, "Devices", json_encode($Devices));
                                        IPS_ApplyChanges($id);
                                        continue; 
                                    } 
                                    if($rdata["Value"] < $lastvalue){
                                        $spanvalue = (4294967295 - $lastvalue) + $rdata["Value"];
                                    }else{
                                        $spanvalue = $rdata["Value"] - $lastvalue;
                                    }
                                    $spantime = time() - $lastchange;

                                    $util = (($spanvalue * 8 * 100) / ($spantime * ($speed * 1000000)));
                                    SetValue($instanceID, round($util,1));

                                    $Device["lastvalue"] = $rdata["Value"];
                                    $Device["lastchange"] = time();

                                    IPS_SetProperty($id, "Devices", json_encode($Devices));
                                    IPS_ApplyChanges($id);
                                break;
                                case stristr($oid,'PortUtilizationTX'):
                                    $rdata = IPSWINSNMP_ReadSNMP($id, "1.3.6.1.2.1.2.2.1.16." .$port_id); //ifOutOctets
                                    if(!is_array($rdata)) continue;  
                                    if(empty($lastchange) || empty($lastvalue) || !is_numeric($lastvalue)){
                                        $Device["lastvalue"] = $rdata["Value"];
                                        $Device["lastchange"] = time();

                                        IPS_SetProperty($id, "Devices", json_encode($Devices));
                                        IPS_ApplyChanges($id);
                                        continue; 
                                    } 
                                    if($rdata["Value"] < $lastvalue){
                                        $spanvalue = (4294967295 - $lastvalue) + $rdata["Value"];
                                    }else{
                                        $spanvalue = $rdata["Value"] - $lastvalue;
                                    }
                                    $spantime = time() - $lastchange;

                                    $util = (($spanvalue * 8 * 100) / ($spantime * ($speed * 1000000)));
                                    SetValue($instanceID, round($util,1));

                                    $Device["lastvalue"] = $rdata["Value"];
                                    $Device["lastchange"] = time();

                                    IPS_SetProperty($id, "Devices", json_encode($Devices));
                                    IPS_ApplyChanges($id);
                                break;
                                case stristr($oid,'PortUtilizationTRX'):
                                    $rdata1 = IPSWINSNMP_ReadSNMP($id, "1.3.6.1.2.1.2.2.1.10." .$port_id); //ifInOctets
                                    $rdata2 = IPSWINSNMP_ReadSNMP($id, "1.3.6.1.2.1.2.2.1.16." .$port_id); //ifOutOctets
                                    if(!is_array($rdata1)) continue;
                                    if(!is_array($rdata2)) continue;  
                                    if(empty($lastchange) || empty($lastvalue) || !stristr($lastvalue,'|')){
                                        $Device["lastvalue"] = $rdata1["Value"] . "|" . $rdata2["Value"];
                                        $Device["lastchange"] = time();

                                        IPS_SetProperty($id, "Devices", json_encode($Devices));
                                        IPS_ApplyChanges($id);
                                        continue; 
                                    } 
                                    $arrlastvalue = explode("|", $lastvalue);
                                    if(count($arrlastvalue) < 2) continue;

                                    if($rdata1["Value"] < $arrlastvalue[0]){
                                        $spanvalue1 = (4294967295 - $arrlastvalue[0]) + $rdata1["Value"];
                                    }else{
                                        $spanvalue1 = $rdata1["Value"] - $arrlastvalue[0];
                                    }

                                    if($rdata2["Value"] < $arrlastvalue[1]){
                                        $spanvalue2 = (4294967295 - $arrlastvalue[1]) + $rdata2["Value"];
                                    }else{
                                        $spanvalue2 = $rdata2["Value"] - $arrlastvalue[1];
                                    }

                                    $spantime = time() - $lastchange;

                                    $util = ((($spanvalue1 + $spanvalue2) * 8 * 100) / ($spantime * ($speed * 1000000)));
                                    SetValue($instanceID, round($util,1));

                                    $Device["lastvalue"] = $rdata1["Value"] . "|" . $rdata2["Value"];
                                    $Device["lastchange"] = time();

                                    IPS_SetProperty($id, "Devices", json_encode($Devices));
                                    IPS_ApplyChanges($id);
                                break;
                                case stristr($oid,'PortUtilizationFD-TRX'):
                                    $rdata1 = IPSWINSNMP_ReadSNMP($id, "1.3.6.1.2.1.2.2.1.10." .$port_id); //ifInOctets
                                    $rdata2 = IPSWINSNMP_ReadSNMP($id, "1.3.6.1.2.1.2.2.1.16." .$port_id); //ifOutOctets
                                    if(!is_array($rdata1)) continue;
                                    if(!is_array($rdata2)) continue;  
                                    if(empty($lastchange) || empty($lastvalue) || !stristr($lastvalue,'|')){
                                        $Device["lastvalue"] = $rdata1["Value"] . "|" . $rdata2["Value"];
                                        $Device["lastchange"] = time();

                                        IPS_SetProperty($id, "Devices", json_encode($Devices));
                                        IPS_ApplyChanges($id);
                                        continue; 
                                    } 
                                    $arrlastvalue = explode("|", $lastvalue);
                                    if(count($arrlastvalue) < 2) continue;

                                    if($rdata1["Value"] < $arrlastvalue[0]){
                                        $spanvalue1 = (4294967295 - $arrlastvalue[0]) + $rdata1["Value"];
                                    }else{
                                        $spanvalue1 = $rdata1["Value"] - $arrlastvalue[0];
                                    }

                                    if($rdata2["Value"] < $arrlastvalue[1]){
                                        $spanvalue2 = (4294967295 - $arrlastvalue[1]) + $rdata2["Value"];
                                    }else{
                                        $spanvalue2 = $rdata2["Value"] - $arrlastvalue[1];
                                    }

                                    $spantime = time() - $lastchange;

                                    $util = ((max($spanvalue1, $spanvalue2) * 8 * 100) / ($spantime * ($speed * 1000000)));
                                    SetValue($instanceID, round($util,1));

                                    $Device["lastvalue"] = $rdata1["Value"] . "|" . $rdata2["Value"];
                                    $Device["lastchange"] = time();

                                    IPS_SetProperty($id, "Devices", json_encode($Devices));
                                    IPS_ApplyChanges($id);
                                break;
                            default:
                                continue;
                        }
                    }else{
                        $rdata = IPSWINSNMP_ReadSNMP($id, $oid);

                        if(!is_array($rdata)) continue;
                        if(!IPS_VariableExists($instanceID)){
                            $vartyp = "";
                            $varid = 0;
                            $allow_use = false;
                            $use_action = false;
                            
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
                                            $use_action = true;
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
                            if($use_action && IPS_ScriptExists($ScriptID)) IPS_SetVariableCustomAction($varid, $ScriptID);

                            $Device["instanceID"] = $varid;
                            $Device["var"] = $vartyp;
                            $instanceID = $varid;

                            IPS_SetProperty($id, "Devices", json_encode($Devices));
                            IPS_ApplyChanges($id);
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
                }
            }    
        }

        public function GetPorts($status = false, $util = false, $utyp = ""){
            if(!$status && !$util) return "NO Mode selected!";
            if($util == true && empty($utyp)) return "Please Select Util Typ!";

            $id = $this->InstanceID;
            $DevicesString = $this->ReadPropertyString("Devices");
            $Devices = json_decode($DevicesString, true);

            $rdata = IPSWINSNMP_ReadSNMP($id, "1.3.6.1.2.1.2.1.0"); //ifNumber
            if(!is_array($rdata)) return "OID Not found!"; 
            $value = $rdata["Value"];

            for ($i=1; $i <= $value; $i++){
                //$rdata = IPSWINSNMP_ReadSNMP($id, "1.3.6.1.2.1.2.2.1.1.".$i); //ifindex
                //if(!is_array($rdata)) continue; 
                //if(!is_numeric($rdata["Value"]) || $rdata["Value"] >= 100) continue; //
                $rdata = IPSWINSNMP_ReadSNMP($id, "1.3.6.1.2.1.2.2.1.5.".$i); //ifspeed
                if(!is_array($rdata)) continue;
                if(!is_numeric($rdata["Value"]) || $rdata["Value"] == 0){
                    $speed = 1000;
                }else{
                    $speed = $rdata["Value"] / 1000000;
                }

                if($i < 10) $name = "0".$i; else $name = $i;
                
                if($status == true){
                    $key1 = array_search("PortStatus100|".$i, array_column($Devices, 'oid'));
                    $key2 = array_search("PortStatus1000|".$i, array_column($Devices, 'oid'));
                    if(empty($key1) && empty($key2)){
                        if($speed = 100) $oid = "PortStatus100|".$i; else $oid = "PortStatus1000|".$i;
                        $add = array("instanceID" => 0,"name" => "Port-".$name."|Status", "oid" => $oid, "var" => "", "typ" => "", "speed" => $speed);
                        array_push($Devices, $add);
                    }
                }

                if($util == true){
                    $key1 = array_search($utyp."|".$i, array_column($Devices, 'oid'));
                    if(empty($key1)){
                        $oid = $utyp . "|" . $i;
                        $add = array("instanceID" => 0,"name" => "Port-".$name."|Status", "oid" => $oid, "var" => "", "typ" => "", "speed" => $speed);
                        array_push($Devices, $add);
                    }
                }
            }

            IPS_SetProperty($id, "Devices", json_encode($Devices));
            IPS_ApplyChanges($id);

            return "Load Complete! Please restart the Instance-Menu.";
        }
    }
?>