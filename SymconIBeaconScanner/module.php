<?
// Klassendefinition
class SymconIBeaconScanner extends IPSModule
{

    public function __construct($InstanceID)
    {
        parent::__construct($InstanceID);
    }

    public function Create()
    {
        parent::Create();

        $this->ConnectParent('{C6D2AEB3-6E1F-4B2E-8E69-3A1A00246850}');

        $this->RegisterPropertyBoolean("Debug", false);

        $this->RegisterPropertyInteger("CheckInterval", 5);
        $this->RegisterPropertyInteger("Timeout", 60);
        $this->RegisterPropertyString("IBeaconScanner_Devices", "{}");
        $this->RegisterPropertyString("IBeaconTag_Devices", "{}");


        //event erstellen
        $this->RegisterTimer("CheckStatus", $this->ReadPropertyInteger("CheckInterval"), 'SymconIBeaconScanner_CheckStatus($_IPS[\'TARGET\']);');

        $this->SetStatus(102);

        $this->SetTimerInterval("CheckStatus", $this->ReadPropertyInteger("CheckInterval") * 1000);
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();
        $this->SetTimerInterval("CheckStatus", $this->ReadPropertyInteger("CheckInterval") * 1000);
    }

    public function ReceiveData($JSONString)
    {
        $debug = $this->ReadPropertyBoolean("Debug");
        if($debug) $this->SendDebug(__FUNCTION__, $JSONString, 0);

        $beacon_items = json_decode($this->ReadPropertyString("IBeaconScanner_Devices"), true);
        $data = json_decode($JSONString);
        if (count($beacon_items) > 0) {

            if (array_search($data->Topic, array_column($beacon_items, 'name')) !== false) {
                if($debug) $this->SendDebug(__FUNCTION__, "Beacon -> " .$data->Topic. " found!", 0);
                $beacon_data = json_decode($data->Payload, true);
                if(json_last_error() != JSON_ERROR_NONE) return;
                if($debug) $this->SendDebug(__FUNCTION__, "Payload:  " .json_encode($data->Payload), 0);

                //Suchen jedes Beacons in der liste
                $beacon_tags = json_decode($this->ReadPropertyString("IBeaconTag_Devices"), true);

                foreach ($beacon_data as $key => $value){

                    $a_key = array_search($key, array_column($beacon_tags, 'mac'));
                    if($a_key !== false){
                        //beacon found
                        $user = $beacon_tags[$a_key]["name"];
                        if($debug) $this->SendDebug(__FUNCTION__, "Found => ". $key . " (" .$user. ")", 0);

                        $ident = str_replace(":", "", $key);
                        $id = @$this->GetIDForIdent($ident);
                        if($id !== false){
                            //Updaten
                            $this->SetValue($ident, true);
                            $this->SetBuffer($ident, time());
                        }else{
                            //erstellen
                            $this->RegisterVariableBoolean($ident, $user, "~Presence");
                            $this->SetValue($ident, true);
                            $this->SetBuffer($ident, time());
                        }

                        if($this->GetValue($ident) == false) $this->SendDebug("BEACON", $user . " => online" , 0);
                    }
                }

            }else{
                //$this->SendDebug(__FUNCTION__, "Beacon -> " .$data->Topic. " not in list!", 0);
                //$this->SendDebug(__FUNCTION__, json_encode(array_column($beacon_items, 'name')), 0);
            }

        }
    }

    public function CheckStatus()
    {
        $debug = $this->ReadPropertyBoolean("Debug");
        $beacon_tags = json_decode($this->ReadPropertyString("IBeaconTag_Devices"), true);
        $buffer_list = $this->GetBufferList();
        $Timeout = $this->ReadPropertyInteger("Timeout");

        foreach ($beacon_tags as $item){
            $mac = $item["mac"];
            $ident = str_replace(":", "", $mac);

            $id = @$this->GetIDForIdent($ident);
            if($id === false || $this->GetValue($ident) == false) continue;

            if(in_array($ident, $buffer_list)){
                $now = time();
                $time = (int)$this->GetBuffer($ident);
                $max = $now - $Timeout;

                if($debug) $this->SendDebug("BEACON", $item["name"] . " " . $max ." >= ".  $time , 0);

                if($max >= $time){
                    //timeout user Offline
                    $this->SetValue($ident, false);
                    $this->SendDebug("BEACON", $item["name"] . " => offline" , 0);
                }

            }else{
                //Um nach den neustart keine unegwolten ergebnisse zuerzeuegn
                $now = time();
                if($debug) $this->SendDebug(__FUNCTION__, "Update Buffer ".$ident." => " . $now, 0);
                $this->SetBuffer($ident, $now);
            }
        }
    }
}