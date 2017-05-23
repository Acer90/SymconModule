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

        public function ReadSNMP($oid = ".1.3.6.1.2.1.1.4.0") {
            $Filedir = dirname(__FILE__). "\\bin\\". "SnmpGet.exe";

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

                $Parameters = '-r:' . $SNMPIPAddress.' -p:'.$SNMPPort.' -t:'.$SNMPTimeout.' -c:"'.$SNMPCommunity.'"' .' -o:' . $oid;
                $out = IPS_Execute($Filedir , $Parameters, FALSE, TRUE);
            }
            echo $out;
        }
    }
?>