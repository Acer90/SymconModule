<?php
/*
 * PHPWOL - Send wake on lan magic packet from php.
 * PHP Version 5.6.28
 * @package PHPWOL
 * @see https://github.com/andishfr/wake-on-lan.php/ GitHub project
 * @author Andreas Schaefer <asc@schaefer-it.net>
 * @copyright 2017 Andreas Schaefer
 * @license https://github.com/AndiSHFR/wake-on-lan.php/blob/master/LICENSE MIT License
 * @note This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Wake On Lan function.
 *
 * @param string      $mac         The mac address of the host to wake
 * @param string      $ip          The hostname or ip address of the host to wake
 * @param string      $cidr        The cidr of the subnet to send to the broadcast address
 * @param string      $port        The udp port to send the packet to
 *
 * @return bool|string             false  = No error occured, string = Error message
 */
function wakeOnLan($mac, $ip, $cidr, $port, &$debugOut) {
    // Initialize the result. If FALSE then everything went ok.
    $wolResult = false;
    // Initialize the debug output return
    $debugOut = [];
    // Initialize the magic packet
    $magicPacket = str_repeat(chr(0xFF), 6);

    $debugOut[] = __LINE__ . " : wakeupOnLan('$mac', '$ip', '$cidr', '$port' );";

    // Test if socket support is available
    if(!$wolResult && !extension_loaded('sockets')) {
        $wolResult = 'Error: Extension <strong>php_sockets</strong> is not loaded! You need to enable it in <strong>php.ini</strong>';
        $debugOut[] = __LINE__ . ' : ' . $wolResult;
    }

    // Test if UDP datagramm support is avalable
    if(!array_search('udp', stream_get_transports())) {
        $wolResult = 'Error: Cannot send magic packet! Tranport UDP is not supported on this system.';
        $debugOut[] = __LINE__ . ' : ' . $wolResult;
    }

    // Validate the mac address
    if(!$wolResult) {
        $debug[] = __LINE__ . ' : Validating mac address: ' . $mac;
        $mac = str_replace(':','-',strtoupper($mac));
        if ((!preg_match("/([A-F0-9]{2}[-]){5}([0-9A-F]){2}/",$mac)) || (strlen($mac) != 17)) {
            $wolResult = 'Error: Invalid MAC-address: ' . $mac;
            $debugOut[] = __LINE__ . ' : ' . $wolResult;
        }
    }

    // Finish the magic packet
    if(!$wolResult) {
        $debugOut[] = __LINE__ . ' : Creating the magic paket';
        $hwAddress = '';
        foreach( explode('-', $mac) as $addressByte) {
            $hwAddress .= chr(hexdec($addressByte));
        }
        $magicPacket .= str_repeat($hwAddress, 16);
    }

    // Resolve the hostname if not an ip address
    if(!$wolResult && !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ) {
        $debugOut[] = __LINE__ . ' : Resolving host :' . $ip;
        $tmpIp = gethostbyname($ip);
        if($ip==$tmpIp) {
            $wolResult = 'Error: Cannot resolve hostname "' . $ip . '".';
            $debugOut[] = __LINE__ . ' : ' . $wolResult;
        } else {
            $ip = $tmpIp; // Use the ip address
        }
    }

    // If $cidr is not empty we will use the broadcast address rather than the supplied ip address
    if(!$wolResult && '' != $cidr ) {
        $debugOut[] = __LINE__ . ' : CIDR is set to ' . $cidr . '. Will use broadcast address.';
        $cidr = intval($cidr);
        if($cidr < 0 || $cidr > 32) {
            $wolResult = 'Error: Invalid subnet size of ' . $cidr . '. CIDR must be between 0 and 32.';
            $debugOut[] = __LINE__ . ' : ' . $wolResult;
        } else {
            // Create the bitmask long from the cidr value
            $netMask = -1 << (32 - (int)$cidr);
            // Create the network address from the long of the ip and the network bitmask
            $networkAddress = ip2long($ip) & $netMask;
            // Calulate the size fo the network (number of ip addresses in the subnet)
            $networkSize = pow(2, (32 - $cidr));
            // Calculate the broadcast address of the network by adding the network size to the network address
            $broadcastAddress = $networkAddress + $networkSize - 1;

            $debugOut[] = __LINE__ . ' : $netMask = ' . long2ip($netMask);
            $debugOut[] = __LINE__ . ' : $networkAddress = ' . long2ip($networkAddress);
            $debugOut[] = __LINE__ . ' : $networkSize = ' . $networkSize;
            $debugOut[] = __LINE__ . ' : $broadcastAddress = ' . long2ip($broadcastAddress);

            // Create the braodcast address from the long value and use this ip
            $ip = long2ip($broadcastAddress);
        }
    }

    // Validate the udp port
    if(!$wolResult && '' != $port ) {
        $port = intval($port);
        if($port < 0 || $port > 65535 ) {
            $wolResult = 'Error: Invalid port value of ' . $port . '. Port must between 1 and 65535.';
            $debugOut[] = __LINE__ . ' : ' . $wolResult;
        }
    }

    // Can we work with fsockopen/fwrite/fclose?
    if(!$wolResult &&	function_exists('fsockopen') ) {

        $debugOut[] = __LINE__ . " : Calling fsockopen('udp://$ip', $port, ... )";

        // Open the socket
        $socket = @fsockopen('udp://' . $ip, $port, $errNo, $errStr);
        if(!$socket) {
            $wolResult = 'Error: ' . $errNo . ' - ' . $errStr;
            $debugOut[] = __LINE__ . ' : ' . $wolResult;
        } else {
            $debugOut[] = __LINE__ . ' : Sending magic paket with ' . strlen($magicPacket) . ' bytes using fwrite().';
            // Send the magic packet
            $writeResult = fwrite($socket, $magicPacket);
            if(!$writeResult) {
                $wolResult = 'Error: ' . $errNo . ' - ' . $errStr;
                $debugOut[] = __LINE__ . ' : ' . $wolResult;
            }	else {
                $debugOut[] = __LINE__ . ' : Magic packet has been send to address ' . $ip;
            }
            // Clean up the socket
            fclose($socket);
            unset($socket);
        }

    } else

        // Can we work with socket_create/socket_sendto/socket_close?
        if(!$wolResult && function_exists('socket_create') ) {

            $debug[] = __LINE__ . ' : Calling socket_create(AF_INET, SOCK_DGRAM, SOL_IDP)';
            // Create the socket
            $socket = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP); // IPv4 udp datagram socket
            if(!$socket) {
                $errno = socket_last_error();
                $wolResult = 'Error: ' . $errno . ' - ' . socket_strerror($errno);
                $debug[] = __LINE__ . ' : ' . $wolResult;
            }

            if(!$wolResult) {
                $debug[] = __LINE__ . ' : Calling socket_set_option($socket, SOL_SOCKET, SO_BROADCAST, true)';
                // Set socket options
                $socketResult = socket_set_option($socket, SOL_SOCKET, SO_BROADCAST, true);
                if(0 >= $socketResult) {
                    $wolResult = 'Error: ' . socket_strerror($socketResult);
                    $debug[] = __LINE__ . ' : ' . $wolResult;
                }
            }

            if(!$wolResult) {
                $debug[] = __LINE__ . ' : Sending magic packet using socket-sendto()...';
                //$socket_data = socket_sendto($socket, $buf, strlen($buf), $flags, $addr, $port);
                //if(!$socket_data) {
                    //$wolResult = 'Error: ' . socket_strerror($socketResult);
                    //$debug[] = __LINE__ . ' : ' . $wolResult;
                    //DbOut("A magic packet of ".$socket_data." bytes has been sent via UDP to IP address: ".$addr.":".$port.", using the '".$function."()' function.");
                //}
            }

            if($socket) {
                socket_close($socket);
                unset($socket);
            }

        } else
            if(!$wolResult) {
                $wolResult = 'Error: Cannot send magic packet. Neither fsockopen() nor'
                    . ' socket_create() is available on this system.';
                $debugOut[] = __LINE__ . ' : ' . $wolResult;
            }

    if(!$wolResult) $debugOut[] = __LINE__ . ' : Done.';

    return $wolResult;
}

