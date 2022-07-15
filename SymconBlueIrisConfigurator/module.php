<?php
    // Klassendefinition
    class SymconBlueIrisConfigurator extends IPSModule {
        public function Create() {
            parent::Create();

            $this->ConnectParent("{E138AFDC-D1E0-B462-A5E5-AF24F57D4686}");

            $this->SetBuffer('Cams', '{}');
        }

        public function ApplyChanges()
        {
            //Never delete this line!
            parent::ApplyChanges();

            $this->SetStatus("102");
        }

        public function GetConfigurationForm() {
            $output = json_decode(file_get_contents(__DIR__ . '/form.json'), true);
            $this->UpdateCams();

            $values = array();
            $item = array();
            $item["id"] = 1;
            $arr = IPS_GetInstanceListByModuleID('{CDFC7E83-C425-E923-9B6F-ECB22F3DFCC9}');
            //print_r($InstanceIDs);
            if(is_array($arr) && count($arr) > 0){
                $item["instanceID"] = $arr[0];
            }
            $item["name"] = "BlueIris System";
            $item["shortName"] = "";
            $item["ptz"] = "";
            $item["create"]["moduleID"] = "{CDFC7E83-C425-E923-9B6F-ECB22F3DFCC9}";
            $item["create"]["configuration"]["GetLog"] = false; //hier muss etwas definiert werden, sonnst kommt es bei symcon zu einen Bug!

            $values[] = $item;

            $item = array();
            $item["id"] = 2;
            $item["name"] = $this->Translate("Cameras");
            $item["shortName"] = "";
            $item["ptz"] = "";

            $values[] = $item;

            $cams = json_decode($this->GetBuffer('Cams'), true);;
            $values  = array_merge($values, $cams);

            $output['elements'][0]['values'] = $values;
            return json_encode($output);
        }

        private function UpdateCams(){
            $camsData = json_decode($this->GetBuffer('Cams'), true);

            for($i = 0; $i < count($camsData); $i++){
                $sname = $camsData[$i]["shortName"];

                if(empty($sname)) continue;

                $newInstanceID = $this->GetDeviceInstanceID($sname);

                if($camsData[$i]["instanceID"] != $newInstanceID){
                    $camsData[$i]["instanceID"] = $newInstanceID;
                    $this->SendDebug(__FUNCTION__, 'InstanceID for ShortName ' . $sname . ' updated => ' . $newInstanceID, 0);
                }
            }

            $this->SetBuffer('Cams', json_encode($camsData));
        }

        public function ReceiveData($JSONString)
        {
            $rData = json_decode($JSONString, true);
            $buffer = json_decode($rData["Buffer"], true);
            $camsData = json_decode($this->GetBuffer('Cams'), true);

            //update Buffer
            if($buffer["cmd"] == "CamList"){
                foreach ($buffer["payload"] as $item){

                    $key = array_search($item["shortName"], array_column($camsData, 'shortName'));

                    if($key === false){
                        //datensatz neu im configurator anlegen
                        $s_Data = array();
                        $s_Data = $item;
                        $s_Data["instanceID"] = $this->GetDeviceInstanceID($item["shortName"]);
                        $s_Data["create"]["moduleID"] = "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}";
                        $s_Data["create"]["configuration"]["ShortName"] = $item["shortName"];
                        $s_Data["create"]["configuration"]["PTZ"] = $item["ptz"];
                        $s_Data["parent"] = 2;

                        $camsData[] = $s_Data;
                    }
                }
            }

            //update buffer
            $this->SetBuffer('Cams', json_encode($camsData));
            //$this->SetBuffer('Cams', '{}');
        }

        private function GetDeviceInstanceID($shortName)
        {
            $InstanceIDs = IPS_GetInstanceListByModuleID('{5308D185-A3D2-42D0-B6CE-E9D3080CE184}');
            foreach ($InstanceIDs as $id) {
                if (IPS_GetProperty($id, 'ShortName') == $shortName) {
                    $this->SendDebug(__FUNCTION__, 'Modul with ShortName ' . $shortName . ' found => ' . $id, 0);
                    return $id;
                }
            }
            return 0;
        }


    }
