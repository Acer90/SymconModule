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

    private Function GetViewlevelData(){
        $r_arr = array();
        $r_arr[] = array("caption" => "Simple", "value" => 0);
        $r_arr[] = array("caption" => "Basic", "value" => 1);
        $r_arr[] = array("caption" => "Advance", "value" => 2);
        $r_arr[] = array("caption" => "Expert", "value" => 3);

        $isDev = false;
        if($isDev) $r_arr[] = array("caption" => "Developer", "value" => 4);

        return $r_arr;
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
                                "width": "80px",
                                "type": "OpenObjectButton",
                                "caption": "Open",
                                "name": "Open_Instance"
                            }, { 
                                "type": "SelectInstance", 
                                "name": "ModuleInstanceID", 
                                "validModules" : '.json_encode($modules).',
                                "caption": "Target (Require)" ,
                                "onChange": "SymconJSLiveConfigStore_OnSelectInstanceChange($id, $ModuleInstanceID, \"ViewLevelRequire\", \"Open_Instance\");"
                            }, { 
                                "type": "Select", 
                                "name": "ViewLevelRequire", 
                                "caption": "min. ViewLevel (Require)", 
                                "width": "160px",
                                "options": '.json_encode($this->GetViewlevelData()).'
                            }, {
                                "type": "ValidationTextBox", 
                                "width": "580px",
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
        $ConfigID_List = array();

        if($data["success"] == 1){
            foreach($data["data"] as $item){
                $hashdata[$item["Conf_ID"]] = $item["Conf_Hash"];
                $ConfigID_List[] = $item["Conf_ID"];

                switch($item["Conf_ViewLevel"]){
                    case 0: $level = "Simple"; break;
                    case 1: $level = "Basic"; break;
                    case 2: $level = "Advance"; break;
                    case 3: $level = "Expert"; break;
                    case 4: $level = "Developer"; break;
                    default: $level = "Simple"; break;
                }

                if(is_null($item["Vote_Points_User"])){
                    $item["Vote_Points_User"] = "No Vote Set!";
                }

                $img = $item["Conf_PicturePreview"];
                $p_item = array();
                $p_item[] = array("width"=> "100px", "type"=>"Image", "image"=> $img); //img
                $p_item[] = array("width"=> "270px", "type"=> "Label", "caption" => $item["Conf_Description"]); //Description
                $p_item[] = array("width"=> "80px", "type"=> "Label", "caption" => $item["Admin_ForumUser"]); //ForumUser
                $p_item[] = array("width"=> "80px", "type"=> "Label", "caption" => round($item["Vote_Points"], 2), "name" => "avg_votePoints_".$item["Conf_ID"]); //vote
                $p_item[] = array("width"=> "80px", "type"=> "Label", "caption" => $item["Conf_DownloadsCounter"], "name" => "downloads_".$item["Conf_ID"]); //Downloads

                //button mehr info und load
                $p_item[] = array("width"=> "80px", "type"=> "Button", "caption" => "More", "onClick" => " echo 'https://jslive.babenschneider.net/info.php?id=".$item["Conf_ID"]."';");
                $p_item[] = array("width"=> "80px", "type"=> "Button", "caption" => "Load", "confirm"=> "Create new Instancen (no Selected)", "onClick" => "echo SymconJSLiveConfigStore_LoadConfiguration(\$id, ".$item["Conf_ID"].");", "name" => "load_".$item["Conf_ID"]);
                
                //button update and Remove
                if($item["own_Module"]){
                    if($item["allow_update"]){
                        $pop_item = array();

                        $pop_sitem = array();
                        $pop_sitem[] = array("width"=> "150px", "type"=> "Label", "caption" => "Target: ");
                        $pop_sitem[] = array("width"=> "400px", "type"=> "SelectInstance", "value" => $item["Conf_Description"], "caption" => "Target", "name" => "update_Instance_".$item["Conf_ID"], "validModules" => array($item["Config_ModuleID"]), "onChange" => "SymconJSLiveConfigStore_OnSelectInstanceChange(\$id, \$update_Instance_".$item["Conf_ID"].", \"update_ViewLevel_".$item["Conf_ID"]."\", \"open_Instance_".$item["Conf_ID"]."\");");
                        $pop_sitem[] = array("width"=> "80px", "type"=> "Button", "caption" => "Preview", "onClick" => "SymconJSLiveConfigStore_Preview(\$id, \$update_Instance_".$item["Conf_ID"].");");
                        $pop_sitem[] = array("width"=> "80px", "type"=> "OpenObjectButton", "caption" => "Open", "name" => "open_Instance_".$item["Conf_ID"]);
                        $pop_item[] = array("type"=> "RowLayout", "items" => $pop_sitem);

                        $pop_sitem = array();
                        $pop_sitem[] = array("width"=> "150px", "type"=> "Label", "caption" => "ViewLevel: ");
                        $pop_sitem[] = array("width"=> "400px", "type"=> "Select", "value" => intVal($item["Conf_ViewLevel"]), "caption" => "ViewLevel (Min)", "name" => "update_ViewLevel_".$item["Conf_ID"], "options" => $this->GetViewlevelData());
                        $pop_item[] = array("type"=> "RowLayout", "items" => $pop_sitem);

                        $pop_sitem = array();
                        $pop_sitem[] = array("width"=> "150px", "type"=> "Label", "caption" => "Description: ");
                        $pop_sitem[] = array("width"=> "600px", "type"=> "ValidationTextBox", "value" => $item["Conf_Description"], "caption" => "Description", "name" => "update_Description_".$item["Conf_ID"]);
                        $pop_item[] = array("type"=> "RowLayout", "items" => $pop_sitem);

                        $pop_sitem = array();
                        $pop_sitem[] = array("width"=> "150px", "type"=> "Label", "caption" => "Picture: ");
                        $pop_sitem[] = array("width"=> "400px", "type"=> "SelectFile", "caption" => "Picture", "name" => "update_Picture_".$item["Conf_ID"], "extensions" => ".jpg,.gif,.txt");
                        $pop_item[] = array("type"=> "RowLayout", "items" => $pop_sitem);

                        $pop_item[] = array("width"=> "80px", "type"=> "Button", "caption" => "Update", "confirm"=> "Update Configuration", "onClick" => "SymconJSLiveConfigStore_UpdateConfiguration(\$id, ".$item["Conf_ID"].", \$update_Instance_".$item["Conf_ID"].", \$update_ViewLevel_".$item["Conf_ID"].", \$update_Description_".$item["Conf_ID"].", \$update_Picture_".$item["Conf_ID"].");");

                        $p_item[] = array("width"=> "80px", "type"=> "PopupButton", "caption" => "Update", "popup" => array("caption" => "Update - " . $item["Conf_Description"], "width"=> "400px", "items" => $pop_item));
                    }else{
                        $p_item[] = array("width"=> "80px", "type"=> "Label", "caption" => "");
                    }
    
                    if($item["allow_remove"]){
                        $p_item[] = array("width"=> "80px", "type"=> "Button", "caption" => "Delete", "confirm"=> "Remove Configuration", "onClick" => "SymconJSLiveConfigStore_DeleteConfiguration(\$id, ".$item["Conf_ID"].");");
                    }else{
                        $p_item[] = array("width"=> "80px", "type"=> "Label", "caption" => "");
                    }
                }else{
                    $pop_item = array();
                    $button_item = array();

                    $pop_sitem = array();
                    $pop_sitem[] = array("width"=> "150px", "type"=> "Label", "caption" => "Description:");
                    $pop_sitem[] = array("width"=> "400px", "type"=> "Label", "caption" => $item["Conf_Description"]);
                    $pop_item[] = array("type"=> "RowLayout", "items" => $pop_sitem);

                    $pop_sitem = array();
                    $pop_sitem[] = array("width"=> "150px", "type"=> "Label", "caption" => "User:");
                    $pop_sitem[] = array("width"=> "400px", "type"=> "Label", "caption" => $item["Admin_ForumUser"]);
                    $pop_item[] = array("type"=> "RowLayout", "items" => $pop_sitem);

                    $pop_sitem = array();
                    $pop_sitem[] = array("width"=> "150px", "type"=> "Label", "caption" => "Current Vote Level:" );
                    $pop_sitem[] = array("width"=> "400px", "type"=> "Label", "caption" => $item["Vote_Points"], "name" => "avg_votePoints2_".$item["Conf_ID"]);
                    $pop_item[] = array("type"=> "RowLayout", "items" => $pop_sitem);

                    $pop_sitem = array();
                    $pop_sitem[] = array("width"=> "150px", "type"=> "Label", "caption" => "Your Current Vote:");
                    $pop_sitem[] = array("width"=> "400px", "type"=> "Label", "caption" => $item["Vote_Points_User"], "name" => "user_votePoints_".$item["Conf_ID"]);
                    $pop_item[] = array("type"=> "RowLayout", "items" => $pop_sitem);

                    $button_item[] = array("width"=> "80px", "type"=> "Button", "caption" => "1", "onClick" => "SymconJSLiveConfigStore_SetVote(\$id, ".$item["Conf_ID"].", 1);");
                    $button_item[] = array("width"=> "80px", "type"=> "Button", "caption" => "2", "onClick" => "SymconJSLiveConfigStore_SetVote(\$id, ".$item["Conf_ID"].", 2);" );
                    $button_item[] = array("width"=> "80px", "type"=> "Button", "caption" => "3", "onClick" => "SymconJSLiveConfigStore_SetVote(\$id, ".$item["Conf_ID"].", 3);" );
                    $button_item[] = array("width"=> "80px", "type"=> "Button", "caption" => "4", "onClick" => "SymconJSLiveConfigStore_SetVote(\$id, ".$item["Conf_ID"].", 4);" );
                    $button_item[] = array("width"=> "80px", "type"=> "Button", "caption" => "5", "onClick" => "SymconJSLiveConfigStore_SetVote(\$id, ".$item["Conf_ID"].", 5);" );
                    $pop_item[] = array("type"=> "RowLayout", "items" => $button_item);

                    $p_item[] = array("width"=> "80px", "type"=> "PopupButton", "caption" => "Vote", "popup" => array("caption" => "Configuration-Vote", "width"=> "400px", "items" => $pop_item));
                }
                

                $p_array[] = array("type"=> "RowLayout", "items" => $p_item);
            }
            
            $count = $data["count"];
            $from = $data["from"]; 
            $to = $data["to"];
            $this->SetBuffer("CurCount", $count);
        }

        $this->SetBuffer("ConfigID_List", $ConfigID_List);
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
        $this->UpdateFormField("StoreDir", "caption", $dir);

        if($dir == "DESC"){
            $dir = "ASC";
        }else{
            $dir = "DESC";
        }

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

    public function OnSelectInstanceChange($ModuleInstanceID, $viewlevel_name, $openButton_name){
        if(!IPS_InstanceExists($ModuleInstanceID)) {
            return;
        }

        $modulID = IPS_GetInstance($ModuleInstanceID)["ModuleInfo"]["ModuleID"];
        $modullist = $this->GetBuffer("Modulelist");
        if(in_array($modulID, $modullist)){
            $level = IPS_GetProperty($ModuleInstanceID, "ViewLevel");
            $this->UpdateFormField($viewlevel_name, "value", $level);
            $this->UpdateFormField($openButton_name, "objectID", $ModuleInstanceID);
            //echo $openButton_name;
        }
    }

    private Function GetInstanceList(){
        $instanceList = array();
        $search = strtolower($this->GetBuffer("SearchInstance"));
        $indexModulType = intval($this->GetBuffer("SelectInstanceType"));

        $this->SetBuffer("Selected_Instance_List", array()); //alle selectierten Instancen entfernen

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

    public function ChangeInstanceList($Instance_List){
        $Selected_Instance_List = array();
        foreach($Instance_List as $item){
            if($item["Select"]){
                $Selected_Instance_List[] = $item["InstanceID"];
            }
        }

        $this->SetBuffer("Selected_Instance_List", $Selected_Instance_List);
        $this->UpdateLoadButtons();
    }

    private function UpdateLoadButtons(){
        $ConfigID_List = $this->GetBuffer("ConfigID_List");
        $Selected_Instance_List = $this->GetBuffer("Selected_Instance_List");
        if(!is_array($ConfigID_List) || !is_array($Selected_Instance_List)) return;

        $selItems = count($Selected_Instance_List);
        $txt = "Load Configuration for ".$selItems." Selected Instancen";
        if($selItems == 0){
            $txt = "Create new Instancen (no Selected)";
        }

        foreach($ConfigID_List as $item){
            $this->UpdateFormField("load_".$item, "confirm", $txt);
        }
    }

    public function SearchInstanceList($search){
        $this->SetBuffer("SearchInstance", $search);
        $this->UpdateFormField("Instance_List", "values", json_encode($this->GetInstanceList()));
        $this->UpdateLoadButtons();
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
        $this->UpdateLoadButtons();
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
        $config = $function($ModuleInstanceID, false, array());
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

    public Function LoadConfiguration($conf_ID){
        $output = "";
        $hash = "";
        $hashlist = $this->GetBuffer("HashData");
        $Selected_Instance_List = $this->getBuffer("Selected_Instance_List");

        if(array_key_exists($conf_ID, $hashlist)){
            $hash = $hashlist[$conf_ID];
        }

        if(empty($hash)) return "CRC=ERROR!";

        $postData = array();
        $url = self::APILINK . "api/Configuration.php?Type=load&ConfigID=".$conf_ID;
        $data = json_decode($this->GetWebData($url, $postData), true);

        if(!$data["success"]) return $data["msg"];
        if(sha1($data["data"]) != $hash) return "CRC=FALSE! (" . sha1($data["data"]). ")";

        $json = json_decode($data["data"], true);
        $file = base64_encode($data["data"]);

        if(count($Selected_Instance_List) > 0){
            //update selectet instancen   
            
            foreach($Selected_Instance_List as $selected_Instance){
                if(!IPS_ObjectExists($selected_Instance)) continue;
                //wrong type
                if(IPS_GetObject($selected_Instance)["ObjectType"] !== 1) continue;
                //skip wrong modules
                if(IPS_GetInstance($selected_Instance)["ModuleInfo"]["ModuleID"] !== $json["ModuleID"]) continue;

                $function = IPS_GetInstance($selected_Instance)["ModuleInfo"]["ModuleName"]."_LoadConfigurationFile";
                $config = $function($selected_Instance, $file, false);
    
                //IPS_SetConfiguration($selected_Instance, json_encode($json["Config"]));
                //IPS_ApplyChanges($selected_Instance);
                $output .= "Configuration of " . IPS_GetObject($selected_Instance)["ObjectName"] . "(".$selected_Instance.") changed! \r\n";
            }   
        }else{
            //create new instance
            $InstanceID = IPS_CreateInstance($json["ModuleID"]);
            IPS_SetName($InstanceID, $data["description"]); // Instanz benennen
            IPS_SetConfiguration($InstanceID, json_encode($json["Config"]));
            IPS_ApplyChanges($InstanceID);
            $output .= "New Instance => " . IPS_GetObject($InstanceID)["ObjectName"] . "(".$InstanceID.") created! \r\n";
        } 
        $this->UpdateFormField("downloads_".$conf_ID , "caption", $data["downloads"]);

        return $output;
    }
    public Function UpdateConfiguration($conf_ID, $ModuleInstanceID, $ViewLevelRequire, $Description, $Picture){
        $postData = array();
        $update = false;
        if(!is_numeric($conf_ID) && $conf_ID <= 0) return;

        $postData["UserID"] = $this->ReadPropertyString("UserID");
        $postData["ConfigID"] = $conf_ID;
        $postData["ForumUser"] = $this->ReadPropertyString("ForumUsername");

        if($ModuleInstanceID > 0 && IPS_InstanceExists($ModuleInstanceID)) {
            $function = IPS_GetInstance($ModuleInstanceID)["ModuleInfo"]["ModuleName"]."_ExportConfiguration";
            $postData["Config"] = $function($ModuleInstanceID, array());
            $postData["ViewLevel"] = $ViewLevelRequire;
            $update = true;
        }

        if(!empty($Description)){
            $postData["Description"] = $Description;
            $update = true;
        }

        if(!empty($Picture)){
            $postData["Pic"] = $Picture;
            $update = true;
        }

        if(!$update){
            return "No new Configuration(Instance), Description, or Picture set!";
        }else{
            $url = self::APILINK . "api/Configuration.php?Type=update";
            $data = json_decode($this->GetWebData($url, $postData), true);

            if(!$data["success"]) return $data["msg"];

            $this->CheckPending();
            if(!empty($Picture) || !empty($conf_ID)){
                $this->UpdateFormField("ExpandUploadPending", "expanded", true);
            }

            $this->UpdateFormField("ModuleInstanceID", "value", 0);
            $this->UpdateFormField("Description", "value", "");
            $this->UpdateFormField("Picture", "value", "");

            $modulelist = $this->GetStoreList();
            $this->UpdateFormField("StoreList", "items", json_encode($modulelist["data"]));
            $this->UpdateLoadButtons();
        }
    }
    public Function DeleteConfiguration($conf_ID){
        if(empty($conf_ID)) return "ERROR!";

        $postData = array(  'UserID'=>$this->ReadPropertyString("UserID"), 
                            'ConfigID' => $conf_ID);

        $url = self::APILINK . "api/Configuration.php?Type=remove";
        $data = json_decode($this->GetWebData($url, $postData), true);

        if(!$data["success"]) return $data["msg"];

        $modulelist = $this->GetStoreList();
        $this->UpdateFormField("StoreList", "items", json_encode($modulelist["data"]));
        $this->UpdateLoadButtons();
    }
    public Function SetVote($conf_ID, $vote){
        if($vote <= 0) return "Error on Set Vote";
        if($vote > 5) return "Error on Set Vote";

        $postData = array(  'UserID'=>$this->ReadPropertyString("UserID"), 
                            'ConfigID' => $conf_ID,
                            'Vote' => $vote);

        $url = self::APILINK . "api/Configuration.php?Type=vote";
        $data = json_decode($this->GetWebData($url, $postData), true);

        if(!$data["success"]) return $data["msg"];

        $this->UpdateFormField("avg_votePoints_".$conf_ID, "caption", round($data["avg_vote"], 2));
        $this->UpdateFormField("avg_votePoints2_".$conf_ID, "caption", round($data["avg_vote"], 2));
        $this->UpdateFormField("user_votePoints_".$conf_ID, "caption", $data["vote"]);
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