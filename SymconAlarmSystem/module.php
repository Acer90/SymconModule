<?
// Klassendefinition
class SymconAlarmSystem extends IPSModule
{

    public function __construct(int $InstanceID)
    {
        parent::__construct($InstanceID);
    }

    public function Create()
    {
        parent::Create();

        $this->RegisterPropertyInteger("Interval", 10);
        $this->RegisterPropertyInteger("P_Alarm", 10);
        $this->RegisterPropertyInteger("P_Warn", 5);
        $this->RegisterPropertyString("VarList", "");
        $this->RegisterPropertyBoolean("Debug", false);

        if (!IPS_VariableProfileExists("SymconAlarmSystem_Level") || !IPS_VariableProfileExists("SymconAlarmSystem_Status")) $this->UpdateProfil();
        $this->RegisterVariableFloat("AlarmLevel", "AlarmLevel", "SymconAlarmSystem_Level", 0);
        $this->RegisterVariableFloat("AlarmStatus", "AlarmStatus", "SymconAlarmSystem_Status", 0);
        $this->RegisterVariableBoolean("AlarmON", "Eingeschaltet", "~Switch",0);
        $this->EnableAction("AlarmON");

        //$this->UpdateMessageSink();
        //$this->UpdateGroups();

        //event erstellen
        $this->RegisterTimer("UpdateData", $this->ReadPropertyInteger("Interval"), 'SymconAlarmSystem_UpdateData($_IPS[\'TARGET\']);');
    }

    public function ApplyChanges()
    {
        // Diese Zeile nicht löschen
        parent::ApplyChanges();

        $this->SetStatus(102);

        $this->SetTimerInterval("UpdateData", $this->ReadPropertyInteger("Interval") * 1000);

        $this->UpdateMessageSink();
        $this->UpdateGroups();
    }

    public function Test()
    {
        $this->UpdateMessageSink();

        //$this->UpdateData();

    }

    public function UpdateProfil()
    {
        $alarm = $this->ReadPropertyInteger("P_Alarm");
        $warn = $this->ReadPropertyInteger("P_Warn");


        if (!IPS_VariableProfileExists("SymconAlarmSystem_Level")) {
            IPS_CreateVariableProfile("SymconAlarmSystem_Level", 2);
            IPS_SetVariableProfileDigits("SymconAlarmSystem_Level", 1);
        }

        if (!IPS_VariableProfileExists("SymconAlarmSystem_Status")) {
            IPS_CreateVariableProfile("SymconAlarmSystem_Status", 2);
            IPS_SetVariableProfileDigits("SymconAlarmSystem_Status", 0);
            IPS_SetVariableProfileAssociation("SymconAlarmSystem_Status", -1, "OK", "", 0xe6e6e6);
            IPS_SetVariableProfileAssociation("SymconAlarmSystem_Status", 0, "OK", "", 0x00cc00);
            IPS_SetVariableProfileAssociation("SymconAlarmSystem_Status", $warn, "Warnung", "", 0xff9900);
            IPS_SetVariableProfileAssociation("SymconAlarmSystem_Status", $alarm, "Alarm", "", 0xff3300);
        } else {
            $p_data = IPS_GetVariableProfile("SymconAlarmSystem_Status");

            foreach ($p_data["Associations"] as $item) {
                IPS_SetVariableProfileAssociation("SymconAlarmSystem_Status", $item["Value"], "", "", 0x00cc00);
            }

            IPS_SetVariableProfileAssociation("SymconAlarmSystem_Status", 0, "OK", "", 0x00cc00);
            IPS_SetVariableProfileAssociation("SymconAlarmSystem_Status", $warn, "Warnung", "", 0xff9900);
            IPS_SetVariableProfileAssociation("SymconAlarmSystem_Status", $alarm, "Alarm", "", 0xff3300);

        }
    }

