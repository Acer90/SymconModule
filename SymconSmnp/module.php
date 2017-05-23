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
            $this->RegisterPropertyInteger("SNMPVersion", 2);

            $this->RegisterPropertyString("SNMPCommunity", "public"); 

            $this->RegisterPropertyString("SNMPSecurityName", "");
            $this->RegisterPropertyString("SNMPAuthenticationProtocol", "SHA"); 
            $this->RegisterPropertyString("SNMPAuthenticationPassword", ""); 
            $this->RegisterPropertyString("SNMPPrivacyProtocol", "AES256"); 
            $this->RegisterPropertyString("SNMPPrivacyPassword", ""); 
            $this->RegisterPropertyString("SNMPEngineID", ""); 
            $this->RegisterPropertyString("SNMPContextName", ""); 
            $this->RegisterPropertyString("SNMPContextEngine", ""); 
        }

        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();
            $this->RequireParent("{1A75660D-48AE-4B89-B351-957CAEBEF22D}");
        }

        public function MeineErsteEigeneFunktion() {
            // Selbsterstellter Code
        }
    }
?>