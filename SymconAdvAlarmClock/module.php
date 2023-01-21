<?php

// Klassendefinition
class SymconAlarmClock extends IPSModule
{
    private $allowed_ObjectType = [2,3]; //Objekt-Typ (0: Kategorie, 1: Instanz, 2: Variable, 3: Skript, 4: Ereignis, 5: Media, 6: Link)
    
    public function __construct($InstanceID)
    {
        parent::__construct($InstanceID);
    }

    public function Create()
    {
        parent::Create();

        $this->RegisterPropertyInteger("Interval", 1);
        $this->RegisterPropertyInteger("IntervalBefore", 60);
        $this->RegisterPropertyInteger("ICalInstanceID", 0);
        $this->RegisterPropertyInteger("StartTimeBefore", 0);
        $this->RegisterPropertyInteger("GoogleMapsInstanceID", 0);
        $this->RegisterPropertyInteger("IntervalMaps", 120);
        $this->RegisterPropertyInteger("SafetyTime", 120);

        $this->RegisterPropertyString("OriginAddress", "Musterstraße 1, 12345 Musterstadt");
        
        $this->RegisterPropertyInteger("AlternativVariableID", 0);

        $this->RegisterPropertyString("List_Actions", "[]");
        $this->RegisterPropertyBoolean("Debug", false);

        $this->RegisterVariableInteger("NextTime", "Nächste Weckzeit", "~UnixTimestamp", 0);

        $this->RegisterVariableBoolean("Aktive", "Eingeschaltet", "~Switch", 1);
        $this->RegisterVariableInteger("Time", "Weckzeit", "~UnixTimestampTime", 2);

        $this->RegisterVariableBoolean("Day_Mo", "Montag", "~Switch", 3);
        $this->RegisterVariableBoolean("Day_Di", "Dienstag", "~Switch", 4);
        $this->RegisterVariableBoolean("Day_Mi", "Mittwoch", "~Switch", 5);
        $this->RegisterVariableBoolean("Day_Do", "Donnerstag", "~Switch", 6);
        $this->RegisterVariableBoolean("Day_Fr", "Freitag", "~Switch", 7);
        $this->RegisterVariableBoolean("Day_Sa", "Samstag", "~Switch", 8);
        $this->RegisterVariableBoolean("Day_So", "Sonntag", "~Switch", 9);

        $this->RegisterVariableBoolean("UseCalendar", "Kalendar verwenden", "~Switch", 20);
        $this->RegisterVariableBoolean("UseAlternativ", "Alternative verwenden", "~Switch", 20);

        //event erstellen
        $this->RegisterTimer("CheckStatus", $this->ReadPropertyInteger("Interval"), 'SymconAlarmClock_CheckStatus($_IPS[\'TARGET\']);');
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();

        $this->SetBuffer("runNow", 0);
        $this->SetBuffer("endTime", 0);
        $this->SetBuffer("wakeTime", 0);
        $this->SetBuffer("startTime", 0);
        $this->SetBuffer("SpeedUp", 1);

        $this->Set_DefaultAction();

        $GoogleMapsInstanceID = $this->ReadPropertyInteger("GoogleMapsInstanceID");
        if($GoogleMapsInstanceID > 0 && IPS_InstanceExists($GoogleMapsInstanceID)){
            $this->RegisterVariableString("MapsRoute", "Maps-Route", "~HTMLBox", 99);
            $this->RegisterVariableString("MapsData", "Maps-Data", "", 99);
        }

        $this->CheckStatus();
        $this->Check_ActionList();
    }

    public function GetConfigurationForm(){
        
    }

    private function Set_DefaultAction()
    {
        $actionData = array();
        $speedup = $this->GetBuffer("SpeedUp");
        $List_ActionsData = json_decode($this->ReadPropertyString("List_Actions"), true);

        foreach($List_ActionsData as $item){
            if($item["Active"] && $item["Object"] > 0 && IPS_ObjectExists($item["Object"])){
                $actionItem = array();
                $actionItem["Object"] = $item["Object"];
                $actionItem["ObjectName"] = IPS_GetLocation($item["Object"]) . "/". IPS_GetObject($item["Object"])["ObjectName"];

                $actionItem["Current"] = "";
                //$actionItem["From"] = $item["From"];
                $actionItem["Value"] = $item["Value"];

                $actionItem["StartTime"] = round($item["StartAt"] / $speedup);
                $actionItem["Duration"] = round($item["Duration"] / $speedup);
                $actionItem["Runningin"] = "";

                $actionData[] = $actionItem;
            }
        }

        $this->SetBuffer("DefaultAction", json_encode($actionData));
    }

