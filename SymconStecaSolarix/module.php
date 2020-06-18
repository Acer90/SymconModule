<?
 // Klassendefinition
    class StecaSolarix extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }

        public function Create() {
            parent::Create();

            // Modul-Eigenschaftserstellung
            $this->RegisterPropertyInteger("Interval", 2);
            $this->RegisterPropertyInteger("IntervalProperties", 600);

            $this->ConnectParent("{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}");
            $this->GetConfigurationForParent();

            $this->RegisterVariableBoolean("VarBuzzer", "Summer", "~Switch", 0);
            $this->EnableAction("VarBuzzer");

            $this->RegisterVariableBoolean("VarDisplay", "Display Beleuchtung", "~Switch", 0);
            $this->EnableAction("VarDisplay");

            $this->RegisterVariableBoolean("VarPowerSavingMode", "Energiesparmodus", "~Switch", 0);
            $this->EnableAction("VarPowerSavingMode");

            $this->RegisterVariableBoolean("VarOverloadAutoRestart", "Überlast Neustart", "~Switch", 0);
            $this->EnableAction("VarOverloadAutoRestart");

            $this->RegisterVariableBoolean("VarOverTempertureAutoRestart", "Übertemperatur Neustart", "~Switch", 0);
            $this->EnableAction("VarOverTempertureAutoRestart");

            $this->RegisterVariableBoolean("VarInterrupsBeeps", "Alarmton bei Ausfall einer Versogungsquelle", "~Switch", 0);
            $this->EnableAction("VarInterrupsBeeps");

            $this->RegisterVariableBoolean("VarOverloadBypass", "Überlast Bypass", "~Switch", 0);
            $this->EnableAction("VarOverloadBypass");

            $this->RegisterVariableBoolean("VarScreenReturnsToDefault", "Automatische Rückehr zum Hauptmenü", "~Switch", 0);
            $this->EnableAction("VarScreenReturnsToDefault");

            $this->RegisterVariableBoolean("VarSolarEnergyBalance", "Solarenergieausgleich", "~Switch", 0);
            $this->EnableAction("VarSolarEnergyBalance");

            $this->RegisterVariableBoolean("VarFaultCodeRecord", "Fehlercode", "~Switch", 0);
            $this->EnableAction("VarFaultCodeRecord");

            $this->SetBuffer("DataBuffer", "");


            if (!IPS_VariableProfileExists("Steca_Modus")){
                IPS_CreateVariableProfile("Steca_Modus", 1);
                IPS_SetVariableProfileAssociation("Steca_Modus", 0, "Fehler", "", 0xff0000);
                IPS_SetVariableProfileAssociation("Steca_Modus", 1, "An", "", -1);
                IPS_SetVariableProfileAssociation("Steca_Modus", 2, "Stand-by", "", -1);
                IPS_SetVariableProfileAssociation("Steca_Modus", 3, "Netz", "", 0x00ff00);
                IPS_SetVariableProfileAssociation("Steca_Modus", 4, "Batterie", "", 0x00ff00);
                IPS_SetVariableProfileAssociation("Steca_Modus", 5, "Energiespar", "", 0x00ff00);
            }
            $this->RegisterVariableInteger("VarModus", "Modus", "Steca_Modus", 0);


            if (!IPS_VariableProfileExists("Steca_Spannung")){
                IPS_CreateVariableProfile("Steca_Spannung", 1);
                IPS_SetVariableProfileText("Steca_Spannung", "", "V");
            }

            if (!IPS_VariableProfileExists("Steca_Leistung")){
                IPS_CreateVariableProfile("Steca_Leistung", 1);
                IPS_SetVariableProfileText("Steca_Leistung", "", " Watt");
            }

            if (!IPS_VariableProfileExists("Steca_Scheinleistung")){
                IPS_CreateVariableProfile("Steca_Scheinleistung", 1);
                IPS_SetVariableProfileText("Steca_Scheinleistung", "", " VA");
            }

            if (!IPS_VariableProfileExists("Steca_Strom")){
                IPS_CreateVariableProfile("Steca_Strom", 1);
                IPS_SetVariableProfileText("Steca_Strom", "", " A");
            }

            if (!IPS_VariableProfileExists("Steca_Temperatur")){
                IPS_CreateVariableProfile("Steca_Temperatur", 1);
                IPS_SetVariableProfileText("Steca_Temperatur", "", " °C");
            }

            if (!IPS_VariableProfileExists("Steca_Ladezustand")){
                IPS_CreateVariableProfile("Steca_Ladezustand", 1);
                IPS_SetVariableProfileAssociation("Steca_Ladezustand", 0, "Kein laden", "", -1);
                IPS_SetVariableProfileAssociation("Steca_Ladezustand", 1, "Laden mit Solar-Laderegler", "", 0x00ff00);
                IPS_SetVariableProfileAssociation("Steca_Ladezustand", 2, "Laden mit AC Quelle", "", 0x00ff00);
                IPS_SetVariableProfileAssociation("Steca_Ladezustand", 3, "Laden mit Solar-Laderegler und AC", "", 0x00ff00);
            }

            $this->RegisterVariableFloat("VarNetzspannung", "Netzspannung", "~Volt.230", 0);
            $this->RegisterVariableFloat("VarNetzfrequenze", "Netzfrequenze", "~Hertz", 0);
            $this->RegisterVariableFloat("VarACAusgangspannung", "AC Ausgangspannung", "~Volt.230", 0);
            $this->RegisterVariableFloat("VarACAusgangsfrequenz", "AC Ausgangsfrequenz", "~Hertz", 0);
            $this->RegisterVariableInteger("VarACScheinleistung", "AC Scheinleistung", "Steca_Scheinleistung", 0);
            $this->RegisterVariableInteger("VarACWirkleistung", "AC Wirkleistung", "Steca_Leistung", 0);
            $this->RegisterVariableInteger("VarAusgangslast", "Ausgangslast", "~Intensity.100", 0);
            $this->RegisterVariableInteger("VarBusSpannung", "Interne Bus-Spannung", "Steca_Spannung", 0);
            $this->RegisterVariableFloat("VarBatteriespannungWR", "Batteriespannung (Wechselrichter)", "~Volt.230", 0);
            $this->RegisterVariableInteger("VarBatterieLadestrom", "Batterie-Ladestrom", "Steca_Strom", 0);
            $this->RegisterVariableInteger("VarBatterieKapazitaet", "Batterie-Kapazität", "~Intensity.100", 0);
            $this->RegisterVariableInteger("VarKuehlkoerperTemperatur", "Kühlkörper-Temperatur", "Steca_Temperatur", 0);
            $this->RegisterVariableInteger("VarPVEingangsstromBAT", "PV Eingangsstrom (batterieseitig)", "Steca_Strom", 0);
            $this->RegisterVariableFloat("VarPVSpannung", "PV Spannung", "~Volt.230", 0);
            $this->RegisterVariableFloat("VarBatteriespannungLR", "Batteriespannung (Laderegler)", "~Volt.230", 0);
            $this->RegisterVariableInteger("VarBatterieEntladestrom", "Batterie-Entladestrom", "Steca_Strom", 0);
            $this->RegisterVariableInteger("VarLadezustand", "Ladezustand", "Steca_Ladezustand", 0);
            $this->RegisterVariableInteger("VarPVLadeleistung", "PV Ladeleistung", "Steca_Leistung", 0);


            if (!IPS_VariableProfileExists("Steca_Ladequelle")){
                IPS_CreateVariableProfile("Steca_Ladequelle", 1);
                IPS_SetVariableProfileAssociation("Steca_Ladequelle", 0, "Utility", "", -1);
                IPS_SetVariableProfileAssociation("Steca_Ladequelle", 1, "Solar first", "", -1);
                IPS_SetVariableProfileAssociation("Steca_Ladequelle", 2, "Utility and Solar", "", -1);
                IPS_SetVariableProfileAssociation("Steca_Ladequelle", 3, "Solar Only", "", -1);
            }
            $this->RegisterVariableInteger("VarLadequelle", "Ladequelle", "Steca_Ladequelle", 0);
            $this->EnableAction("VarLadequelle");

            if (!IPS_VariableProfileExists("Steca_ACEingansbereich")){
                IPS_CreateVariableProfile("Steca_ACEingansbereich", 1);
                IPS_SetVariableProfileAssociation("Steca_ACEingansbereich", 0, "Appliance", "", -1);
                IPS_SetVariableProfileAssociation("Steca_ACEingansbereich", 1, "UPS", "", -1);
            }
            $this->RegisterVariableInteger("VarACEingansbereich", "AC Eingansbereich", "Steca_ACEingansbereich", 0);
            $this->EnableAction("VarACEingansbereich");

            if (!IPS_VariableProfileExists("Steca_Netzrueckkehrspannung")){
                IPS_CreateVariableProfile("Steca_Netzrueckkehrspannung", 1);
                IPS_SetVariableProfileText("Steca_Netzrueckkehrspannung", "", " V");
                IPS_SetVariableProfileValues("Steca_Netzrueckkehrspannung", 44, 51, 1);
            }
            $this->RegisterVariableInteger("VarNetzrueckkehrspannung", "Netzrückkehrspannung", "Steca_Netzrueckkehrspannung", 0);
            $this->EnableAction("VarNetzrueckkehrspannung");

            if (!IPS_VariableProfileExists("Steca_maxLadestrom")){
                IPS_CreateVariableProfile("Steca_maxLadestrom", 1);
                IPS_SetVariableProfileText("Steca_maxLadestrom", "", " A");
                IPS_SetVariableProfileValues("Steca_maxLadestrom", 10, 140, 10);
            }
            $this->RegisterVariableInteger("VarmaxLadestrom", "max Ladestrom", "Steca_maxLadestrom", 0);
            $this->EnableAction("VarmaxLadestrom");

            if (!IPS_VariableProfileExists("Steca_Entladespannung")){
                IPS_CreateVariableProfile("Steca_Entladespannung", 1);
                IPS_SetVariableProfileValues("Steca_Entladespannung", 48, 59, 1);
                IPS_SetVariableProfileAssociation("Steca_Entladespannung", 48, "48.0 V", "", -1);
                IPS_SetVariableProfileAssociation("Steca_Entladespannung", 49, "49.0 V", "", -1);
                IPS_SetVariableProfileAssociation("Steca_Entladespannung", 50, "50.0 V", "", -1);
                IPS_SetVariableProfileAssociation("Steca_Entladespannung", 51, "51.0 V", "", -1);
                IPS_SetVariableProfileAssociation("Steca_Entladespannung", 52, "52.0 V", "", -1);
                IPS_SetVariableProfileAssociation("Steca_Entladespannung", 53, "53.0 V", "", -1);
                IPS_SetVariableProfileAssociation("Steca_Entladespannung", 54, "54.0 V", "", -1);
                IPS_SetVariableProfileAssociation("Steca_Entladespannung", 55, "55.0 V", "", -1);
                IPS_SetVariableProfileAssociation("Steca_Entladespannung", 56, "56.0 V", "", -1);
                IPS_SetVariableProfileAssociation("Steca_Entladespannung", 57, "57.0 V", "", -1);
                IPS_SetVariableProfileAssociation("Steca_Entladespannung", 58, "58.0 V", "", -1);
                IPS_SetVariableProfileAssociation("Steca_Entladespannung", 59, "Full", "", 0x00ff00);
            }
            $this->RegisterVariableInteger("VarEntladespannung", "Entladespannung", "Steca_Entladespannung", 0);
            $this->EnableAction("VarEntladespannung");

            if (!IPS_VariableProfileExists("Steca_maxACLadestrom")){
                IPS_CreateVariableProfile("Steca_maxACLadestrom", 1);
                IPS_SetVariableProfileAssociation("Steca_maxACLadestrom", 2, "2 A", "", -1);
                IPS_SetVariableProfileAssociation("Steca_maxACLadestrom", 10, "10 A", "", -1);
                IPS_SetVariableProfileAssociation("Steca_maxACLadestrom", 20, "20 A", "", -1);
                IPS_SetVariableProfileAssociation("Steca_maxACLadestrom", 30, "30 A", "", -1);
                IPS_SetVariableProfileAssociation("Steca_maxACLadestrom", 40, "40 A", "", -1);
                IPS_SetVariableProfileAssociation("Steca_maxACLadestrom", 50, "50 A", "", -1);
                IPS_SetVariableProfileAssociation("Steca_maxACLadestrom", 60, "60 A", "", -1);
            }
            $this->RegisterVariableInteger("VarmaxACLadestrom", "max AC-Ladestrom", "Steca_maxACLadestrom", 0);
            $this->EnableAction("VarmaxACLadestrom");

            if (!IPS_VariableProfileExists("Steca_Ausgangsquelle")){
                IPS_CreateVariableProfile("Steca_Ausgangsquelle", 1);
                IPS_SetVariableProfileAssociation("Steca_Ausgangsquelle", 0, "Utility", "", -1);
                IPS_SetVariableProfileAssociation("Steca_Ausgangsquelle", 1, "Solar", "", -1);
                IPS_SetVariableProfileAssociation("Steca_Ausgangsquelle", 2, "SBU", "", -1);
            }
            $this->RegisterVariableInteger("VarAusgangsquelle", "Ausgangsquelle", "Steca_Ausgangsquelle", 0);
            $this->EnableAction("VarAusgangsquelle");


            //event erstellen
            $this->RegisterTimer("Load_LiveData", $this->ReadPropertyInteger("Interval"), 'StecaSolarix_Load_LiveData($_IPS[\'TARGET\']);');
            $this->RegisterTimer("Load_LiveProperties", $this->ReadPropertyInteger("IntervalProperties"), 'StecaSolarix_Load_LiveProperties($_IPS[\'TARGET\']);');

            $this->SetStatus(102);
        }

        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();

            $this->SetStatus(102);
            $this->SetTimerInterval("Load_LiveData", $this->ReadPropertyInteger("Interval")*1000);
            $this->SetTimerInterval("Load_LiveProperties", $this->ReadPropertyInteger("IntervalProperties")*1000);

            $this->ConnectParent("{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}");
            $this->GetConfigurationForParent();

        }

        public function Load_LiveData()
        {
            $this->SendToIO(hex2bin("514D4F4449C10D"));//51 4D 4F 44 49 C1 0D                                 QMODIÁ.
            sleep(1);
            $this->SendToIO(hex2bin("5150494753B7A90D"));//51 50 49 47 53 B7 A9 0D                            QPIGS·©.

        }

        public function Load_LiveProperties()
        {

            $this->SendToIO(hex2bin("51464C414798740D"));//51 46 4C 41 47 98 74 0D                            QFLAG˜t.
            sleep(1);
            $this->SendToIO(hex2bin("5150495249F8540D"));//51 50 49 52 49 F8 54 0D                            QPIRIøT.
        }

        public function ReceiveData($JSONString) {
            $data = json_decode($JSONString);
            $data_str = utf8_decode($data->Buffer);
            $Bufferdata = $this->GetBuffer("DataBuffer");
            $data_str = $Bufferdata.$data_str;

            if($data_str[0] != "("){
                $this->SetBuffer("DataBuffer", "");
                return;
            }else{
                $last_str = $rest = substr(bin2hex($data_str), -2, 2);
                if($last_str != "0d"){
                    $this->SetBuffer("DataBuffer", $data_str);
                    return;
                }
            }

            $this->SetBuffer("DataBuffer", "");
            $this->SendDebug("ReceiveData", $data_str, 0);
            $zeichen = strlen($data_str);

            switch($zeichen){
                case 5:
                    //Modus
                    $data_str = substr($data_str,1,$zeichen-4);

                    switch($data_str){
                        case "F": SetValue($this->GetIDForIdent("VarModus"), 0);
                            break;
                        case "P": SetValue($this->GetIDForIdent("VarModus"), 1);
                            break;
                        case "S": SetValue($this->GetIDForIdent("VarModus"), 2);
                            break;
                        case "L": SetValue($this->GetIDForIdent("VarModus"), 3);
                            break;
                        case "B": SetValue($this->GetIDForIdent("VarModus"), 4);
                            break;
                        case "H": SetValue($this->GetIDForIdent("VarModus"), 5);
                            break;
                    }
                    break;
                case 102:
                    //Peri
                    $data_str = substr($data_str,1,$zeichen-4);
                    $data_arr = explode(" ", $data_str);

                    SetValue($this->GetIDForIdent("VarNetzrueckkehrspannung"), $data_arr[8]);
                    SetValue($this->GetIDForIdent("VarmaxACLadestrom"), $data_arr[13]);
                    SetValue($this->GetIDForIdent("VarmaxLadestrom"), $data_arr[14]);
                    SetValue($this->GetIDForIdent("VarACEingansbereich"), $data_arr[15]);
                    SetValue($this->GetIDForIdent("VarAusgangsquelle"), $data_arr[16]);
                    SetValue($this->GetIDForIdent("VarLadequelle"), $data_arr[17]);
                    SetValue($this->GetIDForIdent("VarEntladespannung"), $data_arr[22]);
                    SetValue($this->GetIDForIdent("VarSolarEnergyBalance"), $data_arr[24]);
                    break;
                case 110:
                    //Live Data
                    $data_str = substr($data_str,1,$zeichen-4);
                    $data_arr = explode(" ", $data_str);

                    SetValue($this->GetIDForIdent("VarNetzspannung"), $data_arr[0]);
                    SetValue($this->GetIDForIdent("VarNetzfrequenze"), $data_arr[1]);
                    SetValue($this->GetIDForIdent("VarACAusgangspannung"), $data_arr[2]);
                    SetValue($this->GetIDForIdent("VarACAusgangsfrequenz"), $data_arr[3]);
                    SetValue($this->GetIDForIdent("VarACScheinleistung"), $data_arr[4]);
                    SetValue($this->GetIDForIdent("VarACWirkleistung"), $data_arr[5]);
                    SetValue($this->GetIDForIdent("VarAusgangslast"), $data_arr[6]);
                    SetValue($this->GetIDForIdent("VarBusSpannung"), $data_arr[7]);
                    SetValue($this->GetIDForIdent("VarBatteriespannungWR"), $data_arr[8]);
                    SetValue($this->GetIDForIdent("VarBatterieLadestrom"), $data_arr[9]);
                    SetValue($this->GetIDForIdent("VarBatterieKapazitaet"), $data_arr[10]);
                    SetValue($this->GetIDForIdent("VarKuehlkoerperTemperatur"), $data_arr[11]);
                    SetValue($this->GetIDForIdent("VarPVEingangsstromBAT"), $data_arr[12]);
                    SetValue($this->GetIDForIdent("VarPVSpannung"), $data_arr[13]);
                    SetValue($this->GetIDForIdent("VarBatteriespannungLR"), $data_arr[14]);
                    SetValue($this->GetIDForIdent("VarBatterieEntladestrom"), $data_arr[15]);

                    $geraetestatus = substr($data_arr[16],5 ,$zeichen);
                    //$this->SendDebug("ReceiveData", $geraetestatus, 0);
                    switch($geraetestatus){
                        case "000":
                            SetValue($this->GetIDForIdent("VarLadezustand"), 0);
                            break;
                        case "110":
                            SetValue($this->GetIDForIdent("VarLadezustand"), 1);
                            break;
                        case "101":
                            SetValue($this->GetIDForIdent("VarLadezustand"), 2);
                            break;
                        case "111":
                            SetValue($this->GetIDForIdent("VarLadezustand"), 3);
                            break;
                    }
                    if(count($data_arr) > 19) $this->SetValue("VarPVLadeleistung",  $data_arr[19]);

                    break;
                case 15:
                    //FlagList
                    $data_str = substr($data_str,2,$zeichen-4);
                    //$this->SendDebug("ReceiveData", $data_str, 0);

                    $data_arr = str_split($data_str);
                    $status_on = true;

                    foreach($data_arr as $item){
                        switch($item) {
                            case "a";//a = buzzer
                                SetValue($this->GetIDForIdent("VarBuzzer"), $status_on);
                                break;
                            case "b";//b = Overload Bypass
                                SetValue($this->GetIDForIdent("VarOverloadBypass"), $status_on);
                                break;
                            case "j";//j = Power saving Mode
                                SetValue($this->GetIDForIdent("VarPowerSavingMode"), $status_on);
                                break;
                            case "k";//k = Screen Returns to default
                                SetValue($this->GetIDForIdent("VarScreenReturnsToDefault"), $status_on);
                                break;
                            case "u";//u = Overload auto restart
                                SetValue($this->GetIDForIdent("VarOverloadAutoRestart"), $status_on);
                                break;
                            case "v";//v = Over temperture auto restart
                                SetValue($this->GetIDForIdent("VarOverTempertureAutoRestart"), $status_on);
                                break;
                            case "x";//x = Backlight
                                SetValue($this->GetIDForIdent("VarDisplay"), $status_on);
                                break;
                            case "y";//y = Interrups Beeps
                                SetValue($this->GetIDForIdent("VarInterrupsBeeps"), $status_on);
                                break;
                            case "z";//z = Fault code record
                                SetValue($this->GetIDForIdent("VarFaultCodeRecord"), $status_on);
                                break;
                            case "D";
                                $status_on = false;
                                break;
                        }
                    }
                    break;
            }
        }

        public function Test()
        {
            //auslesen
            //$this->SendToIO("QPIGS·©\r");//Default Daten
            //$this->SendToIO("QMODIÁ\r"); //Modus
            //$this->SendToIO("QBEQI.©\r"); //

            //51 50 49 52 49 F8 54 0D                            QPIRIøT.
            //Output = (230.0 21.7 230.0 50.0 21.7 5000 5000 48.0 47.0 42.0 56.4 54.0 0 02 110 0 2 2 9 01 0 0 57.0 0 0 000b

            //51 42 45 51 49 2E A9 0D                            QBEQI.©.
            //Output = (0 060 030 110 030 60.00 000 120 0o

            //51 53 49 44 BB 05 0D                               QSID»..
            //Output = (2076060700001801150005Ns

            //51 4D 4F 44 49 C1 0D                               QMODIÁ.
            //Output = (B

            //51 50 49 47 53 B7 A9 0D                            QPIGS·©.
            //Output = (229.2 50.0 229.9 50.0 0344 0333 006 339 48.70 000 065 0028 0000 000.0 00.00 00007 00010000 00 00 00000 010B

            //51 50 47 53 30 3F DA 0D                            QPGS0?Ú.
            //Output = (1 00001801150005 B 00 228.9 49.99 229.9 50.02 0344 0330 006 48.7 000 065 000.0 000 00344 00330 006 00000010 0 2 110 140 02 00 007,

            //51 44 49 71 1B 0D                                  QDIq..
            //Output = (230.0 50.0 0030 42.0 54.0 56.4 46.0 60 0 0 2 0 0 0 0 0 1 1 0 0 1 0 54.0 0 1 000`

            //51 46 4C 41 47 98 74 0D                            QFLAG˜t.
            //Output =(EabjkuvxyzD <-- alles ein
            //Output1=(EaDbjkuvxyz
            //28 45 62 6A 76 78 7A 44 61 6B 75 79 1A E6 0D       (EbjvxzDakuy.æ.
            //Alles nach dem "D" ist ausgeschalten !!!!!
            //a = buzzer
            //b = Overload Bypass
            //j = Power saving Mode
            //k = Screen Returns to default
            //u = Overload auto restart
            //v = Over temperture auto restart
            //x = Backlight
            //y = Interrups Beeps
            //z = Fault code record




            //
            //Output =

            //Ladequelle
            //$this->SendToIO("PPCP000æá\r");//LQ Utility             | 50 50 43 50 30 30 30 E6 E1 0D PPCP000æá
            //$this->SendToIO("PPCP001öÀ\r");//LQ Solar first         | 50 50 43 50 30 30 31 F6 C0 0D PPCP001öÀ
            //$this->SendToIO("PPCP002Æ£\v\r");//LQ Utility and Solar | 50 50 43 50 30 30 32 C6 A3 0D PPCP002Æ£
            //$this->SendToIO("PPCP003Ö‚\v\r");//LQ Solar Only        | 50 50 43 50 30 30 33 D6 82 0D PPCP003Ö‚

            //AC Eingansbereich
            //$this->SendToIO("PGR00)ë\r");//AC-Input Appliance | 50 47 52 30 30 29 EB 0D             PGR00)ë
            //$this->SendToIO("PGR019Ê\r");//AC-Input UPS  | 50 47 52 30 31 39 CA 0D                  PGR019Ê

            //Netzrückkehrspannung
            //$this->SendToIO("PBCV44,0€‰\r");//NR-Spannung 44.0 | 50 42 43 56 34 34 2C 30 80 89 0D  PBCV44,0€‰
            //$this->SendToIO("PBCV45,0·¹\r");//NR-Spannung 45.0  | 50 42 43 56 34 35 2C 30 B7 B9 0D  PBCV45,0·¹
            //$this->SendToIO("PBCV46,0îé\r");//NR-Spannung 46.0  | 50 42 43 56 34 36 2C 30 EE E9 0D  PBCV46,0îé
            //$this->SendToIO("PBCV47,0ÙÙ\r");//NR-Spannung 47.0  | 50 42 43 56 34 37 2C 30 D9 D9 0D  PBCV47,0ÙÙ
            //$this->SendToIO("PBCV48,0õè\r");//NR-Spannung 48.0  | 50 42 43 56 34 38 2C 30 F5 E8 0D  PBCV48,0õè
            //$this->SendToIO("PBCV49,0ÂØ\r");//NR-Spannung 49.0  | 50 42 43 56 34 39 2C 30 C2 D8 0D  PBCV49,0ÂØ
            //$this->SendToIO("PBCV50,0*ý\r");//NR-Spannung 50.0  | 50 42 43 56 35 30 2C 30 2A FD 0D  PBCV50,0*ý
            //$this->SendToIO("PBCV51,0".chr(29)."Í\r");//NR-Spannung 51.0 | 50 42 43 56 35 31 2C 30 1D CD 0D   PBCV51,0.

            //max Ladestrom
            //$this->SendToIO("\r");//Max. LS 10    | 4D4E4348474330303130123B0D             MNCHGC0010.;
            //$this->SendToIO("\r");//Max. LS 20    | 4D4E434847433030323047680D             MNCHGC0020Gh
            //$this->SendToIO("\r");//Max. LS 30    | 4D4E434847433030333074590D             MNCHGC0030tY
            //$this->SendToIO("\r");//Max. LS 40    | 4D4E4348474330303430EDCE0D             MNCHGC0040íÎ
            //$this->SendToIO("\r");//Max. LS 50    | 4D4E4348474330303530DEFF0D             MNCHGC0050Þÿ
            //$this->SendToIO("\r");//Max. LS 60    | 4D4E43484743303036308BAC0D             MNCHGC0060‹¬
            //$this->SendToIO("\r");//Max. LS 70    | 4D4E4348474330303730B89D0D             MNCHGC0070¸
            //$this->SendToIO("\r");//Max. LS 80    | 4D4E4348474330303830A8A30D             MNCHGC0080¨£
            //$this->SendToIO("\r");//Max. LS 90    | 4D4E43484743303039309B920D             MNCHGC0090›’
            //$this->SendToIO("\r");//Max. LS 100   | 4D4E4348474330313030163A0D             MNCHGC0100.:
            //$this->SendToIO(hex2bin(""));//Max. LS 110    | 4D4E4348474330313130250B0D             MNCHGC0110%.
            //$this->SendToIO("\r");//Max. LS 120   | 4D4E434847433031323070580D             MNCHGC0120pX
            //$this->SendToIO("\r");//Max. LS 130   | 4D4E434847433031333043690D             MNCHGC0130Ci
            //$this->SendToIO("\r");//Max. LS 140   | 4D4E4348474330313430DAFE0D             MNCHGC0140Úþ

            //Entladespannung (Ladeschlusspannung)
            //$this->SendToIO("\r");//LSS 48.0          | 5042445634382C303DA90D                   PBDV48,0=©
            //$this->SendToIO("\r");//LSS 49.0          | 5042445634392C300B990D                   PBDV49,0.™
            //$this->SendToIO("\r");//LSS 50.0          | 5042445635302C30E2BC0D                   PBDV50,0â¼
            //$this->SendToIO("\r");//LSS 51.0          | 5042445635312C30D58C0D                   PBDV51,0ÕŒ
            //$this->SendToIO("\r");//LSS 52.0          | 5042445635322C308CDC0D                   PBDV52,0ŒÜ
            //$this->SendToIO("\r");//LSS 53.0          | 5042445635332C30BBEC0D                   PBDV53,0»ì
            //$this->SendToIO("\r");//LSS 54.0          | 5042445635342C303E7C0D                   PBDV54,0>|
            //$this->SendToIO("\r");//LSS 55.0          | 5042445635352C30094C0D                   PBDV55,0.L
            //$this->SendToIO("\r");//LSS 56.0          | 5042445635362C30501C0D                   PBDV56,0P.
            //$this->SendToIO("\r");//LSS 57.0          | 5042445635372C30672C0D                   PBDV57,0g,
            //$this->SendToIO("\r");//LSS 58.0          | 5042445635382C304B1D0D                   PBDV58,0K.
            //$this->SendToIO("\r");//LSS FULL          | 5042445630302C305EF90D                   PBDV00,0^ù

            //max AC-Ladestrom
            //$this->SendToIO("\r");//Max. AC-LS 02     | 4D 55 43 48 47 43 30 30 32 B5 D1 0D                MUCHGC002µÑ
            //$this->SendToIO("\r");//Max. AC-LS 10     | 4D 55 43 48 47 43 30 31 30 A6 A2 0D                MUCHGC010¦¢
            //$this->SendToIO("\r");//Max. AC-LS 20     | 4D 55 43 48 47 43 30 32 30 F3 F1 0D                MUCHGC020óñ
            //$this->SendToIO("\r");//Max. AC-LS 30     | 4D 55 43 48 47 43 30 33 30 C0 C0 0D                MUCHGC030ÀÀ
            //$this->SendToIO("\r");//Max. AC-LS 40     | 4D 55 43 48 47 43 30 34 30 59 57 0D                MUCHGC040YW
            //$this->SendToIO("\r");//Max. AC-LS 50     | 4D 55 43 48 47 43 30 35 30 6A 66 0D                MUCHGC050jf
            //$this->SendToIO("\r");//Max. AC-LS 60     | 4D 55 43 48 47 43 30 36 30 3F 35 0D                MUCHGC060?5


            //Ausgangsquelle
            //$this->SendToIO("POP00ÂH\r");//AQ Utility                 | 50 4F 50 30 30 C2 48 0D                           POP00ÂH
            //$this->SendToIO("POP01Òi\r");//AQ Solar               | 50 4F 50 30 31 D2 69 0D                             POP01Òi
            //$this->SendToIO("POP02â\v\r");//AQ SBU                | 50 4F 50 30 32 E2 0B 0D                              POP02â

            //Alarmton
            //$this->SendToIO("PDaãA\r");//Alarmton aus |  50 44 61 E3 41 0D                                PDaãA
            //$this->SendToIO("PEaÐp\r");//Alarmton an  |  50 45 61 D0 70 0D                                PEaÐp
            //
            //Energiesparmodus
            //$this->SendToIO("PDjR*\r");//Energiesparmodus aus |  50 44 6A 52 2A 0D                        PDjR*
            //$this->SendToIO("PEja\e\r");//Energiesparmodus an |  50 45 6A 61 1B 0D                        PEja.

            //Hintergundbeleuchtung
            //$this->SendToIO("PDx`Y\r");//Hintergundbeleuchtung aus | 50 44 78 60 59 0D                    PDx`Y
            //$this->SendToIO("PExSh\r");//Hintergundbeleuchtung an  | 50 45 78 53 68 0D                    PExSh

            //Auto-Überlastneustart
            //$this->SendToIO("PDu±ô\r");//Auto-Überlastneustart aus | 50 44 75 B1 F4 0D                    PDu±ô
            //$this->SendToIO("PEuéÅ\r");//Auto-Überlastneustart an  | 50 45 75 82 C5 0D                    PEu‚

            //Auto-Übertemp.neustart
            //$this->SendToIO(hex2bin("50447681970D"));//Auto-Übertemp.neustart aus | 50 44 76 81 97 0D     PDv—
            //$this->SendToIO("PEv²¦\r");//Auto-Übertemp.neustart an  | 50 45 76 B2 A6 0D                   PEv²¦

            //Alarmton-Versogungsquelle
            //$this->SendToIO("PDypx\r");//Alarmton-Versogungsquelle aus | 50 44 79 70 78 0D                PDypx
            //$this->SendToIO("PEyCI\r");//Alarmton-Versogungsquelle an  | 50 45 79 43 49 0D                PEyCI

            //Überlast-bypass
            //$this->SendToIO("PDbÓ\"\r");//Überlast-bypass aus | 50 44 62 D3 22 0D                         PDbÓ"
            //$this->SendToIO("PEbà".chr(19)."\r");//Überlast-bypass an  | 50 45 62 E0 13 0D                PEbà.

            //zurück-Hauptmenü
            //$this->SendToIO("PDkB\v\r");//zurück-Hauptmenü aus | 50 44 6B 42 0B 0D                        PDkB.
            //$this->SendToIO("PEkq:\r");//zurück-Hauptmenü an   | 50 45 6B 71 3A 0D                        PEkq:

            //Solarenergieausgleich
            //$this->SendToIO("PSPB0øæ\r");//Solarenergieausgleich aus | 50 53 50 42 30 F8 E6 0D            PSPB0øæ
            //$this->SendToIO("PSPB1èÇ\r");//Solarenergieausgleich an  | 50 53 50 42 31 E8 C7 0D            PSPB1èÇ

            //Fault-code-record
            //$this->SendToIO("PDz@\e\r");//Fault-code-record aus | 50 44 7A 40 1B 0D                       PDz@.
            //$this->SendToIO("PEzs*\r");//Fault-code-record an   | 50 45 7A 73 2A 0D                       PEzs*

        }

        public function RequestAction($Ident, $Value) {

            switch($Ident) {
                case "VarLadequelle":
                    switch($Value){
                        case 0:
                            $this->SendToIO(hex2bin("50504350303030E6E10D"));//LQ Utility           | 50 50 43 50 30 30 30 E6 E1 0D           PPCP000æá
                            break;
                        case 1:
                            $this->SendToIO(hex2bin("50504350303031F6C00D"));//LQ Solar first       | 50 50 43 50 30 30 31 F6 C0 0D           PPCP001öÀ
                            break;
                        case 2:
                            $this->SendToIO(hex2bin("50504350303032C6A30D"));//LQ Utility and Solar | 50 50 43 50 30 30 32 C6 A3 0D           PPCP002Æ£
                            break;
                        case 3:
                            $this->SendToIO(hex2bin("50504350303033D6820D"));//LQ Solar Only        | 50 50 43 50 30 30 33 D6 82 0D           PPCP003Ö‚
                            break;
                    }
                    SetValue($this->GetIDForIdent($Ident), $Value);
                    break;
                case "VarACEingansbereich":
                    switch($Value){
                        case 0:
                            $this->SendToIO(hex2bin("504752303029EB0D"));//AC-Input Appliance       | 50 47 52 30 30 29 EB 0D                 PGR00)ë
                            break;
                        case 1:
                            $this->SendToIO(hex2bin("504752303139CA0D"));//AC-Input UPS             | 50 47 52 30 31 39 CA 0D                 PGR019Ê
                            break;
                    }
                    SetValue($this->GetIDForIdent($Ident), $Value);
                    break;
                case "VarNetzrueckkehrspannung":
                    switch($Value){
                        case 44:
                            $this->SendToIO(hex2bin("5042435634342C3080890D"));//NR-Spannung 44.0   | 50 42 43 56 34 34 2C 30 80 89 0D        PBCV44,0€‰
                            break;
                        case 45:
                            $this->SendToIO(hex2bin("5042435634352C30B7B90D"));//NR-Spannung 45.0   | 50 42 43 56 34 35 2C 30 B7 B9 0D        PBCV45,0·¹
                            break;
                        case 46:
                            $this->SendToIO(hex2bin("5042435634362C30EEE90D"));//NR-Spannung 46.0   | 50 42 43 56 34 36 2C 30 EE E9 0D        PBCV46,0îé
                            break;
                        case 47:
                            $this->SendToIO(hex2bin("5042435634372C30D9D90D"));//NR-Spannung 47.0   | 50 42 43 56 34 37 2C 30 D9 D9 0D        PBCV47,0ÙÙ
                            break;
                        case 48:
                            $this->SendToIO(hex2bin("5042435634382C30F5E80D"));//NR-Spannung 48.0   | 50 42 43 56 34 38 2C 30 F5 E8 0D        PBCV48,0õè
                            break;
                        case 49:
                            $this->SendToIO(hex2bin("5042435634392C30C2D80D"));//NR-Spannung 49.0   | 50 42 43 56 34 39 2C 30 C2 D8 0D        PBCV49,0ÂØ
                            break;
                        case 50:
                            $this->SendToIO(hex2bin("5042435635302C302AFD0D"));//NR-Spannung 50.0   | 50 42 43 56 35 30 2C 30 2A FD 0D        PBCV50,0*ý
                            break;
                        case 51:
                            $this->SendToIO(hex2bin("5042435635312C301DCD0D"));//NR-Spannung 51.0   | 50 42 43 56 35 31 2C 30 1D CD 0D        PBCV51,0.
                            break;
                    }
                    SetValue($this->GetIDForIdent($Ident), $Value);
                    break;
                case "VarmaxLadestrom":
                    switch($Value){
                        case 10:
                            $this->SendToIO(hex2bin("4D4E4348474330303130123B0D"));//Max. LS 10     | 4D4E4348474330303130123B0D             MNCHGC0010.;
                            break;
                        case 20:
                            $this->SendToIO(hex2bin("4D4E434847433030323047680D"));//Max. LS 20     | 4D4E434847433030323047680D             MNCHGC0020Gh
                            break;
                        case 30:
                            $this->SendToIO(hex2bin("4D4E434847433030333074590D"));//Max. LS 30     | 4D4E434847433030333074590D             MNCHGC0030tY
                            break;
                        case 40:
                            $this->SendToIO(hex2bin("4D4E4348474330303430EDCE0D"));//Max. LS 40     | 4D4E4348474330303430EDCE0D             MNCHGC0040íÎ
                            break;
                        case 50:
                            $this->SendToIO(hex2bin("4D4E4348474330303530DEFF0D"));//Max. LS 50     | 4D4E4348474330303530DEFF0D             MNCHGC0050Þÿ
                            break;
                        case 60:
                            $this->SendToIO(hex2bin("4D4E43484743303036308BAC0D"));//Max. LS 60     | 4D4E43484743303036308BAC0D             MNCHGC0060‹¬
                            break;
                        case 70:
                            $this->SendToIO(hex2bin("4D4E4348474330303730B89D0D"));//Max. LS 70     | 4D4E4348474330303730B89D0D             MNCHGC0070¸
                            break;
                        case 80:
                            $this->SendToIO(hex2bin("4D4E4348474330303830A8A30D"));//Max. LS 80     | 4D4E4348474330303830A8A30D             MNCHGC0080¨£
                            break;
                        case 90:
                            $this->SendToIO(hex2bin("4D4E43484743303039309B920D"));//Max. LS 90     | 4D4E43484743303039309B920D             MNCHGC0090›’
                            break;
                        case 100:
                            $this->SendToIO(hex2bin("4D4E4348474330313030163A0D"));//Max. LS 100    | 4D4E4348474330313030163A0D             MNCHGC0100.:
                            break;
                        case 110:
                            $this->SendToIO(hex2bin("4D4E4348474330313130250B0D"));//Max. LS 110    | 4D4E4348474330313130250B0D             MNCHGC0110%.
                            break;
                        case 120:
                            $this->SendToIO(hex2bin("4D4E434847433031323070580D"));//Max. LS 120    | 4D4E434847433031323070580D             MNCHGC0120pX
                            break;
                        case 130:
                            $this->SendToIO(hex2bin("4D4E434847433031333043690D"));//Max. LS 130    | 4D4E434847433031333043690D             MNCHGC0130Ci
                            break;
                        case 140:
                            $this->SendToIO(hex2bin("4D4E4348474330313430DAFE0D"));//Max. LS 140    | 4D4E4348474330313430DAFE0D             MNCHGC0140Úþ
                            break;
                    }
                    SetValue($this->GetIDForIdent($Ident), $Value);
                    break;
                case "VarEntladespannung":
                    switch($Value){
                        case 48:
                            $this->SendToIO(hex2bin("5042445634382C303DA90D"));//LSS 48.0           | 5042445634382C303DA90D                   PBDV48,0=©
                            break;
                        case 49:
                            $this->SendToIO(hex2bin("5042445634392C300B990D"));//LSS 49.0           | 5042445634392C300B990D                   PBDV49,0.™
                            break;
                        case 50:
                            $this->SendToIO(hex2bin("5042445635302C30E2BC0D"));//LSS 50.0           | 5042445635302C30E2BC0D                   PBDV50,0â¼
                            break;
                        case 51:
                            $this->SendToIO(hex2bin("5042445635312C30D58C0D"));//LSS 51.0           | 5042445635312C30D58C0D                   PBDV51,0ÕŒ
                            break;
                        case 52:
                            $this->SendToIO(hex2bin("5042445635322C308CDC0D"));//LSS 52.0           | 5042445635322C308CDC0D                   PBDV52,0ŒÜ
                            break;
                        case 53:
                            $this->SendToIO(hex2bin("5042445635332C30BBEC0D"));//LSS 53.0           | 5042445635332C30BBEC0D                   PBDV53,0»ì
                            break;
                        case 54:
                            $this->SendToIO(hex2bin("5042445635342C303E7C0D"));//LSS 54.0           | 5042445635342C303E7C0D                   PBDV54,0>|
                            break;
                        case 55:
                            $this->SendToIO(hex2bin("5042445635352C30094C0D"));//LSS 55.0           | 5042445635352C30094C0D                   PBDV55,0.L
                            break;
                        case 56:
                            $this->SendToIO(hex2bin("5042445635362C30501C0D"));//LSS 56.0           | 5042445635362C30501C0D                   PBDV56,0P.
                            break;
                        case 57:
                            $this->SendToIO(hex2bin("5042445635372C30672C0D"));//LSS 57.0           | 5042445635372C30672C0D                   PBDV57,0g,
                            break;
                        case 58:
                            $this->SendToIO(hex2bin("5042445635382C304B1D0D"));//LSS 58.0           | 5042445635382C304B1D0D                   PBDV58,0K.
                            break;
                        case 59:
                            $this->SendToIO(hex2bin("5042445630302C305EF90D"));//LSS FULL           | 5042445630302C305EF90D                   PBDV00,0^ù
                            break;
                    }
                    SetValue($this->GetIDForIdent($Ident), $Value);
                    break;
                case "VarmaxACLadestrom":
                    switch($Value) {
                        case 2:
                            $this->SendToIO(hex2bin("4D5543484743303032B5D10D"));//Max. AC-LS 02    | 4D 55 43 48 47 43 30 30 32 B5 D1 0D                MUCHGC002µÑ
                            break;
                        case 10:
                            $this->SendToIO(hex2bin("4D5543484743303130A6A20D "));//Max. AC-LS 10   | 4D 55 43 48 47 43 30 31 30 A6 A2 0D                MUCHGC010¦¢
                            break;
                        case 20:
                            $this->SendToIO(hex2bin("4D5543484743303230F3F10D"));//Max. AC-LS 20    | 4D 55 43 48 47 43 30 32 30 F3 F1 0D                MUCHGC020óñ
                            break;
                        case 30:
                            $this->SendToIO(hex2bin("4D5543484743303330C0C00D"));//Max. AC-LS 30    | 4D 55 43 48 47 43 30 33 30 C0 C0 0D                MUCHGC030ÀÀ
                            break;
                        case 40:
                            $this->SendToIO(hex2bin("4D554348474330343059570D"));//Max. AC-LS 40    | 4D 55 43 48 47 43 30 34 30 59 57 0D                MUCHGC040YW
                            break;
                        case 50:
                            $this->SendToIO(hex2bin("4D55434847433035306A660D"));//Max. AC-LS 50    | 4D 55 43 48 47 43 30 35 30 6A 66 0D                MUCHGC050jf
                            break;
                        case 60:
                            $this->SendToIO(hex2bin("4D55434847433036303F350D"));//Max. AC-LS 60    | 4D 55 43 48 47 43 30 36 30 3F 35 0D                MUCHGC060?5
                            break;
                    }
                    SetValue($this->GetIDForIdent($Ident), $Value);
                    break;
                case "VarAusgangsquelle":
                    switch($Value){
                        case 0:
                            $this->SendToIO(hex2bin("504F503030C2480D"));//AQ Utility               | 50 4F 50 30 30 C2 48 0D                           POP00ÂH
                            break;
                        case 1:
                            $this->SendToIO(hex2bin("504F503031D2690D"));//AQ Solar                 | 50 4F 50 30 31 D2 69 0D                           POP01Òi
                            break;
                        case 2:
                            $this->SendToIO(hex2bin("504F503032E20B0D"));//AQ SBU                   | 50 4F 50 30 32 E2 0B 0D                           POP02â
                            break;
                    }
                    SetValue($this->GetIDForIdent($Ident), $Value);
                    break;
                case "VarBuzzer":
                    if($Value){
                        $this->SendToIO(hex2bin("504561D0700D"));//Alarmton an                      | 50 45 61 D0 70 0D                                PEaÐp
                    }else{
                        $this->SendToIO(hex2bin("504461E3410D"));//Alarmton aus                     | 50 44 61 E3 41 0D                                PDaãA
                    }
                    SetValue($this->GetIDForIdent($Ident), $Value);
                    break;
                case "VarDisplay":
                    if($Value){
                        $this->SendToIO(hex2bin("50457853680D"));//Hintergundbeleuchtung an         | 50 45 78 53 68 0D                                PExSh
                    }else{
                        $this->SendToIO(hex2bin("50447860590D"));//Hintergundbeleuchtung aus        | 50 44 78 60 59 0D                                PDx`Y
                    }
                    SetValue($this->GetIDForIdent($Ident), $Value);
                    break;
                case "VarPowerSavingMode":
                    if($Value){
                        $this->SendToIO(hex2bin("50456A611B0D"));//Energiesparmodus an              |  50 45 6A 61 1B 0D                               PEja.
                    }else{
                        $this->SendToIO(hex2bin("50446A522A0D"));//Energiesparmodus aus             |  50 44 6A 52 2A 0D                               PDjR*
                    }
                    SetValue($this->GetIDForIdent($Ident), $Value);
                    break;
                case "VarOverloadAutoRestart":
                    if($Value){
                        $this->SendToIO(hex2bin("50457582C50D"));//Auto-Überlastneustart an         | 50 45 75 82 C5 0D                                PEu‚
                    }else{
                        $this->SendToIO(hex2bin("504475B1F40D"));//Auto-Überlastneustart aus        | 50 44 75 B1 F4 0D                                PDu±ô
                    }
                    SetValue($this->GetIDForIdent($Ident), $Value);
                    break;
                case "VarOverTempertureAutoRestart":
                    if($Value){
                        $this->SendToIO(hex2bin("504576B2A60D"));//Auto-Übertemp.neustart an        | 50 45 76 B2 A6 0D                                PEv²¦
                    }else{
                        $this->SendToIO(hex2bin("50447681970D"));//Auto-Übertemp.neustart aus       | 50 44 76 81 97 0D                                PDv—
                    }
                    SetValue($this->GetIDForIdent($Ident), $Value);
                    break;
                case "VarInterrupsBeeps":
                    if($Value){
                        $this->SendToIO(hex2bin("50457943490D"));//Alarmton-Versogungsquelle an     | 50 45 79 43 49 0D                                PEyCI
                    }else{
                        $this->SendToIO(hex2bin("50447970780D"));//Alarmton-Versogungsquelle aus    | 50 44 79 70 78 0D                                PDypx
                    }
                    SetValue($this->GetIDForIdent($Ident), $Value);
                    break;
                case "VarOverloadBypass":
                    if($Value){
                        $this->SendToIO(hex2bin("504562E0130D"));//Überlast-bypass an               | 50 45 62 E0 13 0D                                PEbà.
                    }else{
                        $this->SendToIO(hex2bin("504462D3220D"));//Überlast-bypass aus              | 50 44 62 D3 22 0D                                PDbÓ"
                    }
                    SetValue($this->GetIDForIdent($Ident), $Value);
                    break;
                case "VarScreenReturnsToDefault":
                    if($Value){
                        $this->SendToIO(hex2bin("50456B713A0D"));//zurück-Hauptmenü an              | 50 45 6B 71 3A 0D                                PEkq:
                    }else{
                        $this->SendToIO(hex2bin("50446B420B0D"));//zurück-Hauptmenü aus             | 50 44 6B 42 0B 0D                                PDkB.
                    }
                    SetValue($this->GetIDForIdent($Ident), $Value);
                    break;
                case "VarSolarEnergyBalance":
                    if($Value){
                        $this->SendToIO(hex2bin("5053504231E8C70D"));//Solarenergieausgleich an     | 50 53 50 42 31 E8 C7 0D                          PSPB1èÇ
                    }else{
                        $this->SendToIO(hex2bin("5053504230F8E60D"));//Solarenergieausgleich aus    | 50 53 50 42 30 F8 E6 0D                          PSPB0øæ
                    }
                    SetValue($this->GetIDForIdent($Ident), $Value);
                    break;
                case "VarFaultCodeRecord":
                    if($Value){
                        $this->SendToIO(hex2bin("50457A732A0D"));//Fault-code-record an             | 50 45 7A 73 2A 0D                                PEzs*
                    }else{
                        $this->SendToIO(hex2bin("50447A401B0D"));//Fault-code-record aus            | 50 44 7A 40 1B 0D                                PDz@.
                    }
                    SetValue($this->GetIDForIdent($Ident), $Value);
                    break;
                default:
                    throw new Exception("Invalid Ident");
            }
        }

        protected function SendToIO(string $payload)
        {
            //an Socket schicken
            $result = $this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => utf8_encode($payload))));
            return $result;
        }

        public function GetConfigurationForParent() {
            return "{\"BaudRate\": \"2400\", \"DataBits\": \"8\", \"StopBits\": \"1\", \"Parity\": \"None\"}";
        }
    }
?>