    public function UpdateMessageSink()
    {
        $update = false;
        $BufferList = $this->GetBufferList();
        if (!in_array("OldData", $BufferList)) $this->SetBuffer("OldData", $this->ReadPropertyString("VarList"));

        $old_data = json_decode($this->GetBuffer("OldData"), true);
        $new_data = json_decode($this->ReadPropertyString("VarList"), true);

        if (!isset($new_data) || empty($new_data)) return;

        foreach ($new_data as $key => $item) {
            if (empty($item["Name"]) || empty($item["Variable"]) || !IPS_VariableExists($item["Variable"])) {
                $new_data[$key]["rowColor"] = "#ff0000";
                $update = true;
                continue;
            }

            if (array_key_exists("rowColor", $item)) {
                unset($new_data[$key]["rowColor"]);
                $update = true;
            }

            $keys = array_keys(array_combine(array_keys($old_data), array_column($old_data, 'Variable')), $item["Variable"]);
            foreach ($keys as $key2 => $value) {
                unset($old_data[$value]);
            }

            $this->SendDebug("RegisterMessage", $item["Variable"], 0);
            $this->RegisterMessage($item["Variable"], 10603);

        }


        foreach ($old_data as $key => $item) {
            $this->UnregisterMessage($item["Variable"], 10603);
            $this->SendDebug("UnregisterMessage", $item["Variable"], 0);
        }


        if ($update) {
            //IPS_SetProperty($this->InstanceID, "VarList", json_encode($new_data, true));
            //IPS_ApplyChanges($this->InstanceID);
        }

        $this->SetBuffer("OldData", json_encode($new_data, true));
    }

    public function UpdateData()
    {
        if(!$this->GetValue("AlarmON")){
            return false;
        }

        $this->SetStatus(102);
        $BufferList = $this->GetBufferList();

        foreach ($BufferList as $item){
            if(!is_numeric($item) || !IPS_VariableExists($item)) continue;

            $arr = json_decode($this->GetBuffer($item),true);
            if(!is_array($arr)) continue;

            //print_r($arr);

            $running = $arr["running"];
            $time = $arr["time"];
            $op = $arr["op"];
            $points = $arr["point"];
            $cur = $arr["cur"];
            $check = $arr["check"];
            $exp = $arr["exp"];
            $Group = $arr["Group"];

            $val = GetValue($item);
            $erg = $this->Check($arr["op"],$val,$arr["check"]);
            if(!$erg){
                $last_time = $running + $time;

                if(time() > $last_time){
                    $this->SetBuffer($item,"");
                    continue;
                }

                if($exp == true){
                    $cur_sek = time() - $running;
                    $progress = $cur_sek / $time;
                    $cur = floatval($points)-(floatval($points) * $progress);

                    $arr =array("running" => $running, "time"=> $time, "point" => $points, "cur" => $cur, "exp" => $exp, "op" => $op, "check" => $check, "Group" => $Group);
                    $this->SetBuffer($item, json_encode($arr));
                }
            }else{
                $arr =array("running" => time(),"time"=> $time, "point" => $points, "cur" => $cur, "exp" => $exp, "op" => $op, "check" => $check, "Group" => $Group);
                $this->SetBuffer($item, json_encode($arr));
            }
        }

        $this->GetPoints();
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        if(!$this->GetValue("AlarmON")) return false;

        if ($Message == 10603)
        {
            if($Data[1] == 1){
                $this->SendDebug("MessageSink", "Message from SenderID ".$SenderID." with Message ".$Message."\r\n Data: ".print_r($Data, true), 0);
                $VarList = json_decode($this->ReadPropertyString("VarList"), true);


                // $Data[0] Neuer Wert
                // $Data[1] true/false ob Änderung oder Aktualisierung.
                // $Data[2] Alter Wert

                $key = array_search($SenderID, array_column($VarList, 'Variable'));

                if(!isset($key)) return false;

                $op = $VarList[$key]["Typ"];
                $points = $VarList[$key]["Points"];
                $check = $VarList[$key]["check"];
                $exp = $VarList[$key]["Exp"];
                $time = $VarList[$key]["Time"];
                $Group = $VarList[$key]["Group"];


                $erg = $this->Check($op,$Data[0],$check);
                if(!$erg) return;

                $arr =array("running" => time(),"time"=> $time, "point" => $points, "cur" => $points, "exp" => $exp, "op" => $op, "check" => $check, "Group" => $Group);
                $this->SetBuffer($SenderID, json_encode($arr));

                $this->GetPoints();
            }
        }
    }

