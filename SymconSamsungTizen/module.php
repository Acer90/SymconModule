<?
    include("websocket_client.php");

    
    // Klassendefinition
    class SamsungTizen extends IPSModule {
        public function __construct($InstanceID) {
            parent::__construct($InstanceID);
        }

        public function Create() {
            parent::Create();

            // Modul-Eigenschaftserstellung
            $this->RegisterPropertyString("IPAddress", "192.168.178.1"); 
            $this->RegisterPropertyString("MACAddress", "aa:bb:cc:00:11:22"); 
            $this->RegisterPropertyInteger("Interval", 10);

            $this->RegisterPropertyString("SIPAddress", "127.0.0.1");
            $this->RegisterPropertyString("SPort", "8001");
            $this->RegisterPropertyInteger("Timeout", 3);
            

            //event erstellen
            $this->RegisterTimer("CheckOnline", $this->ReadPropertyInteger("Interval"), 'CheckOnline_SyncData($_IPS[\'TARGET\'], false);');
            $this->SetStatus(102);
        }

        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();

            $this->SetStatus(102);
            $this->SetTimerInterval("CheckOnline", $this->ReadPropertyInteger("Interval")*1000);
        }

        public function WakeUp(){
            $broadcast = $this->ReadPropertyString("IPAddress");
            $mac_addr = $this->ReadPropertyString("MACAddress");
            $timeout = $this->ReadPropertyInteger("Timeout");

            if (!$fp = fsockopen('udp://' . $broadcast, 2304, $errno, $errstr, $timeout)) 
                return false; 

            $mac_hex = preg_replace('=[^a-f0-9]=i', '', $mac_addr); 

            $mac_bin = pack('H12', $mac_hex); 

            $data = str_repeat("\xFF", 6) . str_repeat($mac_bin, 16); 

            fputs($fp, $data); 
            fclose($fp); 
            return true; 

        }

        public function SendKey(string $key, $WaitforStart = false){
            $Intid = $this->InstanceID;
            $rdata = SamsungTizen_SendData($Intid, $key, $WaitforStart);

            if($rdata == "OK"){
                return true;
            }else{
                return false;
            }

            //$send_data = '{"method":"ms.remote.control","params":{"Cmd":"Click","DataOfCmd":"'.$key.'","Option":"false","TypeOfRemote":"SendRemoteKey"}}';
            // $sp = websocket_open($broadcast,8001, "/api/v2/channels/samsung.remote.control", $headers,$errstr,$timeout);
            // if($sp){
            //     if(is_null($key)) return true;
            //     sleep(0.2);
            //     echo $result = websocket_read($sp,$errstr);
            //     $output = json_decode($result, true);
            //     if ($output['event'] == 'ms.channel.connect') {
            //         sleep(0.2);
            //         $bytes_written = websocket_write($sp,$send_data, true);
            //         if(is_numeric($bytes_written)){
            //             $data = websocket_read($sp,$errstr);
            //             //echo "Server responed with: " . $errstr ? $errstr : $data;
            //             $this->SetStatus(102);
            //             return true;
            //         }else{
            //             return false;
            //         }
            //     }
            // }else{
            //     return false;
            // }
        }

        public function SendKeys($Intid, string $keys, $WaitforStart = false){
            $Intid = $this->InstanceID;
            $key_str = "";
            $first = true;
            foreach($keys as $key){
                if($first){
                    $first = false;
                    $key_str = $key;
                }else{
                    $key_str = $key_str . ";" . $key;
                }
            }

            $rdata = SamsungTizen_SendData($key_str, $WaitforStart);

            if($rdata == "OK"){
                return true;
            }else{
                return false;
            }
        }

        public function SendData(string $keys, $Wait = false){
            $ip = $this->ReadPropertyString("SIPAddress");
            $port = $this->ReadPropertyString("SPort");
            $timeout = $this->ReadPropertyInteger("Timeout");
            if(!($sock = socket_create(AF_INET, SOCK_STREAM, 0)))
            {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
                $this->SetStatus(206);
                return "ERROR";
            }

            //Connect socket to remote server
            if(!socket_connect($sock , $ip , $port))
            {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
                socket_close($sock);
                $this->SetStatus(207);
                return "ERROR";
            }
            echo $message = "WAIT=".$Wait."&KEYS=".$keys;

            if( ! socket_send ( $sock , $message , strlen($message) , 0))
            {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
                socket_close($sock);
                $this->SetStatus(208);
                return "ERROR";
            }

            //Now receive reply from server
            if(socket_recv ( $sock , $buf , 2045 , MSG_WAITALL ) === FALSE)
            {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
                socket_close($sock);
                $this->SetStatus(209);
                return "ERROR";
            }

            //print the received message

            socket_close($sock);
            return $buf;
        }

        public function CheckOnline(){
            $Intid = $this->InstanceID;
            if(@IPS_GetVariableIDByName("Online", $Intid) === False){
                $VarID = IPS_CreateVariable(0);
                IPS_SetName($VarID, "Online"); // Variable benennen
                IPS_SetParent($VarID, $Intid);
                IPS_SetVariableCustomProfile($VarID, "~Switch");
            }
            $VarID = @IPS_GetVariableIDByName("Online", $Intid);
            if($VarID !== False){
                SetValueBoolean($VarID, SamsungTizen_SendKey(null));
            }
        }
    }
?>