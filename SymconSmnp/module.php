<?php
    require_once('lib/snmp.php');
    require(__DIR__ . '/vendor/autoload.php');
    use FreeDSx\Snmp\SnmpClient;
    use FreeDSx\Snmp\Exception\SnmpRequestException;
    use FreeDSx\Snmp\Oid;

class IPSSNMP extends IPSModule {
        public function __construct(int $InstanceID) {
            parent::__construct($InstanceID);
        }

        public function Create() {
            parent::Create();

            // Modul-Eigenschaftserstellung
            $this->RegisterPropertyBoolean("Debug", false);

            $this->RegisterPropertyString("SNMPIPAddress", "192.168.178.1");
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
            $this->RegisterPropertyInteger("SNMPContextEngine", "0");

            $this->RegisterPropertyInteger("SNMPSpeedModify", "1");
            $this->RegisterPropertyString("Devices", ""); 

            //event erstellen
            $this->RegisterTimer("SyncData", $this->ReadPropertyInteger("SNMPInterval"), 'IPSSNMP_SyncData($_IPS[\'TARGET\']);');

            //Profile erstellen
            if (!IPS_VariableProfileExists("SNMP_Watt")){
                IPS_CreateVariableProfile("SNMP_Watt", 2);
                IPS_SetVariableProfileDigits("SNMP_Watt", 1);
                IPS_SetVariableProfileText("SNMP_Watt", "", "W");
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

            if (!IPS_VariableProfileExists("SNMP_PortMbit")){
                IPS_CreateVariableProfile("SNMP_PortMbit", 2);
                IPS_SetVariableProfileDigits("SNMP_PortMbit", 1);
                IPS_SetVariableProfileText("SNMP_PortMbit", "", "Mbit/s");
            }
        }

        public function ApplyChanges() {
            //Diese Zeile nicht löschen
            parent::ApplyChanges();
            //$this->RequireParent("{1A75660D-48AE-4B89-B351-957CAEBEF22D}");
            $this->SetStatus(102);
            $this->SetTimerInterval("SyncData", $this->ReadPropertyInteger("SNMPInterval")*1000);
        }

        /*public function Test(){
            echo "test";
            $oid = '.1.3.6.1.2.1.69.1.1.3';
            $oid = '.1.3.6.1.6.3.15.1.1.4.0';

            // test the oid_format function
            $z = oid_format($oid, OID_TEXT);
            $zz = oid_format($z, OID_NUMERIC);
            echo "$oid => $z => $zz\n";

            $ip = '10.1.2.2'; 		// ip address or hostname
            $community = 'PCBEUser';		// community string
            $oid = '.1.3.6.1.4.1.318.1.1.1.2.2.1';		// only numerical oid are supported

            $snmp = new ipssnmpclass();

            $snmp->version = SNMP_VERSION_1;

            print_r($snmp->walk($ip, $oid, ['community' => $community]));



            // get system ut
            print_r($snmp->get($ip, '.1.3.6.1.4.1.318.1.1.1.4.2.3.0', ['community' => $community]));
        }*/

        public function ReadSNMP($oid_array) {

            if($this->ReadPropertyBoolean("Debug"))$this->SendDebug("ReadSNMP", json_encode($oid_array), 0);

            $SNMPIPAddress = $this->ReadPropertyString("SNMPIPAddress");
            $SNMPTimeout = $this->ReadPropertyInteger("SNMPTimeout");
            $SNMPVersion = $this->ReadPropertyString("SNMPVersion");
            $SNMPCommunity = $this->ReadPropertyString("SNMPCommunity");
            $SNMPSecurityName = $this->ReadPropertyString("SNMPSecurityName");
            $SNMPAuthenticationProtocol = $this->ReadPropertyString("SNMPAuthenticationProtocol");
            $SNMPAuthenticationPassword = $this->ReadPropertyString("SNMPAuthenticationPassword");
            $SNMPPrivacyProtocol = $this->ReadPropertyString("SNMPPrivacyProtocol");
            $SNMPPrivacyPassword = $this->ReadPropertyString("SNMPPrivacyPassword");
            $SNMPEngineID = $this->ReadPropertyInteger("SNMPEngineID");
            $SNMPContextName = $this->ReadPropertyString("SNMPContextName");
            $SNMPContextEngine = $this->ReadPropertyInteger("SNMPContextEngine");

            $snmp = new SnmpClient([
                'host' =>  $SNMPIPAddress,
                'version' => (int)$SNMPVersion,
                'community' => $SNMPCommunity,
            ]);

            if($this->ReadPropertyBoolean("Debug"))$this->SendDebug("ReadSNMP","Use SNMPVersion => ".$SNMPVersion, 0);
            $out = array();
            if(is_array($oid_array)){
                $oids = @call_user_func_array (array($snmp, "get"), $oid_array);

                foreach($oids as $oid) {
                    //$this->SendDebug("ReadSNMP", "READ OID => " . $oid->getOid(), 0);
                    $out[$oid->getOid()] = (string)$oid->getValue();
                }

                if($this->ReadPropertyBoolean("Debug"))$this->SendDebug("ReadSNMP", json_encode($out), 0);
            }
            else{
                //$out = $snmp->get($SNMPIPAddress, $oid_array, $snmp_sdata);
                $out[$oid_array] = @$snmp->getValue($oid_array).PHP_EOL;;
                if($this->ReadPropertyBoolean("Debug"))$this->SendDebug("ReadSNMP",$oid_array. " => ".json_encode($out), 0);
            }

            return $out;
        }

        public function WalkSNMP(string $oid) {

            if($this->ReadPropertyBoolean("Debug"))$this->SendDebug("WalkSNMP", $oid, 0);

            $SNMPIPAddress = $this->ReadPropertyString("SNMPIPAddress");
            $SNMPTimeout = $this->ReadPropertyInteger("SNMPTimeout");
            $SNMPVersion = $this->ReadPropertyString("SNMPVersion");
            $SNMPCommunity = $this->ReadPropertyString("SNMPCommunity");
            $SNMPSecurityName = $this->ReadPropertyString("SNMPSecurityName");
            $SNMPAuthenticationProtocol = $this->ReadPropertyString("SNMPAuthenticationProtocol");
            $SNMPAuthenticationPassword = $this->ReadPropertyString("SNMPAuthenticationPassword");
            $SNMPPrivacyProtocol = $this->ReadPropertyString("SNMPPrivacyProtocol");
            $SNMPPrivacyPassword = $this->ReadPropertyString("SNMPPrivacyPassword");
            $SNMPEngineID = $this->ReadPropertyInteger("SNMPEngineID");
            $SNMPContextName = $this->ReadPropertyString("SNMPContextName");
            $SNMPContextEngine = $this->ReadPropertyInteger("SNMPContextEngine");

            $snmp = new ipssnmpclass();
            $snmp_sdata = array();
            $snmp->timeout = $SNMPTimeout;

            if($this->ReadPropertyBoolean("Debug"))$this->SendDebug("WalkSNMP","Use SNMPVersion => ".$SNMPVersion, 0);

            switch($SNMPVersion){
                case "1";
                    $snmp->version = SNMP_VERSION_1;
                    $snmp_sdata = ['community' => $SNMPCommunity];
                    break;
                case "2";
                    $snmp->version = SNMP_VERSION_2;
                    $snmp_sdata = ['community' => $SNMPCommunity];
                    break;
                case "2c";
                    $snmp->version = SNMP_VERSION_2C;
                    $snmp_sdata = ['community' => $SNMPCommunity];
                    break;
                case "2u";
                    $snmp->version = SNMP_VERSION_2U;
                    $snmp_sdata = ['community' => $SNMPCommunity];
                    break;
                case "3";
                    $snmp->version = SNMP_VERSION_3;
                    $snmp_sdata = ['v3_flags'=> SNMP_AUTH_PRIV, 'v3_user'=>$SNMPSecurityName,'v3_auth'=>$SNMPAuthenticationPassword, 'v3_priv'=>$SNMPPrivacyPassword, 'v3_hash'=>$SNMPAuthenticationProtocol, 'v3_crypt_algorithm'=>$SNMPPrivacyProtocol, 'v3_engine_id'=>$SNMPEngineID, 'v3_context_engine_id'=>$SNMPContextEngine, 'v3_context_name'=>$SNMPContextName];
                    break;
            }

            $out = $snmp->walk($SNMPIPAddress, $oid, $snmp_sdata);
            if($this->ReadPropertyBoolean("Debug"))$this->SendDebug("WalkSNMP",$oid. " => ".json_encode($out), 0);
            return $out;
        }

        public function WriteSNMPbyOID(string $oid, string $value, string $type) {
            if($this->ReadPropertyBoolean("Debug"))$this->SendDebug("WriteSNMPbyOID", $oid ." => ". $value, 0);

            $SNMPIPAddress = $this->ReadPropertyString("SNMPIPAddress");
            $SNMPTimeout = $this->ReadPropertyInteger("SNMPTimeout");
            $SNMPVersion = $this->ReadPropertyString("SNMPVersion");
            $SNMPCommunity = $this->ReadPropertyString("SNMPCommunity");
            $SNMPSecurityName = $this->ReadPropertyString("SNMPSecurityName");
            $SNMPAuthenticationProtocol = $this->ReadPropertyString("SNMPAuthenticationProtocol");
            $SNMPAuthenticationPassword = $this->ReadPropertyString("SNMPAuthenticationPassword");
            $SNMPPrivacyProtocol = $this->ReadPropertyString("SNMPPrivacyProtocol");
            $SNMPPrivacyPassword = $this->ReadPropertyString("SNMPPrivacyPassword");
            $SNMPEngineID = $this->ReadPropertyInteger("SNMPEngineID");
            $SNMPContextName = $this->ReadPropertyString("SNMPContextName");
            $SNMPContextEngine = $this->ReadPropertyInteger("SNMPContextEngine");

            $snmp = new SnmpClient([
                'host' =>  $SNMPIPAddress,
                'version' => (int)$SNMPVersion,
                'community' => $SNMPCommunity,
            ]);

            try {
                switch($type){
                    case "BigCounter": $snmp->set(Oid::fromBigCounter($oid, $value));
                    case "Counter": $snmp->set(Oid::fromCounter($oid, $value));
                    case "Integer": $snmp->set(Oid::fromInteger($oid, (int)$value));
                    case "IpAddress": $snmp->set(Oid::fromIpAddress($oid, $value));
                    case "Oid": $snmp->set(Oid::fromOid($oid, $value));
                    case "String": $snmp->set(Oid::fromString($oid, $value));
                    case "Timeticks": $snmp->set(Oid::fromTimeticks($oid, $value));
                    case "UnsignedInt": $snmp->set(Oid::fromUnsignedInt($oid, $value));
                    case "Asn1": $snmp->set(Oid::fromAsn1($oid));
                }
            } catch (SnmpRequestException $e) {
                //echo $e->getMessage();
            }

            /*
            $oid = $snmp->get($value);
            $oid->get($value)->setValue($value).PHP_EOL;*/

            if($this->ReadPropertyBoolean("Debug"))$this->SendDebug("WriteSNMPbyOID","Use SNMPVersion => ".$SNMPVersion, 0);
            return true;
        }

        public function WriteSNMPbyVarID(int $varid, string $value, string $type)
        {
            if(!is_numeric($varid)){
                return false;
            }
            $obj = IPS_GetObject($varid);
            if($obj == false || !array_key_exists("ObjectIdent",$obj)){
                return false;
            }

            $DevicesString = $this->ReadPropertyString("Devices");
            $Devices = json_decode($DevicesString, true);
            $oid = "";
            $converter = "";

            foreach($Devices as $Device){
                if (empty($Device["oid"])) continue;

                $ident = preg_replace ( '/[^a-z0-9]/i', '', $Device["oid"]);

                if($ident == $obj["ObjectIdent"]) {
                    $oid = $Device["oid"];
                    $converter = $Device["typ"];
                    break;
                }
            }

            if(empty($oid)){
                return false;
            }


            //portstatus
            switch($oid){
                case stristr($oid,'PortStatus100') || stristr($oid,'PortStatus1000'):
                    if($value == -1) {
                        $strarr = explode("|", $oid);
                        if (count($strarr) < 2) return false;
                        $port_id = intval($strarr[1]);
                        if (!is_numeric($port_id)) return false;
                        $value = 2;
                        $oid = "1.3.6.1.2.1.2.2.1.7.".$port_id;
                    }else{
                        $strarr = explode("|", $oid);
                        if (count($strarr) < 2) return false;
                        $port_id = intval($strarr[1]);
                        if (!is_numeric($port_id)) return false;
                        $value = 1;
                        $oid = "1.3.6.1.2.1.2.2.1.7.".$port_id;
                    }
                    break;
            }

            //converter
            if(!empty($converter)){
                switch ($converter){
                    case "switch":
                        if($value) $value = 1; else $value = 0;
                        break;
                    case "switch12":
                        if($value) $value = 1; else $value = 2;
                        break;
                    case "mWtoW":
                        $value = (Int)($value * 1000);
                        break;
                }
            }

            $this->WriteSNMPbyOID($oid, $value, $type);
            return true;
        }

        public function SyncData()
        {
            $time_start = microtime(true);
            $id = $this->InstanceID;
            $this->SetStatus(102);
            $DevicesString = $this->ReadPropertyString("Devices");
            $Devices = json_decode($DevicesString, true);

            //Read Data from SNMP Server

            $oids = [];
            $i = 0;
            $output = [];

            foreach ($Devices as $Device) {
                $oid = $Device["oid"];


                if ($i > 50) {
                    $output = $output + $this->ReadSNMP($oids);
                    $oids = [];
                    $i = 0;
                }


                if (stristr($oid, '|')) {

                    $strarr = explode("|", $oid);
                    if (count($strarr) < 2) continue;
                    $port_id = intval($strarr[1]);
                    if (!is_numeric($port_id)) continue;

                    switch ($oid) {
                        case stristr($oid, 'PortStatus100') || stristr($oid, 'PortStatus1000'):
                            $add_oid = "1.3.6.1.2.1.2.2.1.7." . $port_id;
                            if (!in_array($add_oid, $oids)) {
                                $oids[] = $add_oid;
                                $i++;
                            }
                            $add_oid = "1.3.6.1.2.1.2.2.1.8." . $port_id;
                            if (!in_array($add_oid, $oids)) {
                                $oids[] = $add_oid;
                                $i++;
                            }
                            $add_oid = "1.3.6.1.2.1.2.2.1.5." . $port_id;
                            if (!in_array($add_oid, $oids)) {
                                $oids[] = $add_oid;
                                $i++;
                            }
                            break;
                        case stristr($oid, 'PortUtilizationRX') || stristr($oid, 'PortMbitRX'):
                            $add_oid = "1.3.6.1.2.1.2.2.1.10." . $port_id;
                            if (!in_array($add_oid, $oids)) {
                                $oids[] = $add_oid;
                                $i++;
                            }
                            break;
                        case stristr($oid, 'PortUtilizationTX') || stristr($oid, 'PortMbitTX'):
                            $add_oid = "1.3.6.1.2.1.2.2.1.16." . $port_id;
                            if (!in_array($add_oid, $oids)) {
                                $oids[] = $add_oid;
                                $i++;
                            }
                            break;
                        case stristr($oid, 'PortUtilizationTRX') || stristr($oid, 'PortUtilizationFD-TRX'):
                            $add_oid = "1.3.6.1.2.1.2.2.1.10." . $port_id;
                            if (!in_array($add_oid, $oids)) {
                                $oids[] = $add_oid;
                                $i++;
                            }
                            $add_oid = "1.3.6.1.2.1.2.2.1.16." . $port_id;
                            if (!in_array($add_oid, $oids)) {
                                $oids[] = $add_oid;
                                $i++;
                            }
                            break;
                        default:
                            continue 2;
                    }
                } else {
                    if (!in_array($oid, $oids)) {
                        $oids[] = $oid;
                        $i++;
                    }
                }
            }
            $output = $output + $this->ReadSNMP($oids);
            //print_r($output);



            foreach ($Devices as &$Device) {
                $oid = $Device["oid"];
                if (empty($oid)) continue;

                $ident = preg_replace ( '/[^a-z0-9]/i', '', $oid );
                $instanceID = @IPS_GetObjectIDByIdent($ident, $id);
                $typ = $Device["typ"];

                if (IPS_VariableExists($instanceID) && !empty($this->GetBuffer($instanceID . "-lastvalue"))) $lastvalue = $this->GetBuffer($instanceID . "-lastvalue"); else $lastvalue = 0;
                if (IPS_VariableExists($instanceID) && !empty($this->GetBuffer($instanceID . "-lastchange"))) $lastchange = $this->GetBuffer($instanceID . "-lastchange"); else $lastchange = 0;
                if (isset($Device["speed"])) $speed = $Device["speed"]; else $speed = 100;

                if (stristr($oid, '|')) {

                    $strarr = explode("|", $oid);
                    if (count($strarr) < 2) continue;
                    $port_id = intval($strarr[1]);
                    if (!is_numeric($port_id)) continue;

                    if ($instanceID === false) {
                        switch ($oid) {
                            case stristr($oid, 'PortStatus100') || stristr($oid, 'PortStatus1000'):
                                $this->RegisterVariableInteger($ident, $oid, "SNMP_PortStatus_1000");
                                $instanceID = IPS_GetObjectIDByIdent($ident, $id);
                                break;
                            case stristr($oid, 'PortUtilizationRX') || stristr($oid, 'PortUtilizationTX') || stristr($oid, 'PortUtilizationTRX') || stristr($oid, 'PortUtilizationFD-TRX'):
                                $this->RegisterVariableFloat($ident, $oid, "SNMP_PortUtilization");
                                $instanceID = IPS_GetObjectIDByIdent($ident, $id);
                                break;
                            case stristr($oid, 'PortMbitRX') || stristr($oid, 'PortMbitTX'):
                                $this->RegisterVariableFloat($ident, $oid, "SNMP_PortMbit");
                                $instanceID = IPS_GetObjectIDByIdent($ident, $id);
                                break;
                            default:
                                continue 2;
                        }
                    }

                    switch($oid){
                        case stristr($oid,'PortStatus100') || stristr($oid,'PortStatus1000'):
                            $search_oid = "1.3.6.1.2.1.2.2.1.7." . $port_id;
                            if(!array_key_exists($search_oid, $output)) continue 2;
                            if($output[$search_oid] == 2){
                                if($this->GetValue($ident) != -1)$this->SetValue($ident, -1);
                                $this->SendDebug("SetValue",  $oid." (".$instanceID.") => -1", 0);
                                continue 2;
                            }
                            $search_oid = "1.3.6.1.2.1.2.2.1.8." . $port_id;
                            if(!array_key_exists($search_oid, $output)) continue 2;
                            if($output[$search_oid] == 2){
                                if($this->GetValue($ident) != 0)$this->SetValue($ident, 0);
                                $this->SendDebug("SetValue",  $oid." (".$instanceID.") => 0", 0);
                                continue 2;
                            }
                            $search_oid = "1.3.6.1.2.1.2.2.1.5." . $port_id;
                            if(!array_key_exists($search_oid, $output)) continue 2;
                            $value = $output[$search_oid] * $this->ReadPropertyInteger("SNMPSpeedModify");

                            switch($value){
                                case 10000000:
                                    $svalue = 10;
                                    break;
                                case 100000000:
                                    $svalue = 100;
                                    break;
                                case 1000000000:
                                    $svalue = 1000;
                                    break;
                                default:
                                    $svalue = -1;
                            }
                            if($this->GetValue($ident) != $svalue)$this->SetValue($ident, $svalue);

                            $this->SendDebug("SetValue",  $oid." (".$instanceID.") =>".$svalue, 0);
                            break;
                        case stristr($oid,'PortUtilizationRX'):
                            $search_oid = "1.3.6.1.2.1.2.2.1.10." . $port_id;
                            if(!array_key_exists($search_oid, $output)) continue 2;

                            if(empty($lastchange) || empty($lastvalue) || !is_numeric($lastvalue)){
                                $this->SetBuffer($instanceID."-lastvalue", $output[$search_oid]);
                                $this->SetBuffer($instanceID."-lastchange", time());
                                continue 2;
                            }

                            if($output[$search_oid] < $lastvalue){
                                $spanvalue = (4294967295 - $lastvalue) + $output[$search_oid];
                            }else{
                                $spanvalue = $output[$search_oid] - $lastvalue;
                            }
                            $spantime = time() - $lastchange;

                            $util = (($spanvalue * 8 * 100) / ($spantime * ($speed * 1000000)));
                            if($this->GetValue($ident) != round($util,1))$this->SetValue($ident, round($util,1));

                            $this->SendDebug("SetValue",  $oid." (".$instanceID.") => ".round($util,1)."%"  , 0);

                            $this->SetBuffer($instanceID."-lastvalue", $output[$search_oid]);
                            $this->SetBuffer($instanceID."-lastchange", time());
                            break;
                        case stristr($oid,'PortUtilizationTX'):
                            $search_oid = "1.3.6.1.2.1.2.2.1.16." . $port_id;
                            if(!array_key_exists($search_oid, $output)) continue 2;

                            if(empty($lastchange) || empty($lastvalue) || !is_numeric($lastvalue)){
                                $this->SetBuffer($instanceID."-lastvalue", $output[$search_oid]);
                                $this->SetBuffer($instanceID."-lastchange", time());
                                continue 2;
                            }
                            if($output[$search_oid] < $lastvalue){
                                $spanvalue = (4294967295 - $lastvalue) + $output[$search_oid];
                            }else{
                                $spanvalue = $output[$search_oid] - $lastvalue;
                            }
                            $spantime = time() - $lastchange;

                            $util = (($spanvalue * 8 * 100) / ($spantime * ($speed * 1000000)));
                            if($this->GetValue($ident) != round($util,1))$this->SetValue($ident, round($util,1));

                            $this->SendDebug("SetValue",  $oid." (".$instanceID.") => ".round($util,1)."%"  , 0);

                            $this->SetBuffer($instanceID."-lastvalue", $output[$search_oid]);
                            $this->SetBuffer($instanceID."-lastchange", time());
                            break;
                        case stristr($oid,'PortMbitRX'):
                            $search_oid = "1.3.6.1.2.1.2.2.1.10." . $port_id;
                            if(!array_key_exists($search_oid, $output)) continue 2;

                            if(empty($lastchange) || empty($lastvalue) || !is_numeric($lastvalue)){
                                $this->SetBuffer($instanceID."-lastvalue", $output[$search_oid]);
                                $this->SetBuffer($instanceID."-lastchange", time());
                                continue 2;
                            }
                            if($output[$search_oid] < $lastvalue){
                                $spanvalue = (4294967295 - $lastvalue) + $output[$search_oid];
                            }else{
                                $spanvalue = $output[$search_oid] - $lastvalue;
                            }
                            $spantime = time() - $lastchange;

                            $util = (($spanvalue * 8 * 100) / ($spantime * ($speed * 1000000)));
                            $mbit = ($util / 100) * $speed;
                            if($this->GetValue($ident) != round($mbit,1))$this->SetValue($ident, round(mbit,1));

                            $this->SendDebug("SetValue",  $oid." (".$instanceID.") => ".round($mbit,1)."Mbit" , 0);

                            $this->SetBuffer($instanceID."-lastvalue", $output[$search_oid]);
                            $this->SetBuffer($instanceID."-lastchange", time());
                            break;
                        case stristr($oid,'PortMbitTX'):
                            $search_oid = "1.3.6.1.2.1.2.2.1.16." . $port_id;
                            if(!array_key_exists($search_oid, $output)) continue 2;

                            if(empty($lastchange) || empty($lastvalue) || !is_numeric($lastvalue)){
                                $this->SetBuffer($instanceID."-lastvalue", $output[$search_oid]);
                                $this->SetBuffer($instanceID."-lastchange", time());
                                continue 2;
                            }
                            if($output[$search_oid] < $lastvalue){
                                $spanvalue = (4294967295 - $lastvalue) + $output[$search_oid];
                            }else{
                                $spanvalue = $output[$search_oid] - $lastvalue;
                            }
                            $spantime = time() - $lastchange;

                            $util = (($spanvalue * 8 * 100) / ($spantime * ($speed * 1000000)));
                            $mbit = ($util / 100) * $speed;
                            if($this->GetValue($ident) != round($mbit,1))$this->SetValue($ident, round(mbit,1));

                            $this->SendDebug("SetValue",  $oid." (".$instanceID.") => ".round($mbit,1)."Mbit" , 0);

                            $this->SetBuffer($instanceID."-lastvalue", $output[$search_oid]);
                            $this->SetBuffer($instanceID."-lastchange", time());
                            break;
                        case stristr($oid,'PortUtilizationTRX'):
                            $search_oid1 = "1.3.6.1.2.1.2.2.1.10." . $port_id;
                            $search_oid2 = "1.3.6.1.2.1.2.2.1.16." . $port_id;
                            if(!array_key_exists($search_oid1, $output)) continue 2;
                            if(!array_key_exists($search_oid2, $output)) continue 2;

                            if(empty($lastchange) || empty($lastvalue) || !stristr($lastvalue,'|')){
                                $this->SetBuffer($instanceID."-lastvalue", $output[$search_oid1] . "|" . $output[$search_oid2]);
                                $this->SetBuffer($instanceID."-lastchange", time());
                                continue 2;
                            }
                            $arrlastvalue = explode("|", $lastvalue);
                            if(count($arrlastvalue) < 2) continue 2;

                            if($output[$search_oid1] < $arrlastvalue[0]){
                                $spanvalue1 = (4294967295 - $arrlastvalue[0]) + $output[$search_oid1];
                            }else{
                                $spanvalue1 = $output[$search_oid1] - $arrlastvalue[0];
                            }

                            if($output[$search_oid2] < $arrlastvalue[1]){
                                $spanvalue2 = (4294967295 - $arrlastvalue[1]) + $output[$search_oid2];
                            }else{
                                $spanvalue2 = $output[$search_oid2] - $arrlastvalue[1];
                            }

                            $spantime = time() - $lastchange;

                            $util = ((($spanvalue1 + $spanvalue2) * 8 * 100) / ($spantime * ($speed * 1000000)));
                            if($this->GetValue($ident) != round($util,1))$this->SetValue($ident, round($util,1));

                            $this->SendDebug("SetValue",  $oid." (".$instanceID.") => ".round($util,1)."%" , 0);

                            $this->SetBuffer($instanceID."-lastvalue",  $output[$search_oid1] . "|" . $output[$search_oid2]);
                            $this->SetBuffer($instanceID."-lastchange", time());
                            break;
                        case stristr($oid,'PortUtilizationFD-TRX'):
                            $search_oid1 = "1.3.6.1.2.1.2.2.1.10." . $port_id;
                            $search_oid2 = "1.3.6.1.2.1.2.2.1.16." . $port_id;
                            if(!array_key_exists($search_oid1, $output)) continue 2;
                            if(!array_key_exists($search_oid2, $output)) continue 2;

                            if(empty($lastchange) || empty($lastvalue) || !stristr($lastvalue,'|')){
                                $this->SetBuffer($instanceID."-lastvalue", $output[$search_oid1] . "|" . $output[$search_oid2]);
                                $this->SetBuffer($instanceID."-lastchange", time());
                                continue 2;
                            }
                            $arrlastvalue = explode("|", $lastvalue);
                            if(count($arrlastvalue) < 2) continue 2;

                            if($output[$search_oid1] < $arrlastvalue[0]){
                                $spanvalue1 = (4294967295 - $arrlastvalue[0]) + $output[$search_oid1];
                            }else{
                                $spanvalue1 = $output[$search_oid1] - $arrlastvalue[0];
                            }

                            if($output[$search_oid2] < $arrlastvalue[1]){
                                $spanvalue2 = (4294967295 - $arrlastvalue[1]) + $output[$search_oid2];
                            }else{
                                $spanvalue2 = $output[$search_oid2] - $arrlastvalue[1];
                            }

                            $spantime = time() - $lastchange;

                            $util = ((max($spanvalue1, $spanvalue2) * 8 * 100) / ($spantime * ($speed * 1000000)));
                            if($this->GetValue($ident) != round($util,1))$this->SetValue($ident, round($util,1));

                            $this->SendDebug("SetValue",  $oid." (".$instanceID.") => ".round($util,1)."%" , 0);

                            $this->SetBuffer($instanceID."-lastvalue",  $output[$search_oid1] . "|" . $output[$search_oid2]);
                            $this->SetBuffer($instanceID."-lastchange", time());
                            break;
                        default:
                            continue 2;
                    }
                } else {
                    //if(substr( $oid, 0, 1 ) != ".") $oid = $oid;

                    if($this->ReadPropertyBoolean("Debug"))$this->SendDebug("SyncData","OID => ".$oid ." TYP OID", 0);
                    if($this->ReadPropertyBoolean("Debug"))$this->SendDebug("SyncData",json_encode($output), 0);

                    if(!array_key_exists($oid, $output)) continue;
                    $value = $output[$oid];

                    if($this->ReadPropertyBoolean("Debug"))$this->SendDebug("SyncData","VALUE => ".$value." (".$instanceID.")", 0);
                    if($instanceID === false){

                        if($this->ReadPropertyBoolean("Debug"))$this->SendDebug("SyncData","DATATyp => ".gettype($value), 0);
                        switch (gettype($value)){
                            case "boolean":
                                //Boolean anlegen
                                $this->RegisterVariableBoolean($ident, $oid, "SNMP_PortStatus_1000");
                                break;
                            case "integer":
                                //Integer anlegen
                                switch($typ){
                                    case "mWtoW":
                                        $this->RegisterVariableFloat($ident, $oid, "SNMP_Watt");
                                        break;
                                    case "toTimeSpan":
                                        $this->RegisterVariableString($ident, $oid);
                                        break;
                                    case "switch"  || "switch12":
                                        $this->RegisterVariableBoolean($ident, $oid, "~Switch");
                                        break;
                                    default:
                                        $this->RegisterVariableInteger($ident, $oid);
                                        break;
                                }
                                break;
                            case "double":
                                //Float anlegen
                                switch($typ){
                                    default:
                                        $this->RegisterVariableFloat($ident, $oid);
                                        break;
                                }
                                break;
                            default:
                                //String anlegen
                                $this->RegisterVariableString($ident, $oid);
                                break;
                        }

                        $instanceID = IPS_GetObjectIDByIdent($ident, $id);
                    }

                    switch($typ){
                        case "mWtoW":
                            $value = $value / 1000;
                            if(is_numeric($value)) {
                                if($this->GetValue($ident) != $value)$this->SetValue($ident, $value);

                                if($value == 0) IPS_SetHidden($instanceID, true); else IPS_SetHidden($instanceID, false);
                            }
                            break;
                        case "toTimeSpan":
                            if(is_numeric($value)) {
                                $secs = $value / 100;
                                $days = floor($value/8640000);
                                $hours = date("H:i:s",$secs+strtotime("1970/1/1"));

                                if($days > 0)
                                    if($this->GetValue($ident) != $days. " Tage ". $hours) $this->SetValue($ident, $days. " Tage ". $hours);
                                else
                                    if($this->GetValue($ident) != $hours) $this->SetValue($ident, $hours);
                            }
                            break;
                        case "switch" || "switch12":
                            if(is_numeric($value) && $value == 1) {
                                if ($this->GetValue($ident) != true) $this->SetValue($ident, true);
                            }else{
                                if ($this->GetValue($ident) != false) $this->SetValue($ident, false);
                            }
                            break;
                        default:
                            if($this->GetValue($ident) != $value) @$this->SetValue($ident, $value);
                            break;
                    }

                }
            }
            return  'Total running time in seconds: ' . round((microtime(true) - $time_start)*1000)."ms for ".count($output)." queries";
        }

        public function GetPorts(bool $status = false, bool $util = false, string $utyp = ""){
            if(!$status && !$util) return "NO Mode selected!";
            if($util == true && empty($utyp)) return "Please Select Util Typ!";

            $id = $this->InstanceID;
            $DevicesString = $this->ReadPropertyString("Devices");
            $Devices = json_decode($DevicesString, true);

            $rdata = $this->WalkSNMP("1.3.6.1.2.1.2.2.1.5"); //ifspeed
            if(!is_array($rdata)) return "OID Not found!";

            foreach($rdata as $key => $val){
                $exp_key = explode(".",$key);
                if($exp_key[(count($exp_key)-1)] >= 100){
                    break;
                }

                if(!is_array($rdata)) continue;
                if(!is_numeric($val) || $val == 0){
                    $speed = 1000;
                }else{
                    $speed = $val / 1000000 * $this->ReadPropertyInteger("SNMPSpeedModify");
                }

                if($exp_key[(count($exp_key)-1)] < 10) $name = "0".$exp_key[(count($exp_key)-1)]; else $name = $exp_key[(count($exp_key)-1)];

                if($status == true){
                    $key1 = "";$key2 = "";
                    if(count($Devices)>0){
                        $key1 = array_search("PortStatus100|".$name, array_column($Devices, 'oid'));
                        $key2 = array_search("PortStatus1000|".$name, array_column($Devices, 'oid'));
                    }

                    if(empty($key1) && empty($key2)){
                        if($speed = 100) $oid = "PortStatus100|".$name; else $oid = "PortStatus1000|".$name;
                        $add = array("oid" => $oid, "typ" => "", "speed" => $speed);
                        if(count($Devices)>0){
                            array_push($Devices, $add);
                        }else{
                            $Devices[] = $add;
                        }
                    }
                }

                if($util == true){
                    $key1 = "";
                    if(count($Devices)>0) {
                        $key1 = array_search(($utyp . "|" . $name), array_column($Devices, 'oid'));
                    }

                    if(empty($key1)){
                        $oid = $utyp . "|" . $name;
                        $add = array( "oid" => $oid,  "typ" => "", "speed" => $speed);
                        if(count($Devices)>0){
                            array_push($Devices, $add);
                        }else{
                            $Devices[] = $add;
                        }

                    }
                }
            }

            IPS_SetProperty($id, "Devices", json_encode($Devices));
            IPS_ApplyChanges($id);
            return "Load Complete! Please restart the Instance-Menu.";
        }
    }
