<?php
    // Klassendefinition
    class SymconWS2812 extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }

        public function Create() {
            parent::Create();

            // Modul-Eigenschaftserstellung
            $this->RegisterPropertyString("IPAddress", "192.168.178.1"); 
            $this->RegisterPropertyString("MACAddress", "aa:bb:cc:00:11:22"); 
            $this->RegisterPropertyInteger("Interval", 10);

            $this->RegisterPropertyString("SIPAddress", "127.0.0.1");
            $this->RegisterPropertyString("SPort", "8001");
            $this->RegisterPropertyInteger("Timeout", 3);
            

            //event erstellen
            $this->RegisterTimer("CheckOnline", $this->ReadPropertyInteger("Interval"), 'CheckOnline_SyncData($_IPS[\'TARGET\'], false);');
        }

        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();

            $this->SetStatus(102);
            $this->SetTimerInterval("CheckOnline", $this->ReadPropertyInteger("Interval")*1000);
        }

        public function WakeUp(){
            $broadcast = $this->ReadPropertyString("IPAddress");
            $mac_addr = $this->ReadPropertyString("MACAddress");
            $timeout = $this->ReadPropertyInteger("Timeout");

            if (!$fp = fsockopen('udp://' . $broadcast, 2304, $errno, $errstr, $timeout)) 
                return false; 

            $mac_hex = preg_replace('=[^a-f0-9]=i', '', $mac_addr); 

            $mac_bin = pack('H12', $mac_hex); 

            $data = str_repeat("\xFF", 6) . str_repeat($mac_bin, 16); 

            fputs($fp, $data); 
            fclose($fp); 
            return true; 

        }

    }
