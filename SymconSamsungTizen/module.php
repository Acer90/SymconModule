<?
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
            $this->RegisterPropertyInteger("Sleep", 1000);

            $this->RegisterPropertyInteger("VariableOnline", 0);
            $this->RegisterPropertyInteger("InstanceWebSocket", 0); 

            $this->RegisterPropertyInteger("CIDR", 24); 
            $this->RegisterPropertyInteger("WoLPort", 9); 

            $this->ConnectParent("{3AB77A94-3467-4E66-8A73-840B4AD89582}");  
            $this->GetConfigurationForParent();

            //event erstellen 
            $this->RegisterTimer("CheckOnline", $this->ReadPropertyInteger("Interval"), 'SamsungTizen_CheckOnline($_IPS[\'TARGET\']);');
            $this->SetStatus(102);
        }

        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();

            $this->SetStatus(102);
            $this->SetTimerInterval("CheckOnline", $this->ReadPropertyInteger("Interval")*1000);

            $this->ConnectParent("{3AB77A94-3467-4E66-8A73-840B4AD89582}"); 
            $this->GetConfigurationForParent();
        }

        public function WakeUp(){
            $addr = $this->ReadPropertyString("IPAddress");
            $mac_address = $this->ReadPropertyString("MACAddress");

            $cidr = 24;                        // MASK: 255.255.255.0 ==> 24 (3 Byte * 8 Bit)
            $port = 9;;  

            // Prepare magic packet: part 1/3 (defined constant)
            $buf="";
            for($a=0; $a<6; $a++) $buf .= chr(255); // the defined constant as represented in hexadecimal: FF FF FF FF FF FF (i.e., 6 bytes of hexadecimal FF)
            //Check whether $mac_address is valid
            $mac_address=strtoupper($mac_address);
            $mac_address=str_replace(":", "-", $mac_address);
            if ((!preg_match("/([A-F0-9]{2}[-]){5}([0-9A-F]){2}/",$mac_address)) || (strlen($mac_address) != 17))
            {
                echo $error = "Input error: Pattern of MAC-address is not \"xx-xx-xx-xx-xx-xx\" (x = digit or letter).<br>\n";
                return false; // false
            }
            else
            {
                // Prepare magic packet: part 2/3 (16 times MAC-address)
                $addr_byte = explode('-', $mac_address); // Split MAC-address into an array of (six) bytes
                $hw_addr="";
                for ($a=0; $a<6; $a++) $hw_addr .= chr(hexdec($addr_byte[$a])); // Convert MAC-address from bytes to hexadecimal to decimal
                $hw_addr_string="";
                for ($a=0; $a<16; $a++) $hw_addr_string .= $hw_addr;
                $buf .= $hw_addr_string;
            }
            // Resolve broadcast address
            if (filter_var ($addr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) // same as (but easier than):  preg_match("/\b(([01]?\d?\d|2[0-4]\d|25[0-5])\.){3}([01]?\d?\d|2[0-4]\d|25[0-5])\b/",$addr)
            {
                // $addr has an IP-adres format
            }
            else
            {
                // Whitespaces confuse name lookups
                $addr=trim($addr);
                // If you pass to gethostbyname() an:
                //	unresolvable domainname, gethostbyname() returns the domainname (rather than 'false')
                //	IP address, gethostbyname() returns that IP address.
                if (gethostbyname($addr) == $addr)
                {
                    // $addr is NOT a resolvable domainname
                    $error = "Input error: host name of broadcast address is unresolvable.<br>\n";
                    return false; // false
                }
                else
                {
                    // $addr IS a resolvable domainname
                    $addr = gethostbyname($addr);
                }
            }
            // Store input value for cookie
            $resolved_addr = $addr;
            // If $cidr is set, replace $addr for its broadcast address
            if ($cidr != "")
            {
                // Check whether $cidr is valid
                if ((!ctype_digit($cidr)) || ($cidr < 0) || ($cidr > 32))
                {
                    $error = "Input error: CIDR subnet mask is not a number within the range of 0 till 32.<br>\n";
                    return false; // false
                }
                // Convert $cidr from one decimal to one inverted binary array
                $inverted_binary_cidr="";
                for ($a=0; $a<$cidr; $a++) $inverted_binary_cidr .= "0"; // Build $inverted_binary_cidr by $cidr * zeros (this is the mask)
                $inverted_binary_cidr = $inverted_binary_cidr.substr("11111111111111111111111111111111",0,32-strlen($inverted_binary_cidr)); // Invert the mask (by postfixing ones to $inverted_binary_cidr untill 32 bits are filled/ complete)
                $inverted_binary_cidr_array = str_split($inverted_binary_cidr); // Convert $inverted_binary_cidr to an array of bits
                // Convert IP address from four decimals to one binary array
                $addr_byte = explode('.', $addr); // Split IP address into an array of (four) decimals
                $binary_addr="";
                for ($a=0; $a<4; $a++) {
                    $pre = substr("00000000",0,8-strlen(decbin($addr_byte[$a]))); // Prefix zeros
                    $post = decbin($addr_byte[$a]); // Postfix binary decimal
                    $binary_addr .= $pre.$post;
                }
                $binary_addr_array = str_split($binary_addr); // Convert $binary_addr to an array of bits
                // Perform a bitwise OR operation on arrays ($binary_addr_array & $inverted_binary_cidr_array)
                $binary_broadcast_addr_array="";
                for ($a=0; $a<32; $a++) $binary_broadcast_addr_array[$a] = ($binary_addr_array[$a] | $inverted_binary_cidr_array[$a]); // binary array of 32 bit variables ('|' = logical operator 'or')
                $binary_broadcast_addr = chunk_split(implode("", $binary_broadcast_addr_array),8,"."); // build binary address of four bundles of 8 bits (= 1 byte)
                $binary_broadcast_addr = substr($binary_broadcast_addr,0,strlen($binary_broadcast_addr)-1); // chop off last dot ('.')
                $binary_broadcast_addr_array = explode(".", $binary_broadcast_addr); // binary array of 4 byte variables
                $broadcast_addr_array="";
                for ($a=0; $a<4; $a++) $broadcast_addr_array[$a] = bindec($binary_broadcast_addr_array[$a]); // decimal array of 4 byte variables
                $addr = implode(".", $broadcast_addr_array); // broadcast address
            }
            // Check whether $port is valid
            if ((!ctype_digit($port)) || ($port < 0) || ($port > 65536))
            {
                echo $error = "Input error: Port is not a number within the range of 0 till 65536.<br>\n";
                return false; // false
            }
            // Check whether UDP is supported
            if (!array_search('udp', stream_get_transports()))
            {
                echo $error = "No magic packet can been sent, since UDP is unsupported (not a registered socket transport).<br>\n";
                return false; // false
            }
            if (function_exists('fsockopen'))
            {
                // Try fsockopen function - To do: handle error 'Permission denied'
                $socket = fsockopen("udp://" . $addr, $port, $errno, $errstr);
                if($socket)
                {
                    $socket_data = fwrite($socket, $buf);
                    if($socket_data)
                    {
                        $function = "fwrite";
                        $sent_fsockopen = "OK (fsockopen)" ;

                        fclose($socket);
                        unset($socket);
                        return $sent_fsockopen; // true
                    }
                    else
                    {
                        echo "Using 'fwrite()' failed, due to error: '".$errstr."' (".$errno.")<br>\n";
                        fclose($socket);
                        unset($socket);
                    }
                }
                else
                {
                    echo "Using 'fsockopen()' failed, due to denied permission.<br>\n";
                    unset($socket);
                }
            }
            // Try socket_create function
            if (function_exists('socket_create'))
            {
                $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP); // create socket based on IPv4, datagram and UDP
                if($socket)
                {
                    $level = SOL_SOCKET; // to enable manipulation of options at the socket level (you may have to change this to 1)
                    $optname = SO_BROADCAST; // to enable permission to transmit broadcast datagrams on the socket (you may have to change this to 6)
                    $optval = true;
                    $opt_returnvalue = socket_set_option($socket, $level, $optname, $optval);
                    if($opt_returnvalue < 0)
                    {
                        echo $error = "Using 'socket_set_option()' failed, due to error: '".socket_strerror($opt_returnvalue)."'<br>\n";
                        return false; // false
                    }
                    $flags = 0;
                    // To do: handle error 'Operation not permitted'
                    $socket_data = socket_sendto($socket, $buf, strlen($buf), $flags, $addr, $port);
                    if($socket_data)
                    {
                        $function = "socket_sendto";
                        $socket_create = "OK (create)";
                        socket_close($socket);
                        unset($socket);
                        return true; // true
                    }
                    else
                    {
                        echo $error = "Using 'socket_sendto()' failed, due to error: '".socket_strerror(socket_last_error($socket))."' (".socket_last_error($socket).")<br>\n";
                        socket_close($socket);
                        unset($socket);
                        return false; // false
                    }
                }
                else
                {
                    echo $error = "Using 'socket_create()' failed, due to error: '".socket_strerror(socket_last_error($socket))."' (".socket_last_error($socket).")<br>\n";
                    return false; // false
                }
            }
            else
            {
                $error = "No magic packet has been sent, since no functions are available to transmit it.<br>\n";
                return false; // false
            }
            
            
        }

        public function SendKeys(String $keys){
            $sleep = $this->ReadPropertyInteger("Sleep");
            $sleep = $sleep / 1000;
            if (strpos($keys, ';') !== false) {
                $keys_data = explode(";", $keys);
                foreach ($keys_data as $value) {
                    $send_str = '{"method":"ms.remote.control","params":{"Cmd":"Click","DataOfCmd":"'.$value.'","Option":"false","TypeOfRemote":"SendRemoteKey"}}';
                    $resultat = $this->SendDataToParent(json_encode(Array("DataID" => "{BC49DE11-24CA-484D-85AE-9B6F24D89321}", "FrameTyp" => 1, "Fin" => true, "Buffer" => $send_str))); 
                    sleep($sleep);
                }
            }else{
                $send_str = '{"method":"ms.remote.control","params":{"Cmd":"Click","DataOfCmd":"'.$keys.'","Option":"false","TypeOfRemote":"SendRemoteKey"}}';
                $resultat = $this->SendDataToParent(json_encode(Array("DataID" => "{BC49DE11-24CA-484D-85AE-9B6F24D89321}", "FrameTyp" => 1, "Fin" => true, "Buffer" => $send_str))); 
            }
        }

        public function CheckOnline(){
            $Intid = $this->InstanceID;
            $varonline = $this->ReadPropertyInteger("VariableOnline");
            $ipAdress = $this->ReadPropertyString("IPAddress");

            if($fp = fsockopen($ipAdress, 8001,$errCode, $errStr, 1)){   
                SetValueBoolean($varonline, true);
             } else {
                SetValueBoolean($varonline, false);
             } 
             fclose($fp);
            
        }

        public function TogglePower(){
            $Intid = $this->InstanceID;
            $varonline = $this->ReadPropertyInteger("VariableOnline");

            if($varonline == 0 || !IPS_VariableExists($varonline)) return false;

            if(GetValueBoolean($varonline) == true){
                SamsungTizen_SendKeys($Intid, 'KEY_POWER');
            }else{
                SamsungTizen_WakeUp($Intid);
            }

        }

        public function ReceiveData($JSONString) {
               $data = json_decode($JSONString);
               //IPS_LogMessage("ReceiveData", utf8_decode($data->Buffer));          
        }

        public function GetConfigurationForParent() {
            $ipAdress = $this->ReadPropertyString("IPAddress");

            $TizenAdress = "ws://".$ipAdress.":8001/api/v2/channels/samsung.remote.control";
            return "{\"URL\": \"".$TizenAdress."\"}";
        }
    }
?>
