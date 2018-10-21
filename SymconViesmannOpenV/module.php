<?
// Klassendefinition
class ViesmannOpenV extends IPSModule {

    public function __construct($InstanceID) {
        parent::__construct($InstanceID);
    }

    public function Create()
    {
        parent::Create();

        $this->RegisterPropertyInteger("Interval", 10);
        $this->RegisterPropertyInteger("Timeout", 10);
        $this->RegisterPropertyString("VarList", "");
        $this->RegisterPropertyBoolean("Debug", false);

        $this->ConnectParent("{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}");
        $this->GetConfigurationForParent();

        $BufferList = $this->GetBufferList();
        if(!in_array("Return", $BufferList)) $this->SetBuffer ("Return", json_encode(array()));

        //event erstellen
        $this->RegisterTimer("SyncData", $this->ReadPropertyInteger("Interval"), 'ViesmannOpenV_SyncData($_IPS[\'TARGET\']);');
    }

    public function ApplyChanges() {
        // Diese Zeile nicht löschen
        parent::ApplyChanges();

        $this->SetStatus(102);

        $this->ConnectParent("{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}");
        $this->GetConfigurationForParent();

        $BufferList = $this->GetBufferList();
        if(!in_array("Return", $BufferList)) $this->SetBuffer ("Return", json_encode(array()));

        $this->SetTimerInterval("SyncData", $this->ReadPropertyInteger("Interval")*1000);

    }

    public function Test()
    {
        //return $this->SendData($hexstamp, $bytes, $read_only, $return_data, $value);

    }

    public function SyncData()
    {
        $this->SetStatus(102);
        $VarList = $this->ReadPropertyString("VarList");
        $VarList = json_decode($VarList, true);


        foreach ($VarList as $item) {
            $name = $item["Name"];
            $typ = $item["Typ"];
            $convert = $item["Convert"];
            $length = $item["length"];
            $HexStamp = $item["HexStamp"];
            $r_only = $item["only_read"];

            if(@$this->GetIDForIdent($name) === false){

                $ObjektID = @IPS_GetObjectIDByName($name, $this->InstanceID);
                if ($ObjektID === false) {

                    switch ($typ) {
                        case "string":
                            $this->RegisterVariableString($name, $name, "");
                            if(!$r_only) $this->EnableAction($name);
                            break;
                        case "int":
                            $this->RegisterVariableInteger($name, $name, "");
                            if(!$r_only) $this->EnableAction($name);
                            break;
                        case "float":
                            $this->RegisterVariableFloat($name, $name, "");
                            if(!$r_only) $this->EnableAction($name);
                            break;
                        case "bool":
                            $this->RegisterVariableBoolean($name, $name, "");
                            if(!$r_only) $this->EnableAction($name);
                            break;
                    }
                }else{
                    IPS_SetIdent($ObjektID, $name);
                    if(!$r_only) $this->EnableAction($name);
                }
            }

            $id = IPS_GetObjectIDByIdent($name, $this->InstanceID);
            $this->SendData($HexStamp, $length, true, false, "", $id, $convert);
        }
    }

    public function ClearBuffer(){
        $BufferList = $this->GetBufferList();
        foreach($BufferList as $item){
            $this->SetBuffer ($item, "");
        }
    }

    public function SendData($hexstamp, $bytes, $read_only = true, $return_data = false, $value = "", $ips_id = 0, $convert = 0)
    {
        $hexstamp = str_replace(' ', '', $hexstamp);
        if(strlen($hexstamp) != 4) return false;
        if($bytes <= 0 || $bytes > 255)return false;

        //if($value == "0" || $value == "00"){
            //$value = "";
        //}

        $length = dechex($bytes);
        if(strlen($length) == 1) $length = "0".$length;

        $hex = "F7";
        if($read_only == false){
            //if(empty($value)) return false;
            $hex = "F4";
        }

        $hex = $hex.$hexstamp;

        $buffer_arr = array( "hex" => $hex, "length" => $length, "retrun_data" => $return_data, "value" =>  $value, "ips_id" => $ips_id, "convert" => $convert);

        $BufferList = $this->GetBufferList();
        $t = time();
        if(in_array($t, $BufferList)){
            $r =  rand ( 0 , 100000 );
            $this->SetBuffer ($t."-".$r , json_encode($buffer_arr));
        }else{
            $this->SetBuffer ($t , json_encode($buffer_arr));
        }


        if($return_data){
            $jumps = 1;
            $i = 0;
            $timeout = $this->ReadPropertyInteger("Timeout");
            $t_i = $timeout/$jumps;

            while(true){
                $r_data = json_decode($this->GetBuffer("Return"), true);
                //print_r($r_data);
                if($r_data != false) {
                    if (array_key_exists($hex, $r_data)) {
                        $r_val = $r_data[$hex]["Data"];
                        unset($r_data[$hex]);
                        $this->SetBuffer("Return", json_encode($r_data));

                        return $r_val;
                    }
                }

                $i++;
                if($i >= $t_i){
                    $this->SendDebug("ERROR", "Timeout", 0);
                    return false;
                }

                sleep($jumps);
            }
        }else{
            return true;
        }

    }

