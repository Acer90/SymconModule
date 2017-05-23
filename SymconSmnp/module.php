<?
    // Klassendefinition
    class IPSWINSNMP extends IPSModule {
        public function Create() {
            //parent::Create();

            // Modul-Eigenschaftserstellung
            $this->RegisterPropertyString("SNMPIPAddress", "192.168.178.1"); 
            $this->RegisterPropertyInteger("SNMPPort", 161);
            $this->RegisterPropertyInteger("SNMPTimeout", 1);
            $this->RegisterPropertyInteger("SNMPVersion", 2);
            $this->RegisterPropertyString("SNMPCommunity", "public"); 
         
        }

        public function MeineErsteEigeneFunktion() {
            // Selbsterstellter Code
        }
    }
?>