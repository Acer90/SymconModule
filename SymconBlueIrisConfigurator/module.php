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
            $output['elements'][0]['values'] = json_decode($this->GetBuffer('Cams'), true);
            return json_encode($output);
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
                    return $id;
                }
            }
            return 0;
        }


    }
