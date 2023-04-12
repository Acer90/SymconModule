<?php

class SymconJSLiveModuleSync extends IPSModule{
    private const APILINK = "https://jslive.babenschneider.net/";

    public function Create() {
        //Never delete this line!
        parent::Create();

        $this->RegisterPropertyString("SelectType", "{4713B9C2-22C8-7A45-060C-8C678DE05CC6}");
        $this->RegisterPropertyString("InstanceList", "[]");
        $this->RegisterPropertyString("Parameterlist", "[]");

        $this->SetBuffer("onChange_InstanceList", array());
        $this->SetBuffer("allowSync", true);
        $this->SetBuffer("lastSync", time());
    }

    public function ApplyChanges() {
        //Never delete this line!
        parent::ApplyChanges();
        
        $this->RegisterOnChangeInstance();
    }

    public function GetConfigurationForm()
    {
        $formData = array();
        $jsonPath = realpath(__DIR__ . "/form.json");
        $formData = json_decode(file_get_contents($jsonPath), true);

        //update instancen 
        $elements = $formData["elements"];

        $modullist = $this->GetModuleList();
        //print_r($modullist);
        $m_List = array();
        $i = 1;
        foreach($modullist as $key => $item){
            $m_item = array();
            $m_item["caption"] = $key;
            $m_item["value"] = $item;

            $m_List[] = $m_item;
            $i++;
        }
        
        
        $elements[0]["options"] = $m_List;

        $instanceList = $elements[1]["items"][0]["items"][0];

        $instanceList["columns"][1]["edit"]["validModules"] = $this->ReadPropertyString("SelectType");
        $instanceList["values"] = $this->GetInstanceListData(json_decode($this->ReadPropertyString("InstanceList"), true));

        $elements[1]["items"][0]["items"][0] = $instanceList;

        $parameterList = $elements[1]["items"][1]["items"][0];

        $parameterList["values"] = $this->GetParameterListData($this->ReadPropertyString("SelectType"), json_decode($this->ReadPropertyString("Parameterlist"), true));

        $elements[1]["items"][1]["items"][0] = $parameterList;

        $formData["elements"] = $elements;

        return json_encode($formData);
    }

