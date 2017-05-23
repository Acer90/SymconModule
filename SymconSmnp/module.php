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

            $this->RegisterPropertyString("Devices", ""); 
            
        }

        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();
            //$this->RequireParent("{1A75660D-48AE-4B89-B351-957CAEBEF22D}");
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
                    return '';
                    $this->SetStatus(201);
                    break;
                case stristr($out,'%Failed to get value of SNMP variable. Timeout.'):
                    return '';
                    $this->SetStatus(102);
                    break;
                case stristr($out,'Variable does not exist'):
                    return '';
                    $this->SetStatus(202);
                    break;
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
        public function WriteSNMP($oid) {

        }

        public function SyncData(){
            $DevicesString = $this->ReadPropertyString("Devices");
            $Devices = json_decode($DevicesString, true);
            foreach ($Devices as &$Device) {
                $instanceID = $Device["instanceID"];
                $name = $Device["name"];
                $oid = $Device["oid"];
                $typ = $Device["typ"];

                if(!empty($name) && !empty($oid)){
                    $rdata = IPSWINSNMP_ReadSNMP($oid);
                    if(is_array($rdata)){
                        if(!IPS_VariableExists($instanceID)){
                            echo $typ;

                            switch (true){
                                case stristr($typ,'%Invalid parameter'):
                                    //Boolean anlegen
                                    $var = IPS_CreateVariable(0);
                                    break;
                                case stristr($typ,'Integer') || stristr($typ,'Gauge') || stristr($typ,'Counter'):
                                    //Integer anlegen
                                    $var = IPS_CreateVariable(1);
                                    break;
                                case stristr($typ,'Variable does not exist'):
                                    //Float
                                    $var = IPS_CreateVariable(2);
                                    break;
                                default:
                                    $var = IPS_CreateVariable(3);
                                    break;
                            }

                        }else{

                        }


                    }
                }
            }
        }
    }
?>