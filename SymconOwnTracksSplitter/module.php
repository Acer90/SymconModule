<?php
include_once (__DIR__ . '/libs/WebHookModule.php');
include_once (__DIR__ . '/libs/phpqrcode/qrlib.php');

class SymconOwnTracksSplitter extends WebHookModule {

    public function __construct($InstanceID)
    {
        parent::__construct($InstanceID, "owntracks");
    }
    public function Create() {
        //Never delete this line!
        parent::Create();

        $this->RegisterPropertyString("Password", $this->GenerateRandomPassword());

    }
    public function ApplyChanges() {
        //Never delete this line!
        parent::ApplyChanges();

        $this->SetStatus("102");
    }

    /**
     * This function will be called by the hook control. Visibility should be protected!
     */
    protected function ProcessHookData() {
        $this->SendDebug('WebHook', '$_SERVER: ' . print_r($_SERVER, true), 0);

        if ($_SERVER['SCRIPT_NAME'] === "/hook/owntracks") {
            //configurationPage Open
        } elseif (strpos($_SERVER['SCRIPT_NAME'], "/hook/owntracks/qr") !== false) {
            //show QR Code

        }  elseif (strpos($_SERVER['SCRIPT_NAME'], "/hook/owntracks/api") !== false) {
            //connection API!
            
        }

        //var_dump($_SERVER, true);

        //header("Content-type: application/json");

        $payload = file_get_contents("php://input");
        $this->SendDebug('WebHook', 'PayLoad: ' . $payload,  0);
        $data =  @json_decode($payload, true);

        if(!isset($_SERVER['PHP_AUTH_USER']))
            $_SERVER['PHP_AUTH_USER'] = "";
        if(!isset($_SERVER['PHP_AUTH_PW']))
            $_SERVER['PHP_AUTH_PW'] = "";

        if(($_SERVER['PHP_AUTH_USER'] != "Symcon") || ($_SERVER['PHP_AUTH_PW'] != "passwort")) {
            header('WWW-Authenticate: Basic Realm="Geofency WebHook"');
            header('HTTP/1.0 401 Unauthorized');
            echo "Authorization required";
            return;
        }
        echo "Willkommen im geschützten Bereich";

        //echo'<html><body><p>
//<a href="owntracks:///config?inline=ewogICJfdHlwZSI6ICJjb25maWd1cmF0aW9uIiwKICAiYXV0aCI6IHRydWUsCiAgInVzZXJuYW1lIjogImpqb2xpZSIsCiAgInBhc3N3b3JkIjogInMxa3IzdCIsCiAgImhvc3QiOiAibXlicm9rZXIuZXhhbXBsZS5vcmciLAogICJwb3J0IjogODg4Mwp9Cg==">Click to configure OwnTracks</a>
//</p></body></html>';

        /*
         {
  "_type": "cmd",
  "action": "setWaypoints",
  "waypoints": {
    "waypoints": [
      {
        "desc": "Some place",
        "rad": 8867,
        "lon": 10.428771973,
        "lat": 46.935260881,
        "tst": 1437552714,
        "_type": "waypoint"
      }
    ],
    "_type": "waypoints"
  }
}
         */
    }

    public function ForwardData($JSONString) {
        $rData = json_decode($JSONString, true);
        $jsonData = json_decode($rData['Buffer'], true);

        $this->SendDebug("ForwardData", $JSONString, 0);
    }


    private function GenerateRandomPassword(){
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    private function GetConnectAddress(){
        $connectID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}');
        if(count($connectID) == 0){} return "";
        return CC_GetUrl($connectID);
    }

    public function Test(){
        QRcode::png('PHP QR Code :)');
    }
}

?>