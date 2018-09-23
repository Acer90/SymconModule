<?
// Klassendefinition
class ViesmannOpenV extends IPSModule {

    public function __construct($InstanceID) {
        parent::__construct($InstanceID);
    }

    public function Create()
    {
        parent::Create();

        $this->ConnectParent("{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}");
        $this->GetConfigurationForParent();

    }

    public function ApplyChanges() {
        // Diese Zeile nicht löschen
        parent::ApplyChanges();

        $this->SetStatus(102);

        $this->ConnectParent("{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}");
        $this->GetConfigurationForParent();

    }

    public function Test()
    {
        $this->SendToIO(hex2bin("01F423230100"));

        $BufferList = $this->GetBufferList();
        foreach($BufferList as $item){
            $this->SetBuffer ($item, "");
        }

        $test = array("Name" => "Betriebsmodus", "Hex" => "F7232301");
        $this->SetBuffer (time() , json_encode($test));
        sleep(1);
        $test = array("Name" => "1Temperatur200", "Hex" => "0800");
        $this->SetBuffer (time() , json_encode($test));
    }

    protected function SendToIO(string $payload)
    {
        //an Socket schicken
        //$this->SendDebug("Sende Daten", bin2hex(utf8_encode($payload)), 0);
        $result = $this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => utf8_encode($payload))));
        return $result;
    }

    public function ReceiveData($JSONString)
    {
        $data = json_decode($JSONString);
        $data_str = utf8_decode($data->Buffer);


        $BufferList = $this->GetBufferList();
        if(count($BufferList) == 0){
            return;
        }

        foreach($BufferList as $item){
            $this->SendDebug("Last Data", $item, 0);
        }

        if(in_array("Last", $BufferList)){
            $last = json_decode($this->GetBuffer("Last"), true);
            $this->SendDebug("Last Data", $this->GetBuffer("Last"), 0);
            $data = "";
            if(in_array("Data", $BufferList)){
                $data = $this->GetBuffer("Data") + $data_str;
            }else{
                $data = $data_str;
            }

            if(strlen($data) == $last["length"]){
                $this->SendDebug("FullReceiveData", $data, 0);
                //Ab hier erfolgt die Verabeitung
                switch($last["Name"]){

                }
                $this->SetBuffer("Last", "");
                $this->SetBuffer("Data", "");
                return;
            }elseif (strlen($data) < $last->length){
                $this->SetBuffer("Data", $data);
                retrun;
            }else{
                $this->SetBuffer("Last", "");
                $this->SetBuffer("Data", "");
                retrun;
            }


            $this->SendDebug("Last Data", strlen($data_str), 0);



        }else{
            if($data_str == hex2bin("05")){
                //$this->SendToIO(hex2bin("01F708A704"));
                $this->SendToIO(hex2bin("01F708A704"));
                $last = array("Name" => "test", "length" => 04);
                $this->SetBuffer("Last", json_encode($last));
                $this->SendDebug("ReceiveData", bin2hex($data_str), 0);

            }else{

                //$this->SendToIO(hex2bin("F7080202"));
            }
        }
    }
}