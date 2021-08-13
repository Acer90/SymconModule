<?php

class SymconOwnTracksConfigurator extends IPSModule{
    public function Create() {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent("{435AD1C7-B416-2B64-FE5D-4BD0FB3DD385}");

    }
    public function ApplyChanges() {
        //Never delete this line!
        parent::ApplyChanges();
    }

    public function ReceiveData($JSONString) {
        parent::ReceiveData($JSONString);
        $jsonData = json_decode($JSONString, true);
        $buffer = json_decode($jsonData['Buffer'], true);

        switch($buffer['cmd']) {
            case "exportConfiguration":
                return; //run Code here
            default:
                $this->SendDebug("ReceiveData", "ACTION " . $buffer['cmd'] . " FOR THIS MODULE NOT DEFINED!", 0);
                break;
        }
    }
}

?>