    private function Check(string $op, string $value, string $check){
        $erg = false;

        switch($op){
            case "==":
                if($value == $check) $erg = true; else $erg = false;
                break;
            case ">":
                if($value > $check) $erg = true; else $erg = false;
                break;
            case "<":
                if($value < $check) $erg = true; else $erg = false;
                break;
            case ">=":
                if($value >= $check) $erg = true; else $erg = false;
                break;
            case "<=":
                if($value <= $check) $erg = true; else $erg = false;
                break;
            case "!=":
                if($value <= $check) $erg = true; else $erg = false;
                break;
        }

        return $erg;
    }

    private function GetPoints(){
        $BufferList = $this->GetBufferList();
        $points = 0;
        $points_arr = array();

        foreach ($BufferList as $item){
            if(!is_numeric($item) || !IPS_VariableExists($item)) continue;

            $arr = json_decode($this->GetBuffer($item),true);
            if(!is_array($arr)) continue;

            $points = $points + $arr["cur"];

            if($arr["cur"] > 0){
                if(array_key_exists($arr["Group"],$points_arr)){
                    $points_arr[$arr["Group"]] = $points_arr[$arr["Group"]] + $points;
                }else{
                    $points_arr[$arr["Group"]] = $points;
                }
            }
        }

        $this->SetValue("AlarmLevel", $points);
        $this->SetValue("AlarmStatus", $points);

        $arr_groups = json_decode($this->GetBuffer("Groups"), true);

        //print_r($arr_groups);
        if(!is_array($arr_groups)) $arr_groups = array();

        foreach($points_arr as $key => $val){

            $this->SetValue("AlarmLevel".$key, $val);
            $this->SetValue("AlarmStatus".$key, $val);

            if(array_key_exists($key, $arr_groups)) unset($arr_groups[$key]);
        }

        foreach($arr_groups as $group){
            $this->SetValue("AlarmLevel".$group, 0);
            $this->SetValue("AlarmStatus".$group, 0);
        }
    }

    private function UpdateGroups(){
        $arr_groups = array();

        $VarList = json_decode($this->ReadPropertyString("VarList"), true);
        foreach($VarList as $item){
            if($item["Group"] == 0) continue;

            if(!in_array($item["Group"], $arr_groups)) $arr_groups[$item["Group"]] = $item["Group"];

            if($this->GetIDForIdent("AlarmLevel".$item["Group"]) === false) $this->RegisterVariableFloat("AlarmLevel".$item["Group"], "AlarmLevel".$item["Group"], "SymconAlarmSystem_Level", 0);
            if($this->GetIDForIdent("AlarmStatus".$item["Group"]) === false)$this->RegisterVariableFloat("AlarmStatus".$item["Group"], "AlarmStatus".$item["Group"], "SymconAlarmSystem_Status", 0);
        }

        $this->SetBuffer("Groups", json_encode($arr_groups));
    }

    public function AlarmOn(bool $Value){
        if(!$Value) {
            $this->SetValue("AlarmLevel", 0);
            $this->SetValue("AlarmStatus", -1);

            $arr_groups = json_decode($this->GetBuffer("Groups"), true);
            foreach ($arr_groups as $group) {
                $this->SetValue("AlarmLevel" . $group, 0);
                $this->SetValue("AlarmStatus" . $group, -1);
            }
        }
        $this->SetValue("AlarmON", $Value);
    }

    public function RequestAction($Ident, $Value) {

        switch($Ident) {
            case "AlarmON":
                $this->AlarmOn($Value);
                break;
            default:
                throw new Exception("Invalid Ident");
        }

    }
}