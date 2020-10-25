<?
// Klassendefinition
class LightSceneManager extends IPSModule
{
    protected function SetBuffer($Name, $Daten)
    {
        parent::SetBuffer($Name, serialize($Daten));
    }
    protected function GetBuffer($Name)
    {
        return unserialize(parent::GetBuffer($Name));
    }

    public function __construct($InstanceID)
    {
        parent::__construct($InstanceID);
    }
    public function Create()
    {
        parent::Create();

        $this->RegisterPropertyString("SceneList", "");
        $this->UpdateMessageSink();
        $this->UpdateSceneStatus();
    }
    public function ApplyChanges() {
        $VarList = json_decode($this->ReadPropertyString("SceneList"), true);
        if(!is_array($VarList)) return;

        //Get HighID
        $id = 1;
        $changes = false;
        foreach($VarList as $item){
            if($item["ID"] > 0){
                $id = $item["ID"];
                $id++;
            }
        }

        //updates empty ID
        foreach($VarList as $key => $item){
            if($item["ID"] == 0){

                $VarList[$key]["ID"] = $id;
                $changes = true;
            }
        }

        if($changes){
            IPS_SetProperty($this->InstanceID, "SceneList", json_encode($VarList));
            IPS_ApplyChanges($this->InstanceID);
        }

        $this->UpdateMessageSink();
        $this->UpdateSceneStatus();

        $this->SendStatus();
    }

    public function UpdateSceneStatus(){
        //add default
        $sceneData = array();
        $sceneData[] = array("ID" =>  -1,"Name" => "Default", "Prio" => 0, "disable" => false, "active" => true, "Variable" => 0);

        $VarList = json_decode($this->ReadPropertyString("SceneList"), true);
        if(!is_array($VarList)) {
            $this->SetBuffer("sceneData" , json_encode($sceneData));
            $this->SendDebug("UpdateSceneStatus", json_encode($sceneData), 0);
            return;
        }

        foreach($VarList as $item) {
            if ($item["Variable"] != 0) {
                $active = false;

                if(IPS_VariableExists($item["Variable"]) && $item["disable"] == false){
                    $var = IPS_GetVariable($item["Variable"]);

                    if($var["VariableType"] == 0){
                        $active = GetValueBoolean($item["Variable"]);
                    }
                }

                $item["active"] = $active;
                $sceneData[] = $item;
            }
        }

        $this->SetBuffer("sceneData" , json_encode($sceneData));
        $this->SendDebug("UpdateSceneStatus", json_encode($sceneData), 0);
    }

    private function UpdateMessageSink(){
        $VarList = json_decode($this->ReadPropertyString("SceneList"), true);
        if(!is_array($VarList)) return;

        foreach($VarList as $item){
            if($item["Variable"] > 0){
                $this->RegisterMessage($item["Variable"], 10603);
                $this->SendDebug("UpdateMessageSink", "RegisterMessage => " . $item["Variable"] . "(10603)",0);
            }
        }
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        if ($Message == 10603)
        {
            if($Data[1] == 1){
                //$this->SendDebug("MessageSink", "Message from SenderID ".$SenderID." with Message ".$Message."\r\n Data: ".print_r($Data, true), 0);
                $VarList = json_decode($this->ReadPropertyString("SceneList"), true);

                $key = array_search($SenderID, array_column($VarList, 'Variable'));
                if($key !== FALSE && $VarList[$key]["disable"] == false){
                    $sceneData = json_decode($this->GetBuffer("sceneData"), true);
                    $key = array_search($SenderID, array_column($sceneData, 'Variable'));
                    if($key !== FALSE) {
                        $sceneData[$key]["active"] = boolval($Data[0]);
                        $this->SetBuffer("sceneData" , json_encode($sceneData));
                        $this->SendDebug("MessageSink", "Scene " . $sceneData[$key]["Name"] . " is " . json_encode($sceneData[$key]["active"]), 0);
                    }else{
                        $this->UpdateSceneStatus();
                    }

                    $this->SendStatus();
                }
            }
        }
    }

    public function SendStatus() {
        $this->SendDataToChildren(json_encode([
            'DataID' => "{27F38ACA-F68E-12E5-457A-02E308AAF91F}",
            'Buffer' => $this->GetBuffer("sceneData"),
        ]));
    }

    public function ForwardData($JSONString) {
        $data = json_decode($JSONString, true);
        $this->SendDebug("ForwardData", $data["Buffer"], 0);

        if($data["Buffer"] == "UpdateMessageSink"){
            $this->SendStatus();
        }
    }

}