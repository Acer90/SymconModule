<?php
declare(strict_types=1);

//Constants will be defined with IP-Symcon 5.0 and newer
if (!defined('IPS_KERNELMESSAGE')) {
    define('IPS_KERNELMESSAGE', 10100);
}
if (!defined('KR_READY')) {
    define('KR_READY', 10103);
}

class WebHookModule extends IPSModule
{
    private $hook = '';

    public function __construct($InstanceID, $hook)
    {
        parent::__construct($InstanceID);

        $this->hook = $hook;
    }

    public function Create()
    {

        //Never delete this line!
        parent::Create();

        //We need to call the RegisterHook function on Kernel READY
        $this->RegisterMessage(0, IPS_KERNELMESSAGE);
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {

        //Never delete this line!
        parent::MessageSink($TimeStamp, $SenderID, $Message, $Data);

        if ($Message == IPS_KERNELMESSAGE && $Data[0] == KR_READY) {
            $this->RegisterHook('/hook/' . $this->hook);
        }
    }

    public function ApplyChanges()
    {

        //Never delete this line!
        parent::ApplyChanges();

        //Only call this in READY state. On startup the WebHook instance might not be available yet
        if (IPS_GetKernelRunlevel() == KR_READY) {
            $this->RegisterHook('/hook/' . $this->hook);
        }
    }

    private function RegisterHook($WebHook)
    {
        $ids = IPS_GetInstanceListByModuleID('{015A6EB8-D6E5-4B93-B496-0D3F77AE9FE1}');
        if (count($ids) > 0) {
            $hooks = json_decode(IPS_GetProperty($ids[0], 'Hooks'), true);
            $found = false;
            foreach ($hooks as $index => $hook) {
                if ($hook['Hook'] == $WebHook) {
                    if ($hook['TargetID'] == $this->InstanceID) {
                        return;
                    }
                    $hooks[$index]['TargetID'] = $this->InstanceID;
                    $found = true;
                }
            }
            if (!$found) {
                $hooks[] = ['Hook' => $WebHook, 'TargetID' => $this->InstanceID];
            }
            IPS_SetProperty($ids[0], 'Hooks', json_encode($hooks));
            IPS_ApplyChanges($ids[0]);
        }
    }

    /**
     * This function will be called by the hook control. Visibility should be protected!
     */
    protected function ProcessHookData()
    {
        $this->SendDebug('WebHook', 'Array POST: ' . print_r($_POST, true), 0);
    }

    protected function json_encode_advanced(array $arr, $sequential_keys = false, $quotes = false, $beautiful_json = true, $decimals = 2) {

        $output = $this->isAssoc($arr) ? "{" : "[";
        $count = 0;
        foreach ($arr as $key => $value) {

            if ($this->isAssoc($arr) || (!$this->isAssoc($arr) && $sequential_keys == true )) {
                $output .= ($quotes ? '"' : '') . $key . ($quotes ? '"' : '') . ' : ';
            }

            if (is_array($value)) {
                $output .= $this->json_encode_advanced($value, $sequential_keys, $quotes, $beautiful_json);
            }
            else if (is_bool($value)) {
                $output .= ($value ? 'true' : 'false');
            }
            else if (is_numeric($value)) {
                if(is_float($value)){
                    $output .= number_format($value, $decimals, '.', '');
                }else{
                    $output .= $value;
                }
            }
            else {
                $output .= ($quotes || $beautiful_json ? '"' : '') . $value . ($quotes || $beautiful_json ? '"' : '');
            }

            if (++$count < count($arr)) {
                $output .= ', ';
            }
        }

        $output .= $this->isAssoc($arr) ? "}" : "]";

        return $output;
    }
    private function isAssoc(array $arr) {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
    protected function HexToRGB(int $Hex){
        $r   = floor($Hex/65536);
        $g  = floor(($Hex-($r*65536))/256);
        $b = $Hex-($g*256)-($r*65536);

        return array("R" => $r, "G" => $g, "B" => $b);
    }

    // -------------------------------------------------------------------------
    protected function IsCompressionAllowed($mimeType) {
        return in_array($mimeType, [
            "text/plain",
            "text/html",
            "text/xml",
            "text/css",
            "text/javascript",
            "application/xml",
            "application/xhtml+xml",
            "application/rss+xml",
            "application/json",
            "application/json; charset=utf-8",
            "application/javascript",
            "application/x-javascript",
            "image/svg+xml"
        ]);
    }

    // -------------------------------------------------------------------------
    protected function GetMimeType($extension) {
        $lines = file(IPS_GetKernelDirEx()."mime.types");
        foreach($lines as $line) {
            $type = explode("\t", $line, 2);
            if(sizeof($type) == 2) {
                $types = explode(" ", trim($type[1]));
                foreach($types as $ext) {
                    if($ext == $extension) {
                        return $type[0];
                    }
                }
            }
        }
        return "text/plain";
    }
}