    private function RegisterOnChangeInstance(){
        $old_sync = $this->GetBuffer("onChange_InstanceList");
        $new_sync = array();

        foreach(json_decode($this->ReadPropertyString("InstanceList"), true) as $item){
            if($item["Master"]){
                $instancID = $item["InstanceID"];
                $new_sync[] = $instancID;
                $key = array_search($instancID, $old_sync);

                if($key !== false) unset($old_sync[$key]);
                $this->RegisterMessage($instancID, 10506 /* IM_CHANGESETTINGS */);
                $this->SendDebug(__FUNCTION__, "RegisterMessage => " . $instancID, 0);
            }
        }

        foreach($old_sync as $instancID){
            $this->UnregisterMessage($instancID, 10506 /* IM_CHANGESETTINGS */);
            $this->SendDebug(__FUNCTION__, "(Un)registerMessage => " . $instancID, 0);
        }
        $this->SetBuffer("onChange_InstanceList", $new_sync);
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
        //on Change Instance
        if($Message == 10506){
            $syncList = $this->GetBuffer("onChange_InstanceList"); 
            $allowSync = $this->GetBuffer("allowSync"); 
            $moduleID = $this->ReadPropertyString("SelectType");

            if(IPS_GetInstance($SenderID)["ModuleInfo"]["ModuleID"] != $moduleID) {
                $this->SendDebug(__function__, "CHANGE MODULE ID (".$SenderID.") WRONG!", 0);
                return;
            }

            if(in_array($SenderID, $syncList) && $allowSync){
                $allowSync = $this->SetBuffer("allowSync", false); 
                $this->SendDebug(__FUNCTION__, "Start UPDATE FROM ".$SenderID,0);
                //$this->SendDebug(__FUNCTION__, "Start UPDATE FROM ".$SenderID." with Message ".$Message."\r\n Data: ".print_r($Data, true),0);

                //get default settings from master
                $syncItems = json_decode($this->ReadPropertyString("Parameterlist"), true);
                $configMaster = json_decode($Data[1], true);
                foreach($syncItems as $key => $item){
                    $name = $item["name"];
                    if(!$item["sync"]) continue; //igonre all without sync
                    if(!array_key_exists($name, $configMaster)) continue; // ignore not in Master and Sub Items

                    $value = $configMaster[$name];
                    $json_value = json_decode($value, true);
                    $syncItems[$key]["org"] = $value;

                    if (json_last_error() === 0 && is_array($json_value)) {
                        //Json Update sync items
                        $updateItems = array();
                        foreach($json_value as $sub_key => $list_item){
                            if(!is_array($list_item)) continue;
                            $updateSubItems = array();

                            foreach($list_item as $l_key => $l_value){
                                $subItemName = $name."_".$l_key;
                                $index = array_search($subItemName, array_column($syncItems, 'name'));
                                if($index === false) continue;
                                if(!$syncItems[$index]["sync"]) continue;

                                $updateSubItems[$l_key] = $l_value;
                                //$this->SendDebug(__FUNCTION__, "Config-Master(Item) ". $subItemName . " => " . $l_value,0); 
                            }
                            $updateItems[] = $updateSubItems;
                        }

                        $syncItems[$key]["value"] = $updateItems;
                        //$this->SendDebug(__FUNCTION__, "Config-Master " .$name . "(" . $key . ") Value => " . json_encode($updateItems),0); 
                    }else{
                        $syncItems[$key]["value"] = $value;
                        //$this->SendDebug(__FUNCTION__, "Config-Master " .$name . " Value => " . $value,0); 
                    }
                }

                //update all other Modules
                $allSyncList = json_decode($this->ReadPropertyString("InstanceList"), true); 
                foreach($allSyncList as $syncSItem){
                    $instanceID = $syncSItem["InstanceID"];
                    if($instanceID == $SenderID) continue;
                    if(IPS_InstanceExists($instanceID) === False) continue;
                    if(IPS_GetInstance($instanceID)["ModuleInfo"]["ModuleID"] != $moduleID) continue;


                    $this->SendDebug(__FUNCTION__, "###Start Update ". IPS_GetObject($instanceID)["ObjectName"] . " (" . $instanceID . ") ###",0); 
                    $config = json_decode(IPS_GetConfiguration($instanceID), true);
                    $old_config = json_decode(IPS_GetConfiguration($instanceID), true);

                    foreach($config as $key => $value){
                        $index = array_search($key, array_column($syncItems, 'name'));
                        if($index === false) continue;
                        if(!$syncItems[$index]["sync"]) continue;
                        if(!array_key_exists("value", $syncItems[$index])) continue;

                        $newValue = $syncItems[$index]["value"];
                        $json_value = json_decode($value, true);
                        if (json_last_error() === 0 && is_array($json_value)) {
                            if(count($json_value) == 0){
                                //werte vom Orginal übertragen
                                if($config[$key] != $syncItems[$index]["org"]){
                                    $config[$key] = $syncItems[$index]["org"];
                                    $this->SendDebug(__FUNCTION__, "Write-Single-Config(Set Default) => ".  $syncItems[$index]["org"],0); 
                                }
                            }else{
                                //Json Update sync items
                                foreach($json_value as $sub_key => $list_item){
                                    if(!is_array($list_item)) continue;
        
                                    foreach($list_item as $l_key => $l_value){
                                        $subItemName = $key."_".$l_key;
                                        $index2 = array_search($subItemName, array_column($syncItems, 'name'));
                                        if($index2 === false) continue;
                                        if(!$syncItems[$index2]["sync"]) continue;
                                        if(!array_key_exists($sub_key, $newValue)) continue;
                                        if(!array_key_exists($l_key, $newValue[$sub_key])) continue;
        
                                        if($json_value[$sub_key][$l_key] != $newValue[$sub_key][$l_key]){
                                            $json_value[$sub_key][$l_key] = $newValue[$sub_key][$l_key];
                                            $this->SendDebug(__FUNCTION__, "Write-Single-Config(Item) ". $subItemName . " => " . $l_value . " to " . $newValue[$sub_key][$l_key],0); 
                                        }
                                    } 
                                }

                                if($config[$key] != json_encode($json_value)){
                                    $config[$key] = json_encode($json_value);
                                    $this->SendDebug(__FUNCTION__, "Write-Single-Config " .$name . "(" . $key . ") Value => " . json_encode($config[$key]),0); 
                                }
                            }
                        }else{
                            if($config[$key] != $newValue){
                                $config[$key] = $newValue;
                                $this->SendDebug(__FUNCTION__, "Write-Single-Config " .$key . " => " . $value ." to " . $newValue,0); 
                            }
                        }
                    }
                    
                    if($old_config != $config){
                        $this->SendDebug(__function__, "Write-Config " . IPS_GetObject($instanceID)["ObjectName"]. " (". $instanceID . ") =>" . json_encode($config), 0);
                        IPS_SetConfiguration($instanceID, json_encode($config));
                        IPS_ApplyChanges($instanceID); // Apply new configuration
                    }

                    $this->SendDebug(__FUNCTION__, "###End Update ". IPS_GetObject($instanceID)["ObjectName"] . " (" . $instanceID . ") ###",0); 
                }

                $allowSync = $this->SetBuffer("allowSync", true); 
            }
        }
    }

    public function changeModule($value){
        $colData = '[
            {
                "caption": "ID",
                "name": "ID",
                "width": "100px",
                "add": ""
            }, {
                "caption": "InstanceID",
                "name": "InstanceID", 
                "width": "auto",
                "add": 0,
                "edit": { 
                    "type": "SelectInstance",
                    "validModules": "[\"'.$value.'\"]"
                }
            }, {
                "caption": "Master",
                "name": "Master",
                "width": "120px",
                "add": false,
                "edit": { "type": "CheckBox" }
            }
        ]';