    private function Check_ActionList(){
        $List_ActionsData = json_decode($this->ReadPropertyString("List_Actions"), true);

        foreach ($List_ActionsData as $key => $item) {
            if($item["Active"]){
                $List_ActionsData[$key]["Status"] = "OK";
                $List_ActionsData[$key]["rowColor"] = "#C0FFC0";
            }else{
                $List_ActionsData[$key]["Status"] = "Disabled";
                $List_ActionsData[$key]["rowColor"] = "#C0FFC0";
            }
        }

        //$this->SendDebug(__FUNCTION__, $List_ActionsData, 0);
        $this->UpdateFormField("List_Actions", "values", json_encode($List_ActionsData));
    }

    public function Test($speedup)
    {
        $this->SetBuffer("runNow", 1);
        $this->SetBuffer("SpeedUp", $speedup);

        $List_ActionsData = json_decode($this->ReadPropertyString("List_Actions"), true);
                $starttime = 0;
                foreach ($List_ActionsData as $item) {
                    if (is_numeric($item["StartAt"]) && $starttime > $item["StartAt"]) {
                        $starttime = $item["StartAt"];
                    }
                }

                $starttime = round($starttime / $speedup);
                if($starttime < 0) {
                    
                    $starttime = $starttime * -1;
                }else{
                    $starttime = 0;
                }

                $w_time = time()+$starttime+15;
                //echo date("d.m.y H:i:s", $w_time)." Uhr";
                $this->Set_WakeTime($w_time);

        $this->Set_DefaultAction();
        $this->SetBuffer("ActionData", $this->GetBuffer("DefaultAction"));
        $this->CheckStatus();
    }

    private function Set_WakeTime($wakeTime)
    {
        $speedup = $this->GetBuffer("SpeedUp");
        $starttime = 0;
        $endTime = 0;
        $List_ActionsData = json_decode($this->ReadPropertyString("List_Actions"), true);

        foreach ($List_ActionsData as $item) {
            if(is_numeric($item["StartAt"]) && $starttime > $item["StartAt"]){
                $starttime = $item["StartAt"];
            }

            if(is_numeric($item["StartAt"]) && $endTime < $item["StartAt"]){
                $endTime = $item["StartAt"];
            }

            if(is_numeric($item["StartAt"]) && is_numeric($item["Duration"]) && $endTime < $item["StartAt"] + $item["Duration"]){
                $endTime = $item["StartAt"] + $item["Duration"];
            }
        }

        $starttime = round($starttime / $speedup);
        $endTime = round($endTime / $speedup);

        $this->SetBuffer("startTime", $wakeTime+$starttime);
        $this->SetBuffer("wakeTime", $wakeTime);
        $this->SetBuffer("endTime", $wakeTime+$endTime);

        $this->SendDebug(__FUNCTION__, "StartTime: ". date("d.m.y H:i:s", $wakeTime+$starttime) . " | EndTime: " . date("d.m.y H:i:s",$wakeTime+$endTime), 0);
    }

    public function CheckStatus()
    {
        $actionData = json_decode($this->GetBuffer("ActionData"), true);
        $startTime = $this->GetBuffer("startTime");
        $wakeTime = $this->GetBuffer("wakeTime");
        $endTime = $this->GetBuffer("endTime");
        $runNow = boolval($this->GetBuffer("runNow"));
        $interval = $this->ReadPropertyInteger("Interval");
        $intervalBefore = $this->ReadPropertyInteger("IntervalBefore");
        $startinSeconds = $startTime - time();
        $speedup = $this->GetBuffer("SpeedUp");
        if($startinSeconds < 0) $startinSeconds = 0; 
        $w_type = "";
        $w_time = 0;

        $this->UpdateFormField("curTime", "caption", "Aktuelle Uhrzeit: " . date("j F Y, H:i") . " Uhr");
        $this->SendDebug(__FUNCTION__, "speedup:".$speedup,0); 
        //$this->SendDebug(__FUNCTION__, "StartTime: ". date("d.m.y H:i:s", $startTime-$intervalBefore) . "<=" . date("d.m.y H:i:s", time()) . " | EndTime: " . date("d.m.y H:i:s",$endTime) . ">=" . date("d.m.y H:i:s", time()), 0);
        if(is_array($actionData) && $startTime > 0 && $endTime > 0 && ($startTime-$intervalBefore) <= time() && $endTime >= time()){
            //wecker läuft
            $this->UpdateFormField("nextTime", "caption", "Nächste Weckzeit: LÄUFT!");
            $this->SetTimerInterval("CheckStatus", $interval * 1000);
            $this->SetBuffer("runNow", 0);

            foreach ($actionData as $key => $item) {
                $running_in = $wakeTime + $item["StartTime"] - time();

                if($running_in <= 0){
                    if ($item["Runningin"] == "OK") {
                        //ende der Action nichts mehr machen

                    }elseif($item["Duration"] != 0){
                        //mit duration
                        $end_in = $wakeTime + $item["StartTime"] + $item["Duration"] - time();
                        if(!isset($item["From"]) || empty($item["From"])) $item["From"] = 0;

                        if ($end_in <= 0) {
                            $this->SendDebug(__FUNCTION__, "Setze Variable zu ".$item["Object"]." => " . $item["Value"], 0);
                            RequestAction($item["Object"], $item["Value"]);

                            $actionData[$key]["Current"] = $item["Value"];
                            $actionData[$key]["Runningin"] = "OK"; //$item["Runningin"];
                            $actionData[$key]["rowColor"] = "#C0FFC0";
                        }else{
                            $diff = $item["Value"] - $item["From"];
                            $step = $diff / $item["Duration"];
                            $curTimestamp = ($item["Duration"] - $end_in);
                            $curVal = $item["From"] + ($step * $curTimestamp);

                            if(IPS_GetVariable($item["Object"])["VariableType"] == 1){
                                $curVal = round($curVal);
                            }else{
                                $curVal = round($curVal, 3);
                            }

                            RequestAction($item["Object"], $curVal);
                            $actionData[$key]["Current"] = $curVal;
                            $actionData[$key]["Runningin"] = $curTimestamp."/".$item["Duration"];
                        }
                    }else{
                        //ohne duration
                        $obj = IPS_GetObject($item["Object"]);

                        switch($obj["ObjectType"])
                        {
                            case 2: 
                                //Variable
                                $value = $item["Value"];
                                switch(IPS_GetVariable($item["Object"])["VariableType"]){
                                    case 0:
                                        //boolean
                                        $value = boolval($value);
                                        break;
                                    case 1:
                                        //Integer
                                        $value = intval($value);
                                        break;
                                    case 2:
                                        //float
                                        $value = round($value, 3);
                                        break;
                                    case 3;
                                        //string
                                        break;
                                }

                                RequestAction($item["Object"], $item["Value"]);
                                $actionData[$key]["Current"] = $item["Value"];
                                $this->SendDebug(__FUNCTION__, "Setze Variable zu ".$item["Object"]." => " . $item["Value"], 0);
                                break;
                            case 3:
                                if(!isset($item["From"])) $item["From"] = 0;

                                $data = array("WAKE_FROM" => $item["From"], "WAKE_VALUE" => $item["Value"], "WAKE_DURATION" => $item["Duration"], "WAKE_TIME" => $wakeTime);
                                IPS_RunScriptEx($item["Object"], $data);
                                $this->SendDebug(__FUNCTION__, "Run Script ".$item["Object"]." => " . json_encode($data), 0);
                                break;
                            default:
                                $this->SendDebug(__FUNCTION__, "ERROR Wrong ObjectType => " . $obj["ObjectType"], 0);
                                break;
                        }

                        $actionData[$key]["Runningin"] = "OK"; //$item["Runningin"];
                        $actionData[$key]["rowColor"] = "#C0FFC0";
                    }
                }else{
                    $actionData[$key]["Runningin"] = $running_in; 
                }
            }

            $this->SetBuffer("ActionData", json_encode($actionData));
        }else{
            //Nächste Weckzeit prüfen
            $this->SetTimerInterval("CheckStatus", $intervalBefore * 1000);
            if($runNow){
                $this->SetTimerInterval("CheckStatus", $interval * 1000);
                $w_type = "(TEST) ";
                $w_time = $wakeTime;

            }else{
                if($speedup != 1){
                    $this->SetBuffer("SpeedUp", 1);
                    $this->Set_DefaultAction();
                    $this->SetBuffer("ActionData", $this->GetBuffer("DefaultAction"));
                }
                
                $next_wakeup = 0;

                $w_time = 0;
                if($this->GetValue("Aktive")){
                    $d_str = "";

                    //nächsten aktiven tag finden
                    $WeekDay = date('w');
                    $add_days = 0;
                    for ($i = 1; $i <= 7; $i++) {
                        
                        switch($WeekDay){
                            case 1: $d_str = "Day_Mo"; break;
                            case 2: $d_str = "Day_Di"; break;
                            case 3: $d_str = "Day_Mi"; break;
                            case 4: $d_str = "Day_Do"; break;
                            case 5: $d_str = "Day_Fr"; break;
                            case 6: $d_str = "Day_Sa"; break;   
                            case 0: $d_str = "Day_So"; break;  
                            default: $d_str = ""; break;
                        }

                        //$this->SendDebug(__FUNCTION__, $WeekDay."|".$d_str . "=>" . $this->GetValue($d_str), 0);
                        if($d_str != "" && $this->GetValue($d_str)){
                            break;
                        }else{
                            $add_days++;
                        }

                        $WeekDay++;
                        if($WeekDay > 7) $WeekDay = 1;
                    }

                    $t_stamp = $this->GetValue("Time");

                    $h = $t_stamp / 3600 % 24;
                    $m = $t_stamp / 60 % 60; 

                    //$this->SendDebug(__FUNCTION__, $h .":". $m, 0);
                    
                    $oToday = new DateTime();
                    $oToday->setTimezone(new DateTimeZone('UTC'));
                    $oToday->setTime($h, $m);
                    $oToday->modify('+'.$add_days.' day');
                    $oToday->setTimezone(new DateTimeZone('Europe/Berlin'));
                    $w_time = $oToday->getTimestamp();
                    $this->SendDebug(__FUNCTION__, date("d.m.y H:i:s", $w_time) . " > " . date("d.m.y H:i:s", time()), 0);

                    if($d_str != "" && $w_time > time()){
                        $w_type = "(Wecker) ";
                        $this->Set_WakeTime($w_time);
                    }else{
                        $w_time = 0;
                    }
                }

                $alternateID = $this->ReadPropertyInteger("AlternativVariableID");
                if($this->GetValue("UseAlternativ") && $alternateID > 0 && IPS_VariableExists($alternateID)){
                    $a_time = GetValue($alternateID);

                    if($a_time > time() && ($a_time > $w_time || $w_time == 0)){
                        $w_type = "(Alexa) ";
                        $w_time = $a_time;
                        $this->Set_WakeTime($w_time);
                    }
                }

                $calendarID = $this->ReadPropertyInteger("ICalInstanceID");
                $MapsID = $this->ReadPropertyInteger("GoogleMapsInstanceID");

                if($this->GetValue("UseCalendar") && $calendarID > 0 && IPS_InstanceExists($calendarID)){
                    $nextCalendarEvent = json_decode(ICCR_GetNotifierPresenceReason($calendarID, "NOTIFIER1"), true);
                    $StartTimeBefore = $this->ReadPropertyInteger("StartTimeBefore") * 60;
                    //$this->SendDebug(__FUNCTION__, $nextCalendarEvent, 0);

                    $order   = array('\r\n', '\n', '\r');
                    $location = str_replace($order, " ", $nextCalendarEvent["Location"]);
                    $c_time = $nextCalendarEvent["From"];

                    $this->SendDebug(__FUNCTION__, date("d.m.y H:i:s", $c_time) . " > " . date("d.m.y H:i:s", time()), 0);

                    if($c_time > time() && ($c_time < $w_time || $w_time == 0)){
                        if (!empty($location) && $MapsID > 0 && IPS_InstanceExists($MapsID)) {
                            $lastMapsUpdate = intval($this->GetBuffer("LastMapsUpdate"));
                            $driveTime = intval($this->GetBuffer("DriveTime"));
                            $duration = $this->ReadPropertyInteger("IntervalMaps") * 60;
                            $SafetyTime = $this->ReadPropertyInteger("SafetyTime") * 60;
                            $OriginAddress = $this->ReadPropertyString("OriginAddress");


                            $drivetime = $this->GetBuffer("DriveTime");;

                            if(($lastMapsUpdate + $duration) <= time() || $driveTime == 0){
                                $map = [];
                                // $map['center'] = ['lng' => 11.1018, 'lat' => 47.70875];
                                $map_options = [
                                    'zoom'      => 15,
                                    'tilt'      => 0,
                                    'mapTypeId' => 'roadmap',
                                ];
                                $map['map_options'] = $map_options;

                                $infowindow_options = [
                                    'maxWidth'  => 200,
                                ];
                                $map['infowindow_options'] = $infowindow_options;

                                $map['layers'] = ['traffic'];

                                $drivingOptions = [
                                    'departureTime' => $nextCalendarEvent["From"],
                                    'trafficModel'  => 'bestguess', // bestguess, pessemistic, optimistic
                                ];
                                $transitOptions = [
                                    //'arrivalTime' => strtotime('tomorrow 12:00'),
                                    'departureTime' => $nextCalendarEvent["From"],
                                    'modes' => ['bus', 'rail', 'subway', 'train', 'tram'], // bus, rail, subway, train, tram
                                    'routingPreference'  => 'fewer_transfers', // less_walking, fewer_transfers
                                ];
                                $map['directions'] = [
                                    'origin'            => $OriginAddress, // ['lng' => 11.1018, 'lat' => 47.70875], 
                                    'destination'       => $location,
                                    'travelMode'        => 'driving',    // driving, walking, bicycling, transit, flying
                                    'drivingOptions'    => $drivingOptions, // nur, wenn travelMode == driving
                                    // 'transitOptions'    => $transitOptions, // nur wenn travelMode == transit
                                    'provideRouteAlternatives'   => false,
                                    'avoidTolls'           => false,
                                    'avoidFerries'         => false,
                                    'avoidHighways'        => false,
                                ];

                                $html = GoogleMaps_GenerateDynamicMap($MapsID, json_encode($map));
                                $this->SetValue("MapsRoute", $html);


                                $map = [];
                                // Startpunkt der Route der Karte
                                $map['origin'] = $OriginAddress;
                                // $map['origin'] = [ 'lat' => 50.685676, 'lng' => 7.157836 ];
                                $map['destination'] = $location;
                                // meiden von (optional): tolls, ferries, highways
                                //$map['avoid'] = ['ferries', 'tolls'];
                                // geplante Ankunft (optional) - Auswirkung bei der Berechnung der Reisedauer
                                $map['arrival_time'] = $nextCalendarEvent["From"];
                                // geplante Abreise (optional) - Auswirkung bei der Berechnung der Reisedauer
                                // $map['departure_time'] = trtotime('tomorrow 06:00');
                                // Berechnung der Reisedauer (optional): best_guess (default), pessimistic, optimistic
                                //$map['traffic_model'] = 'best_guess';
                                // Art der Fortbewegung (optional): driving (default), walking, bicycling, transit, flying
                                //$map['mode'] = 'driving';
                                // Verkehrsmittel (nur wenn 'mode' == 'transit'): bus, subway, train, tram, rail
                                // $map['transit_mode'] = [ 'bus', 'subway' ];
                                // Präferenz des Transfers (nur wenn 'mode' == 'transit'): less_walking, fewer_transfers
                                // $map['transit_routing_preference'] = 'fewer_transfers';

                                $r = json_decode(GoogleMaps_GetDistanceMatrix($MapsID, json_encode($map)), true);
                                $this->SendDebug(__FUNCTION__, json_encode($r), 0);
                                $this->SetValue("MapsData", json_encode($r));
                                

                                if($r["status"] == "OK"){
                                    $drivetime = round($r["rows"][0]["elements"][0]["duration"]["value"] / 60);

                                    if($drivetime > 0){
                                        $this->SendDebug(__FUNCTION__, "Drivetime = ". $drivetime . " min", 0);
                                        $this->SetBuffer("DriveTime", $drivetime);
                                    }else{
                                        $this->SetBuffer("DriveTime", 300);
                                    }
                                }
                                $this->SetBuffer("LastMapsUpdate", time());
                                
                            }else{
                                $this->SendDebug(__FUNCTION__, "Nächstes Maps Update => ".date("d.m.y H:i:s", ($lastMapsUpdate + $duration))." Uhr", 0);
                            }
                        }

                        $this->SendDebug(__FUNCTION__, "New Time: " . date("d.m.y H:i:s", $c_time) . " - (Drivetime: " . ($drivetime) . " min.) - (SaftyTime: " . (round($drivetime/60)) * ($SafetyTime/60) . " min.) - StartTimeBefore: " . ($StartTimeBefore/60) . " min."  , 0);
                        $c_time = $c_time - ($drivetime * 60) - (round($drivetime/60) * $SafetyTime) - $StartTimeBefore;

                        $w_type = "(Kalendar) ";
                        $w_time = $c_time;
                        $this->Set_WakeTime($w_time);
                    }
                }
            }

            if ($w_time == 0){
                $this->UpdateFormField("nextTime", "caption", "Nächste Weckzeit: Kein Wecker gestellt!");
            }else{
                $str = "Nächste Weckzeit: " . $w_type . date("j F Y, H:i", $w_time);
                if(!empty($startTime)) $str .= " | STime:" . date("H:i", $startTime);
                if(!empty($endTime)) $str .= " | ETime:" . date("H:i", $endTime);
                if(!empty($startinSeconds)) $str .= " (in " . $startinSeconds . " seconds)";
                

                $this->UpdateFormField("nextTime", "caption", $str);
            }

            if($this->GetValue("NextTime") != $w_time){
                $this->SetValue("NextTime", $w_time);
            }

            $this->SetBuffer("ActionData", $this->GetBuffer("DefaultAction"));
        }

        $this->Update_ActionList();
    }