    protected function SendToIO(string $payload)
    {
        //an Socket schicken
        //$this->SendDebug("Sende Daten", bin2hex(utf8_encode($payload)), 0);
        $result = $this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => utf8_encode($payload))));
        return $result;
    }

    protected function String2Hex($string){
        $hex='';
        for ($i=0; $i < strlen($string); $i++){
            $output = dechex(ord($string[$i]));

            $zahl = strlen($output);
            if ($zahl % 2 != 0) {
                //$this->SendDebug("5.RData", "Die Zahl $zahl ist ungerade", 0);
                $output = "0".$output;
            }

            $hex .= $output;
        }


        return strtoupper($hex);
    }

    public function ReceiveData($JSONString)
    {
        $data = json_decode($JSONString);
        $data_str = $this->String2Hex(utf8_decode($data->Buffer));

        $BufferList = $this->GetBufferList();
        if(count($BufferList) <= 1){
            return;
        }

        if($this->ReadPropertyBoolean("Debug"))$this->SendDebug("GetDataFromSerial", $data_str, 0);

        sort($BufferList);
        if($this->ReadPropertyBoolean("Debug")) {
            foreach ($BufferList as $item) {
                $this->SendDebug("2.Bufferdata", $item, 0);
            }
        }

        if(in_array("Last", $BufferList)){
            $last = json_decode($this->GetBuffer("Last"), true);
            if($this->ReadPropertyBoolean("Debug"))$this->SendDebug("R-3.Last Data", $this->GetBuffer("Last"), 0);
            $len = hexdec($last["length"])*2;

            $data = "";
            if(in_array("Data", $BufferList)){
                $data = $this->GetBuffer("Data").$data_str;
            }else{
                $data = $data_str;
            }

            //Fix Send Bug
            if($len > 2 && strpos($data, '0505') !== false){
                //wert verwerfen
                $this->SendDebug("Transmit verworfen", $last["hex"], 0);
                $this->SetBuffer("Last", "");
                $this->SetBuffer("Data", "");

                //senden des nächsten Befehls
                sort($BufferList);
                $first_item = $BufferList[0];

                if($first_item != "Return" && $first_item != "Last" && $first_item != "Data"){
                    $item = json_decode($this->GetBuffer($first_item), true);

                    $this->SetBuffer($first_item, "");
                    $this->SetBuffer("Last", json_encode($item));
                    $this->SendDebug("Send Hex", "01".$item["hex"].$item["length"].$item["value"], 0);
                    $this->SendToIO(hex2bin("01".$item["hex"].$item["length"].$item["value"]));
                }
                exit;
            }

            if(substr($last["hex"], 0, 2) == "F4"){
                if($data == "00"){
                    $this->SendDebug("Send Change", "OK", 0);
                }else{
                    $this->SendDebug("Send Change", "ERROR", 0);
                }
            }

            if(strlen($data) == $len){
                if($this->ReadPropertyBoolean("Debug"))$this->SendDebug("R-4.FullReceiveData", $data, 0);

                $this->SetBuffer("Last", "");
                $this->SetBuffer("Data", "");

                //senden des nächsten Befehls
                sort($BufferList);
                $first_item = $BufferList[0];

                if($first_item != "Return" && $first_item != "Last" && $first_item != "Data"){
                    $item = json_decode($this->GetBuffer($first_item), true);

                    $this->SetBuffer($first_item, "");
                    $this->SetBuffer("Last", json_encode($item));
                    $this->SendDebug("Send Hex", $item["hex"].$item["length"].$item["value"], 0);
                    $this->SendToIO(hex2bin($item["hex"].$item["length"].$item["value"]));
                }

            }elseif (strlen($data) < $len){
                $this->SetBuffer("Data", $data);
                exit;
            }else{
                $this->SetBuffer("Last", "");
                $this->SetBuffer("Data", "");
                exit;
            }

            //umrechen zu int
            $arr = str_split($data, 2);
            $n_val = "";

            foreach($arr as $val){
                $n_val = $val.$n_val;

            }
            $data_int = hexdec($n_val);

            //ausgabe des wertes
            if($last["retrun_data"]){
                $r_data = json_decode($this->GetBuffer("Return"), true);
                if(!$r_data) $r_data = array();
                //$r_data[$last["hex"]] = array("Data" => $data_int, "Time" => time());
                $this->SetBuffer ("Return", json_encode($r_data));
                if($this->ReadPropertyBoolean("Debug"))$this->SendDebug("R-5.RData", json_encode($r_data), 0);
            }

            //Ab hier erfolgt die Verabeitung
            if(IPS_VariableExists($last["ips_id"])) {

                switch($last["convert"]){
                    case 0:
                    default:
                        break;
                    case 2:
                        $data_int = $data_int / 2;
                        break;
                    case 10:
                        $data_int = $data_int / 10;
                        break;
                    case 3600:
                        $data_int = $data_int / 3600;
                        break;
                }
                $this->SendDebug("Set-Var(".$last["ips_id"].")", $data_int, 0);
                SetValue($last["ips_id"], $data_int);

            }

            //prüfen ob weiter befehl gesendet werden soll
            $BufferList = $this->GetBufferList();
            if(count($BufferList) <= 1){
                return;
            }


        }else{
            if($data_str == "05"){
                $BufferList = $this->GetBufferList();
                sort($BufferList);
                $first_item = $BufferList[0];

                if($first_item == "Return" && $first_item == "Last" && $first_item == "Data"){
                    $this->SendDebug("Error", "First Item -> ". $first_item, 0);
                    $this->SetBuffer($first_item, "");
                    return;
                }

                $item = json_decode($this->GetBuffer($first_item), true);
                //$item = array( "hex" => $hex, "length" => $bytes, "typ" => $typ, "retrun_data" => $retrun_data, "value" =>  $value, "ips_id" => $ips_id);

                $this->SetBuffer($first_item, "");
                $this->SetBuffer("Last", json_encode($item));
                $this->SendDebug("Send Hex", "01".$item["hex"].$item["length"].$item["value"], 0);
                $this->SendToIO(hex2bin("01".$item["hex"].$item["length"].$item["value"]));

            }else{

                //$this->SendToIO(hex2bin("F7080202"));
            }
        }
    }

    public function RequestAction($Ident, $Value)
    {
        $id = $this->GetIDForIdent($Ident);
        if($id == False) exit;

        $VarList = $this->ReadPropertyString("VarList");
        $VarList = json_decode($VarList, true);

        $key = array_search($Ident, array_column($VarList, 'Name'));

        $convert = $VarList[$key]["Convert"];
        $length = $VarList[$key]["length"];
        $HexStamp = $VarList[$key]["HexStamp"];

        $var_arr = IPS_GetVariable ( $id );

        $n_val = $Value;

        switch($convert){
            case 0:
            default:
                break;
            case 2:
                $n_val = $n_val * 2;
                break;
            case 10:
                $n_val = $n_val * 10;
                break;
            case 3600:
                $n_val = $n_val * 3600;
                break;
        }

        $n_val = dechex($n_val);
        $zahl = strlen($n_val);
        if ($zahl % 2 != 0) {
            //$this->SendDebug("5.RData", "Die Zahl $zahl ist ungerade", 0);
            $n_val = "0".$n_val;
        }

        switch ($var_arr["VariableType"]) {
            case 0: //Bool
                if($Value){
                    $this->SendData($HexStamp, 1, false, false, "01", 0, 0);
                }else{
                    $this->SendData($HexStamp, 1, false, false, "", 0, 0);
                }
                break;
            case 1: //Integreter
                $this->SendData($HexStamp, 1, false, false, $n_val, 0, 0);
                break;
            case 2: //Float
                $this->SendData($HexStamp, 1, false, false, $n_val, 0, 0);
                break;
            case 3: //String
                $this->SendData($HexStamp, 1, false, false, $n_val, 0, 0);
                $this->SendToIO(hex2bin($Value));
                break;
        }
        $this->SetValue($Ident, $Value);

    }

    public function GetConfigurationForParent() {
        return "{\"BaudRate\": \"4800\", \"DataBits\": \"8\", \"StopBits\": \"2\", \"Parity\": \"Even\"}";
    }
}