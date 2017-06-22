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
            $this->RegisterTimer("SyncData", $this->ReadPropertyInteger("Interval"), 'IPSWINSNMP_SyncData($_IPS[\'TARGET\']);');
        }

        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();
            //$this->RequireParent("{1A75660D-48AE-4B89-B351-957CAEBEF22D}");

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
            //curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$sid);                                                                 
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
                return "ERROR";
            }else{
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

        public function CamConfig(string $session = null, string $camera = null){
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

        public function ClipList(string $session = null){
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

            $data = array("cmd" => "cliplist", "session" => $session);                                                                 
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

        public function PTZ(string $session = null, string $camera = null, integer $button = null, integer $updown = null){
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
                return $output["data"];
            }else{
                return [];
            };
        }

        public function Status(string $session = null){
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
                return $output["data"];
            }else{
                return [];
            };
        }

        public function SyncData(){
            
        }
    }
?>