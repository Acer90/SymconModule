<?php
    // Klassendefinition
    class SymconHomeAssistantConfigurator extends IPSModule {
        public function Create() {
            parent::Create();

            $this->ConnectParent("{2A23C9D6-AB2C-E818-3D51-E29E446FAF69}");

            $this->SetBuffer('Devices', '{}');
        }

        public function ApplyChanges()
        {
            //Never delete this line!
            parent::ApplyChanges();

            $this->SetStatus("102");
        }

        public function GetConfigurationForm() {
            $output = json_decode(file_get_contents(__DIR__ . '/form.json'), true);
            $devices = json_decode($this->GetBuffer('Devices'), true);

            $values = array();
            $item = array();

            foreach($devices as $device){
                $InstanceID = $this->GetDeviceInstanceID($device);

                $item["name"] = $device;
                $item["entity_id"] = $device;
                $item["instanceID"] = $InstanceID;
                $item["create"]["moduleID"] = "{FD2A7D53-5363-39DD-0C93-A96BAD384B80}";
                $item["create"]["configuration"]["entity_id"] = $device;
                $values[] = $item;
            }

            $output['elements'][0]['values'] = $values;
            return json_encode($output);
        }

        public function ReceiveData($JSONString)
        {
            $rData = json_decode($JSONString, true);
            $buffer = json_decode($rData["Buffer"], true);
            $this->SendDebug(__FUNCTION__, $rData["Buffer"], 0);
            $Devices = json_decode($this->GetBuffer('Devices'), true);

            //update Buffer
            if($buffer["cmd"] == "addDevice"){
                $entity_id = $buffer["entity_id"];

                if(!in_array($entity_id, $Devices)){
                    $Devices[] = $entity_id;
                }
            }

            //update buffer
            $this->SetBuffer('Devices', json_encode($Devices));
        }

        private function GetDeviceInstanceID($entity_id)
        {
            $InstanceIDs = IPS_GetInstanceListByModuleID('{FD2A7D53-5363-39DD-0C93-A96BAD384B80}');
            foreach ($InstanceIDs as $id) {
                if (IPS_GetProperty($id, 'entity_id') == $entity_id) {
                    $this->SendDebug(__FUNCTION__, 'Modul with ShortName ' . $entity_id . ' found => ' . $id, 0);
                    return $id;
                }
            }
            return 0;
        }


    }