function endWithJsonResponse($responseData) {

    array_walk_recursive($responseData, function(&$value, &$key) {
        if(is_string($value)) $value = utf8_encode($value);
    });

    $jsonString = json_encode($responseData, JSON_PRETTY_PRINT);

    if(!$jsonString) {
        http_response_code(500);
        die('Internal Server Error! Cannot convert response to JSON.');
    }

    header('Content-Length: ' . strlen($jsonString) );
    header('Content-Type: application/json');

    header('Expires: Mon, 26 Jul 1997 05:00:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s'));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    die($jsonString);
}



// Init locale variables
$MESSAGE = false;     // false -> all is fine, string -> error message
$DEBUGINFO = [];         // Array of strings containing debug information


// Get the url parameters
$ENABLEDEBUG = isset($_GET['debug'])   ? $_GET['debug']   : false;
$OP          = isset($_GET['op'])      ? $_GET['op']      : '';
$MAC         = isset($_GET['mac'])     ? $_GET['mac']     : '';
$IP          = isset($_GET['ip'])      ? $_GET['ip']      : '';
$CIDR        = isset($_GET['cidr'])    ? $_GET['cidr']    : '';
$PORT        = isset($_GET['port'])    ? $_GET['port']    : '';
$COMMENT     = isset($_GET['comment']) ? $_GET['comment'] : '';