    public function Update_ActionList()
    {
        $ActionData_str = $this->GetBuffer("ActionData");
        //$this->SendDebug(__FUNCTION__, $ActionData_str, 0);
        if(empty($ActionData_str)) $ActionData_str = $this->GetBuffer("DefaultAction");
        $this->UpdateFormField("List_Actions_Running", "values", $ActionData_str);
    }


    /**
     * Ergänzt SendDebug um Möglichkeit Objekte und Array auszugeben.
     *
     * @param string                                           $Message Nachricht für Data.
     * @param mixed $Data    Daten für die Ausgabe.
     *
     * @return int $Format Ausgabeformat für Strings.
     */
    protected function SendDebug($Message, $Data, $Format)
    {
        if (is_array($Data)) {
            if (count($Data) > 25) {
                $this->SendDebug($Message, array_slice($Data, 0, 20), 0);
                $this->SendDebug($Message . ':CUT', '-------------CUT-----------------', 0);
                $this->SendDebug($Message, array_slice($Data, -5, null, true), 0);
            } else {
                foreach ($Data as $Key => $DebugData) {
                    $this->SendDebug($Message . ':' . $Key, $DebugData, 0);
                }
            }
        } elseif (is_object($Data)) {
            foreach ($Data as $Key => $DebugData) {
                $this->SendDebug($Message . '->' . $Key, $DebugData, 0);
            }
        } elseif (is_bool($Data)) {
            parent::SendDebug($Message, ($Data ? 'TRUE' : 'FALSE'), 0);
        } else {
            if (IPS_GetKernelRunlevel() == KR_READY) {
                parent::SendDebug($Message, (string) $Data, $Format);
            } else {
                $this->LogMessage($Message . ':' . (string) $Data, KL_DEBUG);
            }
        }
    }
}
