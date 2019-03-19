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
    }
}