<?

class deCONZLight extends IPSModule
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
    }


    protected function SendToIO(string $payload)
    {
        // send to io
        $result = $this->SendDataToParent(json_encode(Array("DataID" => "{98BD2556-6A31-5B23-48DF-D76CDF8921AB}", "Buffer" => $payload))); // Interface GUI
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
                        switch($key){
                            case "on":
                                $this->RegisterVariableBoolean($key, "State", "~Switch", 0);
                                break;
                            case "bri":
                                $this->RegisterVariableInteger($key, "Brightness", "~Intensity.255", 0);
                                break;
                            case "hue":
                                $this->RegisterVariableInteger($key, "Color", "~HexColor", 0);
                                break;
                            case "sat":
                                $this->RegisterVariableInteger($key, "Saturation", "~Intensity.255", 0);
                                break;
                            case "ct":
                                $this->RegisterVariableInteger($key, "Temperature", "~Intensity.255", 0);
                                break;
                            case "colormode":
                                $this->RegisterVariableString($key, $key, "", 0);
                                break;
                            case "alert":
                            default:
                                break;
                        }
                    }

                    $this->SetValue(($key), $item);

                }

            }
        }
    }
}