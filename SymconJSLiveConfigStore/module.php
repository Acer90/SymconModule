<?php

class SymconJSLiveConfigStore extends IPSModule{
    private const APILINK = "https://jslive.babenschneider.net/";
    private const MODULIDLIST = ["Calendar" => "{46B41C3B-DDAE-BA35-2A1E-6CF4B7F9BF7A}",
    "Chart" => "{4713B9C2-22C8-7A45-060C-8C678DE05CC6}",
    "Custom" => "{784C3E34-F175-98D1-6022-8ADDFAE45CE5}",
    "Doughnut/Pie" => "{9419245E-CE2E-F949-AAB6-714E2045632F}",
    "Gauge" => "{71B93700-9659-97C6-AD83-984C2B44139F}",
    "Progressbar" => "{934051F5-EE82-953D-5241-A29D74CBC251}",
    "Radar Chart" => "{95C8F306-4E51-949E-E25B-FD5C1F173295}"
    ];

    public function Create() {
        //Never delete this line!
        parent::Create();

        $this->RegisterPropertyString("ForumUsername", "");
        $this->RegisterPropertyString("UserID", $this->GetUserID());
        $this->SetBuffer("SearchInstance", ""); 

        $this->RegisterTimer("CheckStatus", 0, 'SymconJSLiveConfigStore_CheckPending($_IPS[\'TARGET\']);');
    }

    public function ApplyChanges() {
        //Never delete this line!
        parent::ApplyChanges();
        
    }

    public function GetConfigurationForm()
    {
        $this->SetBuffer("OrderBy", "Conf_Description"); 
        $this->SetBuffer("Search", ""); 
        $this->SetBuffer("Direction", "ASC"); 
        
        $formData = array();
        $jsonPath = realpath(__DIR__ . "/form.json");
        $formData = json_decode(file_get_contents($jsonPath), true);

        //update upload area
        $this->UpdateAccessData();
        $modullist = $this->GetModuleList();

        //echo "test=>".json_encode($this->GetUploadForm());
        $formData["actions"][0]["items"] = $this->GetUploadForm();

        //update instancen 
        $instanceData = $formData["actions"][1]["items"][0];
        
        $m_List = array();
        $i = 0;
        foreach($modullist as $key => $item){
            $m_item = array();
            $m_item["caption"] = $key;
            $m_item["value"] = $i;

            $m_List[] = $m_item;
            $i++;
        }

        $instanceData["items"][0]["items"][0]["value"] = intval($this->GetBuffer("SelectInstanceType")); 
        $instanceData["items"][0]["items"][0]["options"] = $m_List; 
        $instanceData["items"][0]["items"][2]["value"] = $this->GetBuffer("SearchInstance");  //Search box value
        

        $instanceData["items"][1]["values"]= $this->GetInstanceList();
        $formData["actions"][1]["items"][0] = $instanceData;

        //update store data
        $sData = $this->GetStoreList();
        //array("data" => json_encode($p_array), "moduleID" => $moduleID, "moduleName" => $moduleName, "count" => $count, "from" => $from, "to" => $to);
        
        $storeData = $formData["actions"][1]["items"][1];
        $storeData["caption"] = "JSLive Config-Store (" .$sData["moduleName"]. ")";

        $storeData["items"][0]["items"][0]["value"] = $this->GetBuffer("OrderBy");
        $storeData["items"][0]["items"][5]["caption"] = $sData["from"] ."-".$sData["to"] . " (".$sData["count"] .")";

        $storeData["items"][1]["items"] = $sData["data"];

        //$print_r($storeData["items"][1]);

        $formData["actions"][1]["items"][1] = $storeData;


        return json_encode($formData);
    }

    private Function GetUploadForm(){
        $formdata = array();
        $modules = array();
        $accessData = $this->GetBuffer("AccessData");
        $modullist = $this->GetBuffer("Modulelist");
        
        foreach($modullist as $modulid){
            $modules[] = $modulid;
        }

        //echo "TEST=>".json_encode($modules);

        if($accessData["access_level"] <= 0){
            $formdata[] = array("type"=>"Button", "caption" => "Request Access", "onClick" => "echo SymconJSLiveConfigStore_RequestAccess(\$id);");
        }elseif($accessData["access_level"] == 1){
            $formdata[] = array("type"=>"Label", "caption" => "Wait for enable your access request from JSLive-Administrator");
        }elseif($accessData["access_level"] >= 2){
            $json = '[{ 
                        "type": "RowLayout",
                        "items": [
                            {
                                "width": "80px",
                                "type": "Button",
                                "caption": "Preview",
                                "onClick": "SymconJSLiveConfigStore_Preview($id, $ModuleInstanceID);"
                            }, { 
                                "type": "SelectInstance", 
                                "name": "ModuleInstanceID", 
                                "validModules" : '.json_encode($modules).',
                                "caption": "Target (Require)" ,
                                "onChange": "SymconJSLiveConfigStore_UpdateViewLevel($id, $ModuleInstanceID);"
                            }, { 
                                "type": "Select", 
                                "name": "ViewLevelRequire", 
                                "caption": "min. ViewLevel (Require)", 
                                "width": "160px",
                                "options": [
                                    { "caption": "Simple", "value": 0 },
                                    { "caption": "Basic", "value": 1 },
                                    { "caption": "Advance", "value": 2 },
                                    { "caption": "Expert", "value": 3 },
                                    { "caption": "Developer", "value": 4 }
                                ]
                            }, {
                                "type": "ValidationTextBox", 
                                "width": "670px",
                                "name": "Description", 
                                "caption": "Description  (Require)" 
                            }, { 
                                "type": "SelectFile", 
                                "width": "300px",
                                "name": "Picture", 
                                "caption": "Picture  (Require)", 
                                "extensions": ".jpg,.jpeg,.gif,.png" 
                            }, {
                                "width": "80px",
                                "type": "Button",
                                "caption": "Upload",
                                "onClick": "SymconJSLiveConfigStore_UploadConfig($id, $ModuleInstanceID, $ViewLevelRequire, $Description, $Picture);"
                            }
                        ]
                    }, {
                        "type": "ExpansionPanel",
                        "caption": "Upload Pending",
                        "name": "ExpandUploadPending", 
                        "expanded": false,
                        "items": [
                                {
                                    "type": "ColumnLayout",
                                    "name": "UploadPending", 
                                    "items": '.$this->GetUploadPending().'
                                }
                            ]
                        }
                    ]';

            $formdata = json_decode($json, true);   
        }

        return $formdata;
    }

    private Function GetUploadPending(){
        $p_array = array();
        $postData = array('UserID'=>$this->ReadPropertyString("UserID"));
        $url = self::APILINK . "api/Configuration.php?Type=getpendinglist";

        $data = json_decode($this->GetWebData($url, $postData), true);

        $json = '{ 
            "type": "RowLayout",
            "items": [
                { 
                    "width": "100px",
                    "type": "Label", 
                    "bold": true,
                    "underline": true,
                    "caption": "Type"
                }, {
                    "width": "100px",
                    "type": "Label", 
                    "bold": true,
                    "underline": true,
                    "caption": "Viewlevel"
                }, {
                    "width": "300px",
                    "type": "Label", 
                    "bold": true,
                    "underline": true,
                    "caption": "Description"
                }, {
                    "type": "Label", 
                    "bold": true,
                    "underline": true,
                    "caption": "State"
                }
            ]
        }';

        $p_array[] = json_decode($json, true);
        $pendingItems = false;
        if($data["success"] == 1){
            foreach($data["data"] as $item){
                $pendingItems = true;

                //echo "TEST=>".$item["Conf_State"];
                switch($item["Conf_State"]){
                    case 0: $state = "Wait for start verification"; break;
                    case 1: $state = "Pending verification"; break;
                    case 2: $state = strtoupper("Verification Failed!"); break;
                    default: $state = "NO INFO FOUND!"; break;
                }
    
                switch($item["Conf_ViewLevel"]){
                    case 0: $level = "Simple"; break;
                    case 1: $level = "Basic"; break;
                    case 2: $level = "Advance"; break;
                    case 3: $level = "Expert"; break;
                    case 4: $level = "Developer"; break;
                    default: $level = "Simple"; break;
                }

                $p_item = array();
                $p_item[] = array("width"=> "100px", "type"=> "Label", "caption" => $item["Modul_SName"]); //Type
                $p_item[] = array("width"=> "100px", "type"=> "Label", "caption" => $level); //Viewlevel
                $p_item[] = array("width"=> "300px", "type"=> "Label", "caption" => $item["Conf_Description"]); //Description
                $p_item[] = array("type"=> "Label", "caption" => $state); //State
                $p_array[] = array("type"=> "RowLayout", "items" => $p_item);
            }
        }

        if($pendingItems){
            $this->SetTimerInterval("CheckStatus", 5000);
        }else{
            $this->SetTimerInterval("CheckStatus", 0);
        }

        return json_encode($p_array);
    }

    private Function GetStoreList(){
        $p_array = array();
        $count = 0;
        $from = intval($this->GetBuffer("From")); 
        $to = 0;
        $dir = $this->GetBuffer("Direction");
        $search = $this->GetBuffer("Search"); 
        $orderby = $this->GetBuffer("OrderBy"); 
        

        $moduleList = $this->GetBuffer("Modulelist");
        $indexModulType = intval($this->GetBuffer("SelectInstanceType"));
        $moduleID = $moduleList[array_keys($moduleList)[$indexModulType]];
        $moduleName = array_keys($moduleList)[$indexModulType];

        $postData = array(
            'UserID'=>$this->ReadPropertyString("UserID"),
            'Search' => $search,
            'OrderBy' => $orderby,
            'OrderDir' => $dir,
            'StartBy' => $from,
            'Module' => $moduleID
        );
        $url = self::APILINK . "api/Configuration.php?Type=getlist";

        //echo "TEST".$this->GetWebData($url, $postData);
        //return;

        $data = json_decode($this->GetWebData($url, $postData), true);

        $json = '{ 
            "type": "RowLayout",
            "items": [
                {
                    "width": "100px",
                    "type": "Label", 
                    "bold": true,
                    "underline": true,
                    "caption": "Picture"
                }, {
                    "width": "270px",
                    "type": "Label", 
                    "bold": true,
                    "underline": true,
                    "caption": "Description"
                }, {
                    "width": "80px",
                    "type": "Label", 
                    "bold": true,
                    "underline": true,
                    "caption": "User"
                }, {
                    "width": "80px",
                    "type": "Label", 
                    "bold": true,
                    "underline": true,
                    "caption": "Vote"
                }, {
                    "width": "80px",
                    "type": "Label", 
                    "bold": true,
                    "underline": true,
                    "caption": "Downloads"
                }, {
                    "type": "Label", 
                    "bold": true,
                    "underline": true,
                    "caption": "Funktionen"
                }
            ]
        }';

        $p_array[] = json_decode($json, true);
        $hashdata = array();

        if($data["success"] == 1){
            foreach($data["data"] as $item){
                $hashdata[$item["Conf_ID"]] = $item["Conf_Hash"];


                switch($item["Conf_ViewLevel"]){
                    case 0: $level = "Simple"; break;
                    case 1: $level = "Basic"; break;
                    case 2: $level = "Advance"; break;
                    case 3: $level = "Expert"; break;
                    case 4: $level = "Developer"; break;
                    default: $level = "Simple"; break;
                }

                $img = $item["Conf_PicturePreview"];
                $p_item = array();
                $p_item[] = array("width"=> "100px", "type"=>"Image", "image"=> $img); //img
                $p_item[] = array("width"=> "270px", "type"=> "Label", "caption" => $item["Conf_Description"]); //Description
                $p_item[] = array("width"=> "80px", "type"=> "Label", "caption" => $item["Admin_ForumUser"]); //ForumUser
                $p_item[] = array("width"=> "80px", "type"=> "Label", "caption" => $item["Vote_Points"]); //vote
                $p_item[] = array("width"=> "80px", "type"=> "Label", "caption" => $item["Conf_DownloadsCounter"]); //Downloads

                //button mehr info und load
                $p_item[] = array("width"=> "80px", "type"=> "Button", "caption" => "More", "onClick" => " echo 'https://jslive.babenschneider.net/info.php?id=".$item["Conf_ID"]."';");
                $p_item[] = array("width"=> "80px", "type"=> "Button", "caption" => "Load", "confirm"=> "Load Configuration for Selected Instancen", "onClick" => "echo SymconJSLiveConfigStore_LoadConfiguration(\$id, ".$item["Conf_ID"].", \$Instance_List);");
                
                //button update and Remove
                if($item["own_Module"]){
                    if($item["allow_update"]){
                        $p_item[] = array("width"=> "80px", "type"=> "Button", "caption" => "Update", "confirm"=> "Load Configuration", "onClick" => "SymconJSLiveConfigStore_UpdateConfiguration(\$id, ".$item["Conf_ID"].", \$ModuleInstanceID, \$ViewLevelRequire, \$Description, \$Picture);");
                    }else{
                        $p_item[] = array("width"=> "80px", "type"=> "Label", "caption" => "");
                    }
    
                    if($item["allow_remove"]){
                        $p_item[] = array("width"=> "80px", "type"=> "Button", "caption" => "Delete", "confirm"=> "Load Configuration", "onClick" => "SymconJSLiveConfigStore_DeleteConfiguration(\$id, ".$item["Conf_ID"].");");
                    }else{
                        $p_item[] = array("width"=> "80px", "type"=> "Label", "caption" => "");
                    }
                }else{
                    $pop_item = array();
                    $pop_item[] = array("width"=> "120px", "type"=> "Button", "caption" => "Vote Up", "onClick" => "SymconJSLiveConfigStore_SetVote(\$id, ".$item["Conf_ID"].", 1);");
                    $pop_item[] = array("width"=> "120px", "type"=> "Button", "caption" => "Vote Down", "onClick" => "SymconJSLiveConfigStore_SetVote(\$id, ".$item["Conf_ID"].", -1);" );

                    $p_item[] = array("width"=> "80px", "type"=> "Button", "caption" => "Vote", "popup" => array("caption" => "Configuration-Vote", "items" => array("type"=> "RowLayout", "items" => $pop_item)));
                }
                

                $p_array[] = array("type"=> "RowLayout", "items" => $p_item);
            }
            
            $count = $data["count"];
            $from = $data["from"]; 
            $to = $data["to"];
            $this->SetBuffer("CurCount", $count);
        }

        $this->SetBuffer("HashData", $hashdata);
        $r_data = array("data" => $p_array, "moduleID" => $moduleID, "moduleName" => $moduleName, "count" => $count, "from" => $from, "to" => $to, "dir" => $dir);
        return $r_data;
    }

    public function SearchStoreList($search, $orderby){
        $this->SetBuffer("Search", $search); 
        $this->SetBuffer("OrderBy", $orderby); 
        $this->SetBuffer("From", 0);

        $modulelist = $this->GetStoreList();
        $this->UpdateFormField("Store", "caption", "JSLive Config-Store (" .$modulelist["moduleName"]. ")");
        $this->UpdateFormField("Range", "caption", $modulelist["from"] ."-".$modulelist["to"] . " (".$modulelist["count"] .")");
        $this->UpdateFormField("StoreList", "items", json_encode($modulelist["data"]));
        $this->UpdateFormField("SearchModule", "value", "");
    }

    public function StoreListChangeDirection(){
        $dir = $this->GetBuffer("Direction"); 

        if($dir == "DESC"){
            $dir = "ASC";
        }else{
            $dir = "DESC";
        }

        $this->UpdateFormField("StoreDir", "caption", $dir);

        $this->SetBuffer("Direction", $dir); 
        $modulelist = $this->GetStoreList();
        $this->UpdateFormField("StoreList", "items", json_encode($modulelist["data"]));
        $this->UpdateFormField("Range", "caption", $modulelist["from"] ."-".$modulelist["to"] . " (".$modulelist["count"] .")");
    }

    public function StoreListChangeStartBy($change){
        $CurCount = intval($this->GetBuffer("CurCount"));
        $curVal = $this->GetBuffer("From");
        $curVal = $curVal + $change;
        if($curVal < 0) $curVal = 0;
        if($change > 0 && $curVal >= $CurCount) return;
        $this->SetBuffer("From", $curVal);

        $modulelist = $this->GetStoreList();
        $this->UpdateFormField("Range", "caption", $modulelist["from"] ."-".$modulelist["to"] . " (".$modulelist["count"] .")");
        $this->UpdateFormField("StoreList", "items", json_encode($modulelist["data"]));
    }

    public function CheckPending(){
        //$this->SendDebug(__FUNCTION__, "TEST", 0);
        $this->UpdateFormField("UploadPending", "items", $this->GetUploadPending());
    }

    public function UpdateViewLevel($ModuleInstanceID){
        if(!IPS_InstanceExists($ModuleInstanceID)) {
            return;
        }

        $modulID = IPS_GetInstance($ModuleInstanceID)["ModuleInfo"]["ModuleID"];
        $modullist = $this->GetBuffer("Modulelist");
        if(in_array($modulID, $modullist)){
            $level = IPS_GetProperty($ModuleInstanceID, "ViewLevel");
            $this->UpdateFormField("ViewLevelRequire", "value", $level);
        }
    }

    private Function GetInstanceList(){
        $instanceList = array();
        $search = strtolower($this->GetBuffer("SearchInstance"));
        $indexModulType = intval($this->GetBuffer("SelectInstanceType"));

        if($indexModulType < 0 || $indexModulType >= count($this->GetBuffer("Modulelist"))){
            $this->SendDebug(__FUNCTION__, "Select Type (".$indexModulType.") is wrong!", 0);
        }

        if(count($this->GetBuffer("Modulelist")) == 0)return $instanceList;
        $modulID = $this->GetBuffer("Modulelist")[array_keys($this->GetBuffer("Modulelist"))[$indexModulType]];
        

        foreach(IPS_GetInstanceListByModuleID($modulID) as $index) {

            if(!empty($search)){
                $pos1 = strpos(strtolower(IPS_GetObject($index)["ObjectName"]), $search);
                $pos2 = strpos($index, $search);
                if($pos1 === false && $pos2 === false) continue;
            }

            $instanceItem = array();
            $instanceItem["InstanceID"] = $index;
            $instanceItem["Name"] =  IPS_GetObject($index)["ObjectName"]. " (".IPS_GetLocation($index).")";
            $instanceItem["State"] = false;
            
            $instanceList[] = $instanceItem;
        }

        return $instanceList;
    }

    public function SearchInstanceList($search){
        $this->SetBuffer("SearchInstance", $search);
        $this->UpdateFormField("Instance_List", "values", json_encode($this->GetInstanceList()));
    }

    public function SelectInstanceType($selectType){
        $this->SetBuffer("SelectInstanceType", $selectType);
        $this->UpdateFormField("Instance_List", "values", json_encode($this->GetInstanceList()));
        $this->SetBuffer("Search", ""); 
        $this->SetBuffer("From", 0);

        $modulelist = $this->GetStoreList();
        $this->UpdateFormField("Store", "caption", "JSLive Config-Store (" .$modulelist["moduleName"]. ")");
        $this->UpdateFormField("Range", "caption", $modulelist["from"] ."-".$modulelist["to"] . " (".$modulelist["count"] .")");
        $this->UpdateFormField("StoreList", "items", json_encode($modulelist["data"]));
        $this->UpdateFormField("SearchModule", "value", "");
    }

    public function GetForumUser($username){
        return "https://community.symcon.de/u/" . strtolower($username);
    }

    public function ReadUserID(){
        return $this->ReadPropertyString("UserID");
    }

    private function GetUserID($data = null){
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    private function GetWebData($url, $postdata = array()){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        if(count($postdata) > 0){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        }
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
        $result = curl_exec($ch);

        return $result;
    }

    public Function UploadConfig($ModuleInstanceID, $ViewLevelRequire, $Description, $Picture){
        if(!IPS_InstanceExists($ModuleInstanceID)) {
            echo $this->Translate("Module not found!");
            return;
        }

        $function = IPS_GetInstance($ModuleInstanceID)["ModuleInfo"]["ModuleName"]."_ExportConfiguration";
        $config = $function($ModuleInstanceID, array());
        $postData = array(  'UserID'=>$this->ReadPropertyString("UserID"), 
                            'ForumUsername' => $this->ReadPropertyString("ForumUsername"),
                            'Pic' => $Picture,
                            'Description' => $Description,
                            'ViewLevel' => $ViewLevelRequire,
                            'Config' => $config);

        $url = self::APILINK . "api/Configuration.php?Type=upload";

        //echo $this->GetWebData($url, $postData);

        $data = json_decode($this->GetWebData($url, $postData), true);
        if($data["success"]){
            $this->CheckPending();
            $this->UpdateFormField("ExpandUploadPending", "expanded", true);
            $this->UpdateFormField("ModuleInstanceID", "value", 0);
            $this->UpdateFormField("Description", "value", "");
            $this->UpdateFormField("Picture", "value", "");
        }else{
            echo $data["msg"];
        }
    }

    public Function LoadConfiguration($conf_ID, $Instance_List){
        $output = "";
        $hash = "";
        $hashlist = $this->GetBuffer("HashData");
        if(array_key_exists($conf_ID, $hashlist)){
            $hash = $hashlist[$conf_ID];
        }

        if(empty($hash)) return "CRC=ERROR!";

        $postData = array();
        $url = self::APILINK . "api/Configuration.php?Type=load&ConfigID=".$conf_ID;
        $data = json_decode($this->GetWebData($url, $postData), true);

        if(!$data["success"]) return $data["msg"];
        if(sha1($data["data"]) != $hash) return "CRC=FALSE!";

        $json = json_decode($data["data"], true);
        foreach($Instance_List as $item){
            if($item["Select"]){
                if(!IPS_ObjectExists($item["InstanceID"])) continue;
                //wrong type
                if(IPS_GetObject($item["InstanceID"])["ObjectType"] !== 1) continue;
                //skip wrong modules
                if(IPS_GetInstance($item["InstanceID"])["ModuleInfo"]["ModuleID"] !== $json["ModuleID"]) continue;

                IPS_SetConfiguration($item["InstanceID"], json_encode($json["Config"]));
                IPS_ApplyChanges($item["InstanceID"]);
                $output .= "Configuration of " . IPS_GetObject($item["InstanceID"])["ObjectName"] . "(".$item["InstanceID"].") changed! \r\n";
            }
        }

        return $output;
    }
    public Function UpdateConfiguration($conf_ID, $ModuleInstanceID, $ViewLevelRequire, $Description, $Picture){

    }
    public Function DeleteConfiguration($conf_ID){

    }
    public Function SetVote($conf_ID, $vote){
        if($vote <= 0) $vote = -1;
        if($vote > 0) $vote = 1;

        echo $conf_ID;
    }
    private Function UpdateAccessData(){
        $postData = array('UserID'=>$this->ReadPropertyString("UserID"));
        $url = self::APILINK . "api/Access.php?Type=checkAccess";

        $data = json_decode($this->GetWebData($url, $postData), true);

        if($data["success"]){
            $this->SetBuffer("AccessData", $data);
        }else{
            $data = array("access_level" => 0);
            $this->SetBuffer("AccessData", $data);
        }

        return $data;
    }
    public function RequestAccess(){
        if(empty($this->ReadPropertyString("ForumUsername"))){
            return "NO FORUM-User SET!";
        } 

        $this->SetStatus(102);
        $postData = array('UserID'=>$this->ReadPropertyString("UserID"), 'ForumUsername' => $this->ReadPropertyString("ForumUsername"));
        $url = self::APILINK . "api/Access.php?Type=requestAccess";

        $data = json_decode($this->GetWebData($url, $postData), true);
        if(!$data["success"]){
            echo  "Request failed!";
        }

        $this->UpdateAccessData();
        echo json_encode($this->GetUploadForm());
        $this->UpdateFormField("UploadConfiguartion", "items", json_encode(array()));
    }

    private Function GetModuleList(){
        $postData = array('UserID'=>$this->ReadPropertyString("UserID"));
        $url = self::APILINK . "api/Modules.php?Type=getlist";

        $data = json_decode($this->GetWebData($url, $postData), true);
        $modulelist = array();

        if($data["success"]){
            $modulelist = $data["data"];
        }

        $this->SetBuffer("Modulelist", $modulelist);
        return $modulelist;
    }

    public function Preview($ModuleInstanceID){
        if(!IPS_InstanceExists($ModuleInstanceID)) {
            echo $this->Translate("Module not found!");
            return;
        }

        $function = IPS_GetInstance($ModuleInstanceID)["ModuleInfo"]["ModuleName"]."_GetLink";
        echo $function($ModuleInstanceID);
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

?>