// Is it a "Get host state" request?
if('info'===$OP && '' != $IP) {

    $responseData = [ 'error' => false, 'isUp' => false ];

    $errStr = false;
    $errCode = 0;
    $waitTimeoutInSeconds = 3;
    if($fp = @fsockopen($IP,3389,$errCode,$errStr,$waitTimeoutInSeconds)){
        fclose($fp);
        $responseData['isUp'] = true;
    } else {
        $responseData['isUp'] = false;
        $responseData['errCode'] = $errCode;
        $responseData['errStr'] = $errStr;
    }

    return endWithJsonResponse($responseData);
}

// Try to send the magic packet if at least a mac address and ip address was supplied
if('wol'===$OP && ''!==$MAC && '' != $IP) {

    $responseData = [ 'error' => false, 'data' => '' ];

    // Call to wake up the host
    $MESSAGE = wakeOnLan($MAC, $IP, $CIDR, $PORT, $DEBUGINFO);

    // If the request was with enabled debug mode then append the debug info to the response
    // To enable debug mode add "&debug=1" to the url
    if($ENABLEDEBUG) $responseData['DEBUG'] = $DEBUGINFO;

    // Keep the message or make it an empty string
    if(!$MESSAGE) {
        $responseData['data'] = 'Magic packet has been sent for <strong>' . $MAC. '</strong>. Please wait for the host to come up...';
    } else {
        $responseData['error'] = $MESSAGE;
    }
    return endWithJsonResponse($responseData);
}


// Try to send the magic packet if at least a mac address and ip address was supplied
if(''!==$MAC && '' != $IP) {
    // Call to wake up the host
    $MESSAGE = wakeOnLan($MAC, $IP, $CIDR, $PORT, $DEBUG);
    // Keep the message or make it an empty string
    if(!$MESSAGE) $MESSAGE = 'Magic packet has been sent for <strong>' . $MAC. '</strong>. Please wait for the host to come up...';
}

// Keep the message or make it an empty string
if(!$MESSAGE) $MESSAGE = '';

