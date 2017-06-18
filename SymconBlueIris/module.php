<?
    // Klassendefinition
    class IPSWINSNMP extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }

        public function Create() {
            parent::Create();

            // Modul-Eigenschaftserstellung
            $this->RegisterPropertyString("IPAddress", "192.168.178.1"); 
            $this->RegisterPropertyInteger("Port", 81);
            $this->RegisterPropertyInteger("Timeout", 1);
            $this->RegisterPropertyInteger("Interval", 10);
            $this->RegisterPropertyString("Username", "admin");
            $this->RegisterPropertyString("Password", "");

            //event erstellen
            $this->RegisterTimer("SyncData", $this->ReadPropertyInteger("Interval"), 'IPSWINSNMP_SyncData($_IPS[\'TARGET\']);');
        }

        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();
            //$this->RequireParent("{1A75660D-48AE-4B89-B351-957CAEBEF22D}");

            $this->SetTimerInterval("SyncData", $this->ReadPropertyInteger("Interval")*1000);
        }

        public function SyncData(){
            
        }
    }
?>