<?
    // Klassendefinition
    class BlueIris extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }

        public function Create() {
            parent::Create();

            // Modul-Eigenschaftserstellung
            $this->RegisterPropertyString("IPAddress", "192.168.178.1"); 
            $this->RegisterPropertyInteger("Port", 81);
            $this->RegisterPropertyInteger("Timeout", 3);
            $this->RegisterPropertyInteger("Interval", 10);
            $this->RegisterPropertyString("Username", "admin");
            $this->RegisterPropertyString("Password", "");

            //event erstellen
            $this->RegisterTimer("SyncData", $this->ReadPropertyInteger("Interval"), 'BlueIris_SyncData($_IPS[\'TARGET\'], false);');
            $this->SetStatus(102);
        }

        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();
            //$this->RequireParent("{1A75660D-48AE-4B89-B351-957CAEBEF22D}");

            $this->SetStatus(102);
            $this->SetTimerInterval("SyncData", $this->ReadPropertyInteger("Interval")*1000);
        }

        public function Login(){
            $id = $this->InstanceID;
            $IPAddress = $this->ReadPropertyString("IPAddress");
            $Port = $this->ReadPropertyInteger("Port");
            $Timeout = $this->ReadPropertyInteger("Timeout");
            $Username = $this->ReadPropertyString("Username");
            $Password = $this->ReadPropertyString("Password");
            $url = 'http://'.$IPAddress.":".$Port."/json";

            $data = array("cmd" => "login");                            
            $data_string = json_encode($data);  

            $ch = curl_init($url);  
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$Timeout);   

            $result = curl_exec($ch);

            if(curl_errno($ch))
            {
                if($ch == curl_errno($ch)) $this->SetStatus(204); else echo 'Curl error: ' . curl_error($ch);
                return "ERROR";
            }
            curl_close($ch);

            $output = json_decode($result, true);

            $sid = $output["session"];

            $response = md5($Username.":".$sid.":".$Password);

            $data = array("cmd" => "login", "session" => $sid, "response" => $response);  //                                                 
            $data_string = json_encode($data);                                                                                   
                                                                                                                                
            $ch = curl_init($url);                                                                      
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$Timeout);    
            curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$sid);                                                                 
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

            //print_r($output);
            if($output["result"] == "fail"){
                $this->SetStatus(205);
                print_r($output);
                return "ERROR";
            }else{
                $this->SetStatus(102);
                return $output["session"];
            };
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
                return True;
            }else{
                return False;
            };
        }

        public function AlertList(string $session = null, string $camera = null, integer $startdate = null, bool $reset = null){
            if(is_null($session)){
                $this->SetStatus(203);
                return "ERROR";
            } 
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
                return [];
            };
        }

        public function CamConfig(string $session = null, string $camera = null, bool $reset = null, bool $enable = null, integer $pause = null, bool $motion = null, bool $schedule = null, bool $ptzcycle = null, bool $ptzevents = null, integer $alerts = null, integer $record = null){
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

            $data = array("cmd" => "camconfig", "session" => $session, "camera" => $camera); // , "" => $    
            if(!is_null($reset)) $data["reset"] = $reset;
            if(!is_null($enable)) $data["enable"] = $enable; 
            if(!is_null($pause)) $data["pause"] = $pause; 
            if(!is_null($motion)) $data["motion"] = $motion; 
            if(!is_null($schedule)) $data["schedule"] = $schedule; 
            if(!is_null($ptzcycle)) $data["ptzcycle"] = $ptzcycle;   
            if(!is_null($ptzevents)) $data["ptzevents"] = $ptzevents;  
            if(!is_null($alerts)) $data["alerts"] = $alerts;  
            if(!is_null($record)) $data["record"] = $record;                                                    
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
                return [];
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
                return [];
            };
        }

        public function ClipList(String $session = null, String $camera = null, Int $startdate = null, Int $enddate = null, Bool $tiles = null){
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
                return [];
            };
        }

        public function PTZ($session = null,  $camera = null, $button = null, $updown = null){
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
                return [];
            };
        }

        public function Status($session = null,$signal = null, $profil = null, $dio = null, $play = null){
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
                return [];
            };
        }

        public function Trigger(string $session = null, string $camera = null){
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
                return [];
            };
        }

        public function SyncData(bool $createVar = null){
            $id = $this->InstanceID;
            $IPAddress = $this->ReadPropertyString("IPAddress");
            $Port = $this->ReadPropertyInteger("Port");
            $Username = $this->ReadPropertyString("Username");
            $Password = $this->ReadPropertyString("Password");
            $sid = BlueIris_Login($id);
            if($sid == "ERROR") exit;

            if(is_null($createVar)) $createVar = false;

            $ChildrenIDs = IPS_GetChildrenIDs($id);

            //Create Checklist
            $clist = array();
            foreach ($ChildrenIDs as $val) {
                $obj = IPS_GetObject($val);

                if(IPS_InstanceExists($val) && $obj["ObjectType"] == 1){
                    $obj_conf_str = IPS_GetConfiguration($val);
                    $obj_conf = json_decode($obj_conf_str, true);

                    if(array_key_exists("ShortName", $obj_conf)){
                        $clist[$val] = IPS_GetProperty($val, "ShortName");
                    }
                }
            }
            //print_r($clist);
            
            $data = BlueIris_CamList($id, $sid);
            if($data == "ERROR") exit;
            foreach ($data as $val) {
                if(!array_key_exists("optionValue" ,$val) || strpos($val["optionValue"], 'index') !== false) continue;
                $key = array_search($val["optionValue"], $clist);
                if(in_array($val["optionValue"] , $clist)){
                    //$this->SendDataToChildren(json_encode(Array("DataID" => "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}", "CreateVar" => $createVar, "Buffer" => $data)));
                
                    if($createVar){
                        if(@IPS_GetVariableIDByName("isOnline", $key) === False){
                            $VarID = IPS_CreateVariable(0);
                            IPS_SetName($VarID, "isOnline"); // Variable benennen
                            IPS_SetParent($VarID, $key);
                            IPS_SetVariableCustomProfile($VarID, "~Switch");
                        }

                        if(@IPS_GetVariableIDByName("isRecording", $key) === False){
                            $VarID = IPS_CreateVariable(0);
                            IPS_SetName($VarID, "isRecording"); // Variable benennen
                            IPS_SetParent($VarID, $key);
                            IPS_SetVariableCustomProfile($VarID, "~Switch");
                        }

                        if(@IPS_GetVariableIDByName("isPaused", $key) === False){
                            $VarID = IPS_CreateVariable(0);
                            IPS_SetName($VarID, "isPaused"); // Variable benennen
                            IPS_SetParent($VarID, $key);
                            IPS_SetVariableCustomProfile($VarID, "~Switch");
                        }

                        if(@IPS_GetVariableIDByName("isNoSignal", $key) === False){
                            $VarID = IPS_CreateVariable(0);
                            IPS_SetName($VarID, "isNoSignal"); // Variable benennen
                            IPS_SetParent($VarID, $key);
                            IPS_SetVariableCustomProfile($VarID, "~Switch");
                        }

                        if(@IPS_GetVariableIDByName("isMotion", $key) === False){
                            $VarID = IPS_CreateVariable(0);
                            IPS_SetName($VarID, "isMotion"); // Variable benennen
                            IPS_SetParent($VarID, $key);
                            IPS_SetVariableCustomProfile($VarID, "~Switch");
                        }

                        if(@IPS_GetVariableIDByName("isTriggered", $key) === False){
                            $VarID = IPS_CreateVariable(0);
                            IPS_SetName($VarID, "isTriggered"); // Variable benennen
                            IPS_SetParent($VarID, $key);
                            IPS_SetVariableCustomProfile($VarID, "~Switch");
                        }

                        if(@IPS_GetVariableIDByName("isAlerting", $key) === False){
                            $VarID = IPS_CreateVariable(0);
                            IPS_SetName($VarID, "isAlerting"); // Variable benennen
                            IPS_SetParent($VarID, $key);
                            IPS_SetVariableCustomProfile($VarID, "~Switch");
                        }

                        if(@IPS_GetMediaIDByName("Stream", $key) === False){
                            if(!empty($Username) && !empty($Password))
                                $ImageFile = 'http://'.$IPAddress.":".$Port."/mjpg/". $val["optionValue"]. "/video.mjpg?user=".$Username."&pw=".$Password; // Image-Datei
                            else     
                                $ImageFile = 'http://'.$IPAddress.":".$Port."/mjpg/". $val["optionValue"]. "/video.mjpg";
                            $MediaID = IPS_CreateMedia(3);                  // Image im MedienPool anlegen
                            IPS_SetMediaFile($MediaID, $ImageFile, true);   // Image im MedienPool mit Image-Datei verbinden
                            IPS_SetName($MediaID, "Stream"); // Medienobjekt benennen
                            IPS_SetParent($MediaID, $key);
                        }

                        if(@IPS_GetVariableIDByName("FPS", $key) === False){
                            $VarID = IPS_CreateVariable(2);
                            IPS_SetName($VarID, "FPS"); // Variable benennen
                            IPS_SetParent($VarID, $key);
                        }
                    }

                    $VarID = @IPS_GetVariableIDByName("isOnline", $key);
                    if($VarID !== False){
                        if(!empty($val["isOnline"])) SetValueBoolean($VarID, True); else SetValueBoolean($VarID, False);
                    }

                    $VarID = @IPS_GetVariableIDByName("isPaused", $key);
                    if($VarID !== False){
                        if(!empty($val["isPaused"])) SetValueBoolean($VarID, True); else SetValueBoolean($VarID, False);
                    }

                    $VarID = @IPS_GetVariableIDByName("isNoSignal", $key);
                    if($VarID !== False){
                        if(!empty($val["isNoSignal"])) SetValueBoolean($VarID, True); else SetValueBoolean($VarID, False);
                    }

                    $VarID = @IPS_GetVariableIDByName("isAlerting", $key);
                    if($VarID !== False){
                        if(!empty($val["isAlerting"])) SetValueBoolean($VarID, True); else SetValueBoolean($VarID, False);
                    }

                    $VarID = @IPS_GetVariableIDByName("isMotion", $key);
                    if($VarID !== False){
                        if(!empty($val["isMotion"])) SetValueBoolean($VarID, True); else SetValueBoolean($VarID, False);
                    }

                    $VarID = @IPS_GetVariableIDByName("isTriggered", $key);
                    if($VarID !== False){
                        if(!empty($val["isTriggered"])) SetValueBoolean($VarID, True); else SetValueBoolean($VarID, False);
                    }

                    $VarID = @IPS_GetVariableIDByName("isRecording", $key);
                    if($VarID !== False){
                        if(!empty($val["isRecording"])) SetValueBoolean($VarID, True); else SetValueBoolean($VarID, False);
                    }

                    $VarID = @IPS_GetVariableIDByName("FPS", $key);
                    if($VarID !== False){
                        if(!empty($val["FPS"])) SetValue($VarID,$val["FPS"]); else SetValue($VarID, 0);
                    }

                    $MediaID = @IPS_GetMediaIDByName("Stream", $key);
                    if($MediaID !== False){
                        if(!empty($Username) && !empty($Password))
                            $ImageFile = 'http://'.$IPAddress.":".$Port."/mjpg/". $val["optionValue"]. "/video.mjpg?user=".$Username."&pw=".$Password; // Image-Datei
                        else     
                            $ImageFile = 'http://'.$IPAddress.":".$Port."/mjpg/". $val["optionValue"]. "/video.mjpg";
                        if(IPS_GetMedia($MediaID)["MediaFile"] != $ImageFile) {
                            
                            IPS_SetMediaFile($MediaID, $ImageFile, true);
                        }
                    }
                }else{
                    if($createVar){
                        $InsID = IPS_CreateInstance("{5308D185-A3D2-42D0-B6CE-E9D3080CE184}");
                        IPS_SetName($InsID, $val["optionDisplay"]); // Instanz benennen
                        IPS_SetParent($InsID, $id); 

                        IPS_SetProperty($InsID, "ShortName", $val["optionValue"]); // Ändere Eigenschaft "HomeCode"
                        IPS_ApplyChanges($InsID);

                        $VarID = IPS_CreateVariable(0);
                        IPS_SetName($VarID, "isOnline"); // Variable benennen
                        IPS_SetParent($VarID, $InsID);
                        IPS_SetVariableCustomProfile($VarID, "~Switch");

                        $VarID = IPS_CreateVariable(0);
                        IPS_SetName($VarID, "isPaused"); // Variable benennen
                        IPS_SetParent($VarID, $InsID);
                        IPS_SetVariableCustomProfile($VarID, "~Switch");

                        $VarID = IPS_CreateVariable(0);
                        IPS_SetName($VarID, "isNoSignal"); // Variable benennen
                        IPS_SetParent($VarID, $InsID);
                        IPS_SetVariableCustomProfile($VarID, "~Switch");

                        $VarID = IPS_CreateVariable(0);
                        IPS_SetName($VarID, "isAlerting"); // Variable benennen
                        IPS_SetParent($VarID, $InsID);
                        IPS_SetVariableCustomProfile($VarID, "~Switch");

                        $VarID = IPS_CreateVariable(0);
                        IPS_SetName($VarID, "isMotion"); // Variable benennen
                        IPS_SetParent($VarID, $InsID);
                        IPS_SetVariableCustomProfile($VarID, "~Switch");

                        $VarID = IPS_CreateVariable(0);
                        IPS_SetName($VarID, "isTriggered"); // Variable benennen
                        IPS_SetParent($VarID, $InsID);
                        IPS_SetVariableCustomProfile($VarID, "~Switch");

                        $VarID = IPS_CreateVariable(0);
                        IPS_SetName($VarID, "isRecording"); // Variable benennen
                        IPS_SetParent($VarID, $InsID);
                        IPS_SetVariableCustomProfile($VarID, "~Switch");

                        $ImageFile = 'http://'.$IPAddress.":".$Port."/mjpg/". $val["optionValue"]. "/video.mjpg";     // Image-Datei
                        $MediaID = IPS_CreateMedia(3);                  // Image im MedienPool anlegen
                        IPS_SetMediaFile($MediaID, $ImageFile, true);   // Image im MedienPool mit Image-Datei verbinden
                        IPS_SetName($MediaID, "Stream"); // Medienobjekt benennen
                        IPS_SetParent($MediaID, $InsID);

                        $VarID = IPS_CreateVariable(2);
                        IPS_SetName($VarID, "FPS"); // Variable benennen
                        IPS_SetParent($VarID, $InsID);
                    }
                }
            }

        }
    }
?>