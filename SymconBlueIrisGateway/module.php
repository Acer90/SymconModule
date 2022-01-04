<?php
    // Klassendefinition
    class BlueIrisGateway extends IPSModule {

        public function Create() {
            parent::Create();

            // Modul-Eigenschaftserstellung
            $this->RegisterPropertyString("IPAddress", "192.168.178.1"); 
            $this->RegisterPropertyInteger("Port", 81);
            $this->RegisterPropertyInteger("Timeout", 3);
            $this->RegisterPropertyInteger("Interval", 10);
            $this->RegisterPropertyString("Username", "admin");
            $this->RegisterPropertyString("Password", "");

            $this->RegisterPropertyBoolean("Debug", false);

            $this->SetBuffer("Session", "");

            //event erstellen
            $this->RegisterTimer("SyncData", $this->ReadPropertyInteger("Interval"), 'BlueIrisGateway_SyncData($_IPS[\'TARGET\']);');
        }

        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();
            $this->SetBuffer("Session", "");

            $this->SetStatus(102);
            $this->SetTimerInterval("SyncData", $this->ReadPropertyInteger("Interval")*1000);
        }

        public function ForwardData($JSONString) {
            $rData = json_decode($JSONString, true);
            $buffer = json_decode($rData["Buffer"], true);

            switch($buffer['cmd']) {
                case "GetLink":
                    return $this->GetLink();
                case "CamConfig":
                    return $this->CamConfig($buffer['cam'], $buffer['data']);
                default:
                    $this->SendDebug(__FUNCTION__, "ACTION " . $buffer['cmd'] . " FOR THIS MODULE NOT DEFINED!", 0);
                    break;
            }
        }

        public function SendJSONData (string $cmd, string $json_string){
            $IPAddress = $this->ReadPropertyString("IPAddress");
            $Port = $this->ReadPropertyInteger("Port");
            $Timeout = $this->ReadPropertyInteger("Timeout");

            $session = $this->Login();
            if($session == "ERROR"){
                //wrong login stop function
                $this->SendDebug(__FUNCTION__, "Wrong Login!", 0);
                return;
            }


            $jData = json_decode($json_string, true);
            $sData = array("cmd" => $cmd, "session" => $session);
            $sData = array_merge($sData, $jData);
            $json_string = json_encode($sData);
            $this->SendDebug(__FUNCTION__, $json_string, 0);

            $url = 'http://'.$IPAddress.":".$Port."/json";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$Timeout);
            curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$session);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($json_string))
            );
            $result = curl_exec($ch);

            $this->SendDebug(__FUNCTION__, $result, 0);

            if(curl_errno($ch))
            {
                if($ch == curl_errno($ch)) $this->SetStatus(204); else $this->SendDebug(__FUNCTION__, 'Curl error: ' . curl_error($ch), 0);
                return "{}";
            }

            curl_close($ch);

            return  $result;
        }

        public function Login()
        {
            $run = 0;
            $sid = $this->GetBuffer("Session");

            while (true) {
                $IPAddress = $this->ReadPropertyString("IPAddress");
                $Port = $this->ReadPropertyInteger("Port");
                $Timeout = $this->ReadPropertyInteger("Timeout");
                $Username = $this->ReadPropertyString("Username");
                $Password = $this->ReadPropertyString("Password");
                $url = 'http://' . $IPAddress . ":" . $Port . "/json";

                if (empty($sid)) {


                    $data = array("cmd" => "login");
                    $data_string = json_encode($data);

                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $Timeout);

                    $result = curl_exec($ch);

                    if (curl_errno($ch)) {
                        if ($ch == curl_errno($ch)) $this->SetStatus(204); else echo 'Curl error: ' . curl_error($ch);
                        return "ERROR";
                    }
                    curl_close($ch);

                    $output = json_decode($result, true);
                    $this->SendDebug(__FUNCTION__, $result, 0);

                    $sid = $output["session"];

                }

                $userlogin = $Username . ":" . $sid . ":" . $Password;
                $userlogin = preg_replace('/[^A-Za-z0-9. :-]/', '', $userlogin);
                $response = md5($userlogin);

                $data = array("cmd" => "login", "session" => $sid, "response" => $response);
                $data_string = json_encode($data);

                $i = 0;

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $Timeout);
                curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=' . $sid);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($data_string))
                );
                $result = curl_exec($ch);

                if (curl_errno($ch)) {
                    if ($ch == curl_errno($ch)) $this->SetStatus(204); else echo 'Curl error: ' . curl_error($ch);
                    return "ERROR";
                }

                curl_close($ch);

                $output = json_decode($result, true);

                //print_r($output);
                if ($output["result"] == "fail") {
                    $this->SetStatus(205);
                    $this->SetBuffer("Session", "");
                    $run++;

                    if($run > 5){
                        $this->SendDebug(__FUNCTION__, $result, 0);
                        return "ERROR";
                    }else{
                        $this->Logout($sid);
                        $sid = "";
                    }
                } else {
                    $this->SetStatus(102);
                    //$this->SendDebug(__FUNCTION__, $result, 0);
                    $this->SetBuffer("Session", $output["session"]);
                    return $output["session"];
                }
            }
        }
        public function Logout(string $session = null){
            if(is_null($session)){
                $this->SetStatus(203);
                return "ERROR";
            } 

            $id = $this->InstanceID;
            $IPAddress = $this->ReadPropertyString("IPAddress");
            $Port = $this->ReadPropertyInteger("Port");
            $Timeout = $this->ReadPropertyInteger("Timeout");
            $Username = $this->ReadPropertyString("Username");
            $Password = $this->ReadPropertyString("Password");

            $url = 'http://'.$IPAddress.":".$Port."/json";

            $data = array("cmd" => "logout", "session" => $session);                                                                 
            $data_string = json_encode($data);                                                                                   
                                                                                                                                
            $ch = curl_init($url);                                                                      
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$Timeout);
            curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$session);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/json',                                                                                
                'Content-Length: ' . strlen($data_string))                                                                       
            );     
            $result = curl_exec($ch);

            if(curl_errno($ch))
            {
                if($ch == curl_errno($ch)) $this->SetStatus(204); else echo 'Curl error: ' . curl_error($ch);
                return "ERROR";
            }

            curl_close($ch);

            $output = json_decode($result, true);
            $this->SendDebug(__FUNCTION__, $result, 0);
            if($output["result"] == "success"){
                return True;
            }else{
                return False;
            };
        }

        public function CamList(string $session = null){
            if(is_null($session)){
                $this->SetStatus(203);
                return "ERROR";
            }

            $id = $this->InstanceID;
            $IPAddress = $this->ReadPropertyString("IPAddress");
            $Port = $this->ReadPropertyInteger("Port");
            $Timeout = $this->ReadPropertyInteger("Timeout");
            $Username = $this->ReadPropertyString("Username");
            $Password = $this->ReadPropertyString("Password");

            $url = 'http://'.$IPAddress.":".$Port."/json";

            $data = array("cmd" => "camlist", "session" => $session);
            $data_string = json_encode($data);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$Timeout);
            curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$session);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_string))
            );
            $result = curl_exec($ch);

            if(curl_errno($ch))
            {
                if($ch == curl_errno($ch)) $this->SetStatus(204); else echo 'Curl error: ' . curl_error($ch);
                return [];
            }

            curl_close($ch);

            $output = json_decode($result, true);
            if($output["result"] == "success"){
                return $output["data"];
            }else{
                $this->SendDebug(__FUNCTION__, $result, 0);
                return [];
            };
        }
        public function SysConfig(string $session = null){
            if(is_null($session)){
                $this->SetStatus(203);
                return "ERROR";
            }

            $id = $this->InstanceID;
            $IPAddress = $this->ReadPropertyString("IPAddress");
            $Port = $this->ReadPropertyInteger("Port");
            $Timeout = $this->ReadPropertyInteger("Timeout");
            $Username = $this->ReadPropertyString("Username");
            $Password = $this->ReadPropertyString("Password");

            $url = 'http://'.$IPAddress.":".$Port."/json";

            $data = array("cmd" => "sysconfig", "session" => $session);
            $data_string = json_encode($data);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$Timeout);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_string))
            );
            $result = curl_exec($ch);

            if(curl_errno($ch))
            {
                if($ch == curl_errno($ch)) $this->SetStatus(204); else echo 'Curl error: ' . curl_error($ch);
                return "ERROR";
            }

            curl_close($ch);

            $output = json_decode($result, true);
            if($output["result"] == "success"){
                return $output["data"];
            }else{
                $this->SendDebug(__FUNCTION__, $result, 0);
                return [];
            };
        }
        public function Log(string $session = null){
            if(is_null($session)){
                $this->SetStatus(203);
                return "ERROR";
            }

            $id = $this->InstanceID;
            $IPAddress = $this->ReadPropertyString("IPAddress");
            $Port = $this->ReadPropertyInteger("Port");
            $Timeout = $this->ReadPropertyInteger("Timeout");
            $Username = $this->ReadPropertyString("Username");
            $Password = $this->ReadPropertyString("Password");

            $url = 'http://'.$IPAddress.":".$Port."/json";

            $data = array("cmd" => "log", "session" => $session);
            $data_string = json_encode($data);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$Timeout);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_string))
            );
            $result = curl_exec($ch);

            if(curl_errno($ch))
            {
                if($ch == curl_errno($ch)) $this->SetStatus(204); else echo 'Curl error: ' . curl_error($ch);
                return "ERROR";
            }

            curl_close($ch);

            $output = json_decode($result, true);
            if($output["result"] == "success"){
                return $output["data"];
            }else{
                $this->SendDebug(__FUNCTION__, $result, 0);
                return [];
            };
        }

        private function CamConfig(string $camera, array $data){
            $result = $this->SendJSONData("camconfig", json_encode($data));
            $this->SendDebug(__FUNCTION__, $result, 0);
            $output = json_decode($result, true);
            if($output["result"] == "success"){
                return json_encode($output["data"]);
            }else{
                $this->SendDebug(__FUNCTION__, $result, 0);
                return "{}";
            }
        }
        private function AlertList(string $camera, array $data){
            if(is_null($camera)) $camera = "index";
            if(is_null($startdate)) $startdate = 0;
            if(is_null($reset)) $reset = false;
            
            $id = $this->InstanceID;
            $IPAddress = $this->ReadPropertyString("IPAddress");
            $Port = $this->ReadPropertyInteger("Port");
            $Timeout = $this->ReadPropertyInteger("Timeout");
            $Username = $this->ReadPropertyString("Username");
            $Password = $this->ReadPropertyString("Password");

            $url = 'http://'.$IPAddress.":".$Port."/json";

            $data = array("cmd" => "alertlist", "session" => $session, "camera" => $camera , "startdate" => $startdate , "reset" => $reset); // , "" => $                                                          
            $data_string = json_encode($data);                                                                                   
                                                                                                                                
            $ch = curl_init($url);                                                                      
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$Timeout);
            curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$session);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/json',                                                                                
                'Content-Length: ' . strlen($data_string))                                                                       
            );     
            $result = curl_exec($ch);

            if(curl_errno($ch))
            {
                if($ch == curl_errno($ch)) $this->SetStatus(204); else echo 'Curl error: ' . curl_error($ch);
                return "ERROR";
            }

            curl_close($ch);

            $output = json_decode($result, true);
            if($output["result"] == "success"){ 
                return $output["data"];
            }else{
                $this->SendDebug(__FUNCTION__, $result, 0);
                return [];
            };
        }
        private function ClipList(String $session = null, String $camera = null, Int $startdate = null, Int $enddate = null, Bool $tiles = null){
            if(is_null($session)){
                $this->SetStatus(203);
                return "ERROR";
            } 
            if(is_null($camera)) $camera = "index";
            if(is_null($startdate)) $startdate = 0;
            if(is_null($enddate)) $enddate = time();
            if(is_null($tiles)) $tiles = false;

            $id = $this->InstanceID;
            $IPAddress = $this->ReadPropertyString("IPAddress");
            $Port = $this->ReadPropertyInteger("Port");
            $Timeout = $this->ReadPropertyInteger("Timeout");
            $Username = $this->ReadPropertyString("Username");
            $Password = $this->ReadPropertyString("Password");

            $url = 'http://'.$IPAddress.":".$Port."/json";

            $data = array("cmd" => "cliplist", "session" => $session , "camera" => $camera, "startdate" => $startdate, "enddate" => $enddate, "tiles" =>$tiles);                                                                 
            $data_string = json_encode($data);                                                                                   
                                                                                                                                
            $ch = curl_init($url);                                                                      
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$Timeout);                                                                     
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/json',                                                                                
                'Content-Length: ' . strlen($data_string))                                                                       
            );     
            $result = curl_exec($ch);

            if(curl_errno($ch))
            {
                if($ch == curl_errno($ch)) $this->SetStatus(204); else echo 'Curl error: ' . curl_error($ch);
                return "ERROR";
            }

            curl_close($ch);

            $output = json_decode($result, true);
            //$this->SendDebug("Debug:", $result, 0);
            if($output["result"] == "success" and array_key_exists("data", $output)){
                return $output["data"];
            }else{
                $this->SendDebug(__FUNCTION__, $result, 0);
                return [];
            };
        }
        private function PTZ(string $session = null,  string $camera = null, string $button = null, string $updown = null){
            if(is_null($session)){
                $this->SetStatus(203);
                return "ERROR";
            } 
            if(is_null($camera)){
                $this->SetStatus(203);
                return "ERROR";
            } 
            if(is_null($button)){
                $this->SetStatus(203);
                return "ERROR";
            } 
            if(is_null($updown)) $updown = 0;

            $id = $this->InstanceID;
            $IPAddress = $this->ReadPropertyString("IPAddress");
            $Port = $this->ReadPropertyInteger("Port");
            $Timeout = $this->ReadPropertyInteger("Timeout");
            $Username = $this->ReadPropertyString("Username");
            $Password = $this->ReadPropertyString("Password");

            $url = 'http://'.$IPAddress.":".$Port."/json";

            $data = array("cmd" => "ptz", "session" => $session, "camera" => $camera, "button" => $button,"" => $updown);                                                                 
            $data_string = json_encode($data);                                                                                   
                                                                                                                                
            $ch = curl_init($url);                                                                      
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$Timeout);                                                                     
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/json',                                                                                
                'Content-Length: ' . strlen($data_string))                                                                       
            );     
            $result = curl_exec($ch);

            if(curl_errno($ch))
            {
                if($ch == curl_errno($ch)) $this->SetStatus(204); else echo 'Curl error: ' . curl_error($ch);
                return "ERROR";
            }

            curl_close($ch);

            $output = json_decode($result, true);
            if($output["result"] == "success"){ 
                return $output;
            }else{
                $this->SendDebug(__FUNCTION__, $result, 0);
                return [];
            };
        }
        private function Status(string $session = null, string $signal = null, string $profil = null, string $dio = null, string $play = null){
            if(is_null($session)){
                $this->SetStatus(203);
                return "ERROR";
            } 

            $id = $this->InstanceID;
            $IPAddress = $this->ReadPropertyString("IPAddress");
            $Port = $this->ReadPropertyInteger("Port");
            $Timeout = $this->ReadPropertyInteger("Timeout");
            $Username = $this->ReadPropertyString("Username");
            $Password = $this->ReadPropertyString("Password");

            $url = 'http://'.$IPAddress.":".$Port."/json";

            $data = array("cmd" => "status", "session" => $session); 
            if(!is_null($signal)) $data["signal"] = $signal;
            if(!is_null($profil)) $data["profile"] = $profil;
            if(!is_null($dio)) $data["dio"] = $dio;
            if(!is_null($play)) $data["play"] = $play;

            $data_string = json_encode($data);                                                                                   
                                                                                                                                
            $ch = curl_init($url);                                                                      
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$Timeout);                                                                     
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/json',                                                                                
                'Content-Length: ' . strlen($data_string))                                                                       
            );     
            $result = curl_exec($ch);

            if(curl_errno($ch))
            {
                if($ch == curl_errno($ch)) $this->SetStatus(204); else echo 'Curl error: ' . curl_error($ch);
                return "ERROR";
            }

            curl_close($ch);

            $output = json_decode($result, true);
            if($output["result"] == "success"){ 
                return $output["data"];
            }else{
                $this->SendDebug(__FUNCTION__, $result, 0);
                return [];
            };
        }
        private function Trigger(string $session = null, string $camera = null){
            if(is_null($session)){
                $this->SetStatus(203);
                return "ERROR";
            }
            if(is_null($camera)){
                $this->SetStatus(203);
                return "ERROR";
            }

            $id = $this->InstanceID;
            $IPAddress = $this->ReadPropertyString("IPAddress");
            $Port = $this->ReadPropertyInteger("Port");
            $Timeout = $this->ReadPropertyInteger("Timeout");
            $Username = $this->ReadPropertyString("Username");
            $Password = $this->ReadPropertyString("Password");

            $url = 'http://'.$IPAddress.":".$Port."/json";

            $data = array("cmd" => "trigger", "session" => $session, "camera" => $camera);                                                                 
            $data_string = json_encode($data);                                                                                   
                                                                                                                                
            $ch = curl_init($url);                                                                      
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$Timeout);                                                                     
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/json',                                                                                
                'Content-Length: ' . strlen($data_string))                                                                       
            );     
            $result = curl_exec($ch);

            if(curl_errno($ch))
            {
                if($ch == curl_errno($ch)) $this->SetStatus(204); else echo 'Curl error: ' . curl_error($ch);
                return "ERROR";
            }

            curl_close($ch);

            $output = json_decode($result, true);
            if($output["result"] == "success"){ 
                return $output;
            }else{
                $this->SendDebug(__FUNCTION__, $result, 0);
                return [];
            };
        }

        public function SyncData(){
            $sid = $this->Login();
            if($sid == "ERROR"){
                //wrong login stop function
                $this->SendDebug(__FUNCTION__, "Wrong Login!", 0);
                return;
            }
            
            $result = $this->CamList($sid);
            $payloadConfigurator = array();
            if($this->ReadPropertyBoolean("Debug")) $this->SendDebug(__FUNCTION__, json_encode($result), 0);

            foreach ($result as $val) {
                $itemPayload = array();
                $itemPayload["name"] = $val["optionDisplay"];
                $itemPayload["shortName"] = $val["optionValue"];
                if(array_key_exists("ptz", $val)) $itemPayload["ptz"] = $val["ptz"]; else $itemPayload["ptz"] = false;

                $payloadConfigurator[] = $itemPayload;

                //daten zu den CamInstanzen senden
                $sendData = array("sname" => $val["optionValue"], "cmd" => "CamList", "payload" => $val);
                $this->SendDataToChildren(json_encode([
                    'DataID' => "{99FD6BAA-68AA-D576-8209-4E8E7A33E3E7}",
                    'Buffer' => utf8_encode(json_encode($sendData))
                ]));
            }

            //Send data to configurator
            $sendData = array("cmd" => "CamList", "payload" => $payloadConfigurator);
            $this->SendDataToChildren(json_encode([
                'DataID' => "{80FC55A5-8B0F-0642-DB3B-2CA825E3A2A3}",
                'Buffer' => utf8_encode(json_encode($sendData))
            ]));
        }
        private function GetLink(){
            $output = array();
            $output["link"] = 'http://'.$this->ReadPropertyString("IPAddress").":".$this->ReadPropertyInteger("Port");
            $output["user"] = $this->ReadPropertyString("Username");
            $output["pw"] = $this->ReadPropertyString("Password");

            return json_encode($output);
        }

        /**
         * Ergänzt SendDebug um Möglichkeit Objekte und Array auszugeben.
         * ProgrammCode bei NallChan
         *
         * @param string                                           $Message Nachricht für Data.
         * @param mixed $Data    Daten für die Ausgabe.
         *
         * @return int $Format Ausgabeformat für Strings.
         */
        protected function SendDebug($Message, $Data, $Format)
        {
            if (is_array($Data)) {
                if (count($Data) > 25) {
                    $this->SendDebug($Message, array_slice($Data, 0, 20), 0);
                    $this->SendDebug($Message . ':CUT', '-------------CUT-----------------', 0);
                    $this->SendDebug($Message, array_slice($Data, -5, null, true), 0);
                } else {
                    foreach ($Data as $Key => $DebugData) {
                        $this->SendDebug($Message . ':' . $Key, $DebugData, 0);
                    }
                }
            } elseif (is_object($Data)) {
                foreach ($Data as $Key => $DebugData) {
                    $this->SendDebug($Message . '->' . $Key, $DebugData, 0);
                }
            } elseif (is_bool($Data)) {
                parent::SendDebug($Message, ($Data ? 'TRUE' : 'FALSE'), 0);
            } else {
                if (IPS_GetKernelRunlevel() == KR_READY) {
                    parent::SendDebug($Message, (string) $Data, $Format);
                } else {
                    $this->LogMessage($Message . ':' . (string) $Data, KL_DEBUG);
                }
            }
        }
        protected function SetBuffer($Name, $Daten)
        {
            parent::SetBuffer($Name, serialize($Daten));
        }
        protected function GetBuffer($Name)
        {
            return unserialize(parent::GetBuffer($Name));
        }
    }