        $this->UpdateFormField("InstanceList", "columns", $colData);
        $this->UpdateFormField("Parameterlist", "values", json_encode($this->GetParameterListData($value)));
    }
    private Function GetModuleList(){
        $postData = array();
        $url = self::APILINK . "api/Modules.php?Type=getlist";

        $data = json_decode($this->GetWebData($url, $postData), true);
        $modulelist = array();

        if($data["success"]){
            $modulelist = $data["data"];
        }

        $this->SetBuffer("Modulelist", $modulelist);
        return $modulelist;
    }

    public function changeInstance($data){
        $this->UpdateFormField("InstanceList", "values", json_encode($this->GetInstanceListData($data)));
    }
    private function GetInstanceListData($data){
        $update_InstanceList = array();
        $moduleID = $this->ReadPropertyString("SelectType");
        foreach($data as $item){
            //print_r($item);
            $new_item = array();
            $new_item["ID"] = $item["InstanceID"];
            $new_item["InstanceID"] = $item["InstanceID"];
            if(array_key_exists("Master", $item)){
                $new_item["Master"] = $item["Master"];
            }else{
                $new_item["Master"] = false;
            }
            
            if(!IPS_InstanceExists($item["InstanceID"]) || IPS_GetInstance($item["InstanceID"])["ModuleInfo"]["ModuleID"] != $moduleID){
                $new_item["rowColor"] = "#FFC0C0";
            }

            $update_InstanceList[] = $new_item;
        }

        return $update_InstanceList;
    }

    private function GetParameterListData($moduleGUID, array $data = array()){
        $data = array();
        $moduleList = IPS_GetInstanceListByModuleID($moduleGUID);
        if(count($moduleList) == 0) return $data;

        $moduleID = $moduleList[0];
        $formData = json_decode(IPS_GetConfigurationForm($moduleID), true)["elements"];
        $parameterList = $this->LoadRecusiveFormData($formData)["data"];
        //echo json_encode($parameterList);
        
        //print_r($parameterList);
        foreach($parameterList as $key => $item){
            //prüfen ob in $data
            $item = array_change_key_case($item);
            if(empty($item["name"]) || (array_key_exists("type", $item) && strtolower($item["type"]) == "label")) continue;

            $col = array_column($data, 'name');
            $col = array_change_key_case($col);  
            $pos = array_search($item["name"], $col);
            if($pos === false){
                //add to items
                $data[] = $item;
                //echo $item['name'];
            }else{

            }
        }

        return $data;
    }

    private function LoadRecusiveFormData(array $arr, array $r_arr = array(), string $column_name = "", int $id = 1, int $parentID = 0){
        foreach ($arr as $key => $item) {
            $item = array_change_key_case($item);
            $addItem = true;
            if(array_key_exists("disableexport", $item) && $item["disableexport"] == true) {
                $addItem = false;
                continue;
            }
            
            if (array_key_exists("name", $item)){
                $name = $arr[$key]["name"];
                $is_column = false;
                $sync = true;
                $caption = "";

                if (array_key_exists("ignoreexport", $item) && $item["ignoreexport"] == true) {
                    $sync = false;
                }

                if (array_key_exists("caption", $item)) {
                    $caption = $item["caption"];
                }

                if(!empty($column_name)){
                    $name =  $column_name . "_" . $name;
                    $is_column = true;
                }

                if (!array_key_exists("items", $item)){
                    $r_item = array();
                    $r_item["id"] = $id;
                    $r_item["name"] = $name;
                    $r_item["caption"] = $caption;
                    $r_item["is_column"] = $is_column;
                    $r_item["no_item"] = false;
                    $r_item["sync"] = $sync;
                    if($parentID > 0) $r_item["parent"] = $parentID;

                    $r_arr[] = $r_item;
                }
            } 

            //items Recusive for Items
            if (array_key_exists("items", $item)){
                //if(array_key_exists("caption", $item)) $this->SendDebug(__function__, $item["caption"], 0);
                //$this->SendDebug(__function__, $item["type"], 0);

                $d = $this->LoadRecusiveFormData($item["items"], $r_arr, $column_name, $id++);
                $r_arr = $d["data"];
                $id = $d["id"];
            }elseif (array_key_exists("columns", $item)){
                if(array_key_exists("name", $item)){
                    $pID = $id;
                    $nID = $id + 1;
                    $d = $this->LoadRecusiveFormData($item["columns"], $r_arr, $item["name"], $nID, $pID);
                    $r_arr = $d["data"];
                    $id = $d["id"];
                }else{
                    $id++;
                }
            }else{
                $id++;
            }
        }

        return array("data"=>$r_arr, "id" => $id);
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