<?
include_once (__DIR__ . '/libs/WebHookModule.php');

class SymconJSLive extends WebHookModule {

    public function __construct($InstanceID)
    {
        parent::__construct($InstanceID, "JSLive");
    }
    public function Create() {
        //Never delete this line!
        parent::Create();

        $this->RegisterPropertyString("Password", $this->GenerateRandomPassword());
        $this->RegisterPropertyInteger("WebfrontInstanceID", 0);

        $this->RegisterPropertyString("LocalAddress", "http://127.0.0.1:3777");
        $this->RegisterPropertyString("RemoteAddress", $this->GetConnectAddress());

        $this->RegisterPropertyInteger("LocalDataMode", 1);
        $this->RegisterPropertyInteger("RemoteDataMode", 0);
        $this->RegisterPropertyInteger("LocalRefreshTime", 3);
        $this->RegisterPropertyInteger("RemoteRefreshTime", 10);

        //viewport
        $this->RegisterPropertyBoolean("viewport_enable", true);
        $this->RegisterPropertyString("viewport_content", "width=device-width, initial-scale=1, maximum-scale=1.0, minimum-scale=1, user-scalable=no");

        //expert
        $this->RegisterPropertyBoolean("Debug", false);
        $this->RegisterPropertyBoolean("enableCache", false);
        $this->RegisterPropertyBoolean("enableCompression", true);

        //da das direkte reinladen ja nicht geht!
        $this->LoadConnectAddress();
        $this->SetRandomPassword();
    }
    public function ApplyChanges() {
        //Never delete this line!
        parent::ApplyChanges();
    }

    /**
     * This function will be called by the hook control. Visibility should be protected!
     */
    protected function ProcessHookData() {
        $pos = strpos($_SERVER['SCRIPT_NAME'], "/hook/JSLive/js");

        if($this->ReadPropertyBoolean("Debug"))
            $this->SendDebug('WebHook', '$_SERVER: ' . print_r($_SERVER, true), 0);

        if ($pos !== false) {
            //get javascript files load from webhook
            $subpath = substr($_SERVER['SCRIPT_NAME'], strlen("/hook/JSLive/"));
            $path = __DIR__ . "/" . $subpath;

            if($this->ReadPropertyBoolean("Debug"))
                $this->SendDebug('WebHook', 'JS PATH =>' . $path, 0);

            if (!file_exists($path)) {
                header("HTTP/1.0 404 Not Found");
                echo "404 file not found!";
                return;
            }


            header("HTTP/1.0 200 X");
            //http_response_code(200);
            $path_parts = pathinfo($path);
            if(strtolower($path_parts["extension"]) == "css"){
                header("Content-Type: text/css");
            }else{
                header("Content-Type: text/html");
            }

            if($this->ReadPropertyBoolean("enableCache")) {
                //Add caching support
                $etag = md5_file($path);
                header("ETag: " . $etag);
                if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && (trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag)) {
                    http_response_code(304);
                    return;
                }
            }

            if($this->ReadPropertyBoolean("enableCompression")) {
                $compressed = gzencode(file_get_contents($path));
                header("Content-Encoding: gzip");
                header("Content-Length: " . strlen($compressed));
                echo $compressed;
            }else{
                echo file_get_contents($path);
            }
        }else{
            //Daten vom Modul Laden
            $Type = substr($_SERVER['SCRIPT_NAME'], strlen("/hook/JSLive/"));
            if(empty($Type)) $Type = "getContend";

            $queryData = array();
            foreach (explode("&", $_SERVER['QUERY_STRING']) as $item) {
                $pos2 = strpos($item, '=');
                if ($pos2 !== false) {
                    $p_arr = explode("=", $item);
                    if (count($p_arr) > 2) continue;
                    $queryData[strtolower($p_arr[0])] = $p_arr[1];
                }
            }

            if (!key_exists("instance", $queryData)) {
                $this->SendDebug('WebHook', 'INSTANCE NOT SET!', 0);
                return ""; //wenn instance Parameter nicht gefunden
            }

            //password prüfen!
            if(!empty($this->ReadPropertyString("Password"))){
                $password = "";
                if (key_exists("pw", $queryData)) {
                    $password = $queryData["pw"];
                }
                if($this->ReadPropertyString("Password") != $password){
                    $this->SendDebug("WebHook", "WRONG PASSWORD!", 0);
                    echo "";
                    return;
                }
            }


            //$this->SendDebug('WebHook', 'INSTANCE:'. $queryData["instance"], 0);
            //$this->SendDebug('WebHook', 'Array QUERY_STRING: ' . print_r($queryData, true), 0);
            //$this->SendDebug('WebHook', 'Array Server: ' . print_r($_SERVER, true), 0);

            header("HTTP/1.0 200 X");

            $sendData = array("cmd" => $Type, "instance" => $queryData["instance"], "queryData" => $queryData);
            $contend = $this->SendDataToChildren(json_encode([
                'DataID' => "{79D59629-E9C5-44F1-0F34-0FBC5C88F307}",
                'Buffer' => utf8_encode(json_encode($sendData))
            ]));

            if (count($contend) == 0){
                $this->SendDebug("WebHook", "NO INSTANCE FOUND!", 0);
                $this->SendDebug("WebHook", print_r($contend, true), 0);
                header("Content-Type: text/html");
                return "NO INSTANCE FOUND!"; //wenn instance nicht gefunden
            }

            if(strtolower($Type) == "getsvg"){
                header("Content-type: image/svg+xml");
                //header("Content-Type: text/html");
            }elseif (strtolower($Type) == "exportconfiguration") {
                header('Content-disposition: attachment; filename=' . $queryData["instance"] . '-' . IPS_GetInstance($queryData["instance"])["ModuleInfo"]["ModuleName"] . '.json');
                header('Content-type: application/json');
            }elseif (strtolower($Type) == "loadfile"){
                //Here Do Nothing
            }else{
                header("Content-Type: text/html");
            }

            if(strtolower($Type) == "getcontend") {
                //check if local
                $isLocal = $this->CheckIfLocal($_SERVER["HTTP_HOST"]);

                $arr_data = json_decode($contend[0], true);

                $viewport = false;
                if ($this->ReadPropertyBoolean("viewport_enable") && $arr_data["viewport"]) $viewport = true;

                $contend = $this->ReplacePlaceholder($arr_data["output"], $isLocal, $queryData["instance"], $_SERVER, $viewport);
            }elseif(strtolower($Type) == "loadfile"){
                $arr_data = json_decode($contend[0], true);
                if(strtolower($arr_data["Type"]) == "css"){
                    header('Content-type: text/css');
                }else{
                    header("Content-type: text/javascript");
                }
                $contend = $arr_data["Contend"];
            }else{
                $contend = $contend[0];
            }


            /*if($this->ReadPropertyBoolean("enableCache")) {
                //Add caching support
                $etag = md5($contend);
                header("ETag: " . $etag);
                if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && (trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag)) {
                    http_response_code(304);
                    return;
                }
            }*/

            if($this->ReadPropertyBoolean("Debug")) $this->SendDebug("WebHook", $contend, 0);

            if($this->ReadPropertyBoolean("enableCompression")) {
                $compressed = gzencode($contend);
                header("Content-Encoding: gzip");
                header("Content-Length: " . strlen($compressed));
                echo $compressed;
            }else {
                echo $contend;
            }
        }
    }

    public function ForwardData($JSONString) {
        $rData = json_decode($JSONString, true);
        $jsonData = json_decode($rData['Buffer'], true);

        $this->SendDebug("ForwardData", $JSONString, 0);

        switch($jsonData["Type"]){
            case "GetLink":
                $intId = $jsonData["InstanceID"];

                $remote = $this->ReadPropertyString("RemoteAddress");
                $local = $this->ReadPropertyString("LocalAddress");
                $pw = $this->ReadPropertyString("Password");

                $link = $remote;
                if($jsonData["local"] == true){
                    $link = $local;
                }

                if(empty($link)){
                    $link = $local;
                }

                if(empty($link)){
                    return "No Address in Main modul Set!";
                }


                if(empty($pw)){
                    return $link . "/hook/JSLive?Instance=".$intId;
                }else{
                    return $link . "/hook/JSLive?Instance=".$intId."&pw=".$pw;
                }
            case "GetConfigurationLink":
                $intId = $jsonData["InstanceID"];

                $local = $this->ReadPropertyString("LocalAddress");
                $pw = $this->ReadPropertyString("Password");

                $link = $local;

                if(empty($link)){
                    return "No Address in Main modul Set!";
                }

                if(empty($pw)){
                    return $link . "/hook/JSLive/exportConfiguration?Instance=".$intId;
                }else{
                    return $link . "/hook/JSLive/exportConfiguration?Instance=".$intId."&pw=".$pw;
                }
        }


    }

    private function CheckIfLocal(string $address){
        if(empty($this->ReadPropertyString("RemoteAddress"))) return true;

        $r_address = parse_url($this->ReadPropertyString("RemoteAddress"));

        $chk_address = $r_address["host"];
        if(array_key_exists("port", $r_address)){
            $chk_address = $r_address["host"]. ":". $r_address["port"];
        }

        //$this->SendDebug("CheckIfLocal", $address. "|". $chk_address, 0);
        if($address == $chk_address) return false;

        return true;
    }
    private function ReplacePlaceholder(string $htmlData, bool $isLocal, int $IntID, array $server, bool $viewport){
        $webfrontid = $this->ReadPropertyInteger("WebfrontInstanceID");
        $address = $this->ReadPropertyString("RemoteAddress");

        if($isLocal || empty($address)){
            $address = $this->ReadPropertyString("LocalAddress");
        }
        $wsaddress = $this->GetWebsocket($server, $webfrontid);

        $htmlData = str_replace("{GLOBAL}",  $this->json_encode_advanced($this->GetConfigurationData()), $htmlData);
        $htmlData = str_replace("{ADDRESS}", $address, $htmlData);
        $htmlData = str_replace("{WSADDRESS}", $wsaddress , $htmlData);
        $htmlData = str_replace("{REMOTE_ADDRESS}", $this->ReadPropertyString("RemoteAddress"), $htmlData);
        $htmlData = str_replace("{REMOTE_WSADDRESS}", $this->ReadPropertyString("RemoteAddress"), $htmlData);
        $htmlData = str_replace("{LOCAL_ADDRESS}", $this->ReadPropertyString("LocalAddress"), $htmlData);
        $htmlData = str_replace("{LOCAL_WSADDRESS}", $this->ReadPropertyString("LocalAddress"), $htmlData);
        $htmlData = str_replace("{PASSWORD}", $this->ReadPropertyString("Password"), $htmlData);
        $htmlData = str_replace("{WEBFRONTPASSWORD}", $this->GetWebfrontPassword($webfrontid), $htmlData);
        $htmlData = str_replace("{WEBFRONTID}", $webfrontid, $htmlData);
        $htmlData = str_replace("{ISLOCAL}", ($isLocal ? 'true' : 'false'), $htmlData);
        $htmlData = str_replace("{INSTANCE}", $IntID, $htmlData);

        if($viewport){
            $htmlData = str_replace("{VIEWPORT}", '<meta name="viewport" content="'.$this->ReadPropertyString("viewport_content").'">', $htmlData);
        }else{
            $htmlData = str_replace("{VIEWPORT}", "", $htmlData);
        }

        return $htmlData;
    }

    private function GetConnectAddress(){
        $connectID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}');
        if(count($connectID) == 0){} return "";
        return CC_GetUrl($connectID);
    }
    private function GetWebsocket(array $server, int $webfrontid){
        if(empty($server)) return;

        //$url_str = strtolower($url_str);
        //$url = parse_url($url_str);

        //$port = "";
        //if(key_exists("port", $url)) $port = ":" . $url["port"];

        if($this->isHttps($server)){
            return "wss://" . $server["HTTP_HOST"]. "/wfc/". $webfrontid ."/api/";
        }else{
            return "ws://" . $server["HTTP_HOST"].  "/wfc/". $webfrontid ."/api/";
        }
    }
    private function GetWebfrontPassword(int $webfrontid){
        if(!IPS_InstanceExists($webfrontid)) return "";
        $data = json_decode(IPS_GetConfiguration($webfrontid), true);

        if(is_array($data) && key_exists("Password", $data)){
            return $data["Password"];
        }else{
            return "";
        }

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
    private function GetConfigurationData(){
        $output = json_decode(IPS_GetConfiguration($this->InstanceID), true);
        $output["InstanceID"] = $this->InstanceID;

        return $output;
    }
    public static function isHttps(array $server)
    {
        if (array_key_exists("HTTPS", $server) && 'on' === $server["HTTPS"]) {
            return true;
        }
        if (array_key_exists("SERVER_PORT", $server) && 443 === (int)$server["SERVER_PORT"]) {
            return true;
        }
        if (array_key_exists("HTTP_X_FORWARDED_SSL", $server) && 'on' === $server["HTTP_X_FORWARDED_SSL"]) {
            return true;
        }
        if (array_key_exists("HTTP_X_FORWARDED_PROTO", $server) && 'https' === $server["HTTP_X_FORWARDED_PROTO"]) {
            return true;
        }
        return false;
    }

    public function UpdateTemplates(int $category){
        $templates = glob(__DIR__ ."/templates/*.html");
        //$category = $this->ReadPropertyInteger("TemplateCategoryID");

        if($category == 0){
            echo "It is not allowed to use the standard directory as the template directory!";
        }

        if (!IPS_CategoryExists($category)){
            echo "The template directory does not seem to exist!";
        }

        foreach($templates as $template_path){
            $path_parts = pathinfo($template_path);
            $template_name = "(Default)" .$path_parts['filename'];

            $ScriptID = @IPS_GetScriptIDByName($template_name, $category);
            if ($ScriptID === false){
                $ScriptID = IPS_CreateScript(0);
                IPS_SetParent($ScriptID, $category);
                IPS_SetName($ScriptID, $template_name);
            }

            IPS_SetScriptContent($ScriptID, file_get_contents ($template_path));

            $this->SendDebug('UpdateTemplates', 'FileName: ' . $path_parts['filename'], 0);
        }


    }
    public function LoadConnectAddress(bool $start = false){
        if(!$start || !empty($this->ReadPropertyString("RemoteAddress"))) return;

        $confData = json_decode(IPS_GetConfiguration($this->InstanceID), true);

        //bestimmte aktuelle einstellungen beibehalten
        $confData["RemoteAddress"] = $this->GetConnectAddress();

        IPS_SetConfiguration($this->InstanceID, json_encode($confData));
        IPS_ApplyChanges($this->InstanceID);
    }
    public function SetRandomPassword(bool $start = false){
        if(!$start || !empty($this->ReadPropertyString("Password"))) return;

        $confData = json_decode(IPS_GetConfiguration($this->InstanceID), true);

        //bestimmte aktuelle einstellungen beibehalten
        $confData["Password"] = $this->GenerateRandomPassword();

        IPS_SetConfiguration($this->InstanceID, json_encode($confData));
        IPS_ApplyChanges($this->InstanceID);
    }
}

?>