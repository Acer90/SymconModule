<?

class deCONZSensor extends IPSModule
{
    // helper properties
    private $position = 0;

    public function Create()
    {
        //Never delete this line!
        parent::Create();

        //These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.

        $this->RegisterPropertyString("uniqueid", "");
        $this->ConnectParent("{8AA55C67-B28A-C67B-5332-99CCE8190ACA}"); // I/O

    }

    public function ApplyChanges()
    {
        // wait until IPS is started, dataflow does not work until stated

        //Never delete this line!
        parent::ApplyChanges();
        // check kernel ready, if not wait

        $this->ConnectParent("{8AA55C67-B28A-C67B-5332-99CCE8190ACA}");
        $this->SetStatus(102);
    }


    protected function SendToIO(string $payload)
    {
        // send to io
        $result = $this->SendDataToParent(json_encode(Array("DataID" => "{544D88E2-F711-654E-547B-6F80DA4A22B7}", "Buffer" => $payload))); // Interface GUI
        return $result;
    }

    public function ReceiveData($JSONString)
    {
        $data = json_decode($JSONString);
        $this->SendDebug("ReceiveData", utf8_decode($data->Buffer), 0);
        $data_arr = json_decode($data->Buffer, true);
        //{"e":"changed","id":"9","r":"sensors","state":{"dark":true,"daylight":false,"lastupdated":"2019-03-23T13:50:59","lightlevel":3011,"lux":2},"t":"event","uniqueid":"00:15:8d:00:02:ea:07:3d-01-0400"}
        if($data_arr["uniqueid"] == $this->ReadPropertyString("uniqueid")){
            if(array_key_exists("state", $data_arr)){
                foreach ($data_arr["state"] as $key => $item){

                    if(@$this->GetIDForIdent($key) === false){
                        switch(gettype($item)){
                            case "boolean":
                                $this->RegisterVariableBoolean($key, $key, "~Switch", 0);
                                break;
                            case "string":
                                $this->RegisterVariableString($key, $key, "", 0);
                                break;
                            case "integer":
                                $this->RegisterVariableInteger($key, $key, "", 0);
                                break;
                            case "float":
                                $this->RegisterVariableFloat($key, $key, "", 0);
                                break;
                        }
                    }

                    $this->SetValue(($key), $item);

                }

            }

            if(array_key_exists("config", $data_arr)){
                foreach ($data_arr["config"] as $key => $item) {
                    if ($this->GetIDForIdent($key) === false) {
                        switch (gettype($item)) {
                            case "boolean":
                                $this->RegisterVariableBoolean($key, $key, "~Switch", 0);
                                break;
                            case "string":
                                $this->RegisterVariableString($key, $key, "", 0);
                                break;
                            case "integer":
                                $this->RegisterVariableInteger($key, $key, "", 0);
                                break;
                            case "float":
                                $this->RegisterVariableFloat($key, $key, "", 0);
                                break;
                        }

                        switch ($key) {
                            case "on":
                            case "reachable":
                            case "battery":
                                break;
                            default:
                                $this->EnableAction($key);
                                break;
                        }

                    }

                    $this->SetValue(($key), $item);
                }
            }
        }
    }
}