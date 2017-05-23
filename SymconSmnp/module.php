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
        }

        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();
        }

        public function MeineErsteEigeneFunktion() {
            // Selbsterstellter Code
        }
    }
?>