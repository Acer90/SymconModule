<?php
include_once (__DIR__ . '/../SymconJSLive/libs/JSLiveModule.php');

class SymconJSLiveCofiguartor extends WebHookModule {

    private const HUE = '';

    private const DECONZ = '';
    private const KNX = '';
    private const NANOLEAF = '';

    public function __construct($InstanceID)
    {
        parent::__construct($InstanceID, "JSLive");
    }

    public function Create() {
        //Never delete this line!
        parent::Create();

        $this->RegisterPropertyInteger("ImportCategoryID", 0);

        //we will wait until the kernel is ready
        $this->RegisterMessage(0, IPS_KERNELMESSAGE);
    }

    public function ApplyChanges() {
        //Never delete this line!
        parent::ApplyChanges();

        if (IPS_GetKernelRunlevel() !== KR_READY) {
            return;
        }

        $this->SetStatus(IS_ACTIVE);
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        switch ($Message) {
            case IM_CHANGESTATUS:
                if ($Data[0] === IS_ACTIVE) {
                    $this->ApplyChanges();
                }
                break;

            case IPS_KERNELMESSAGE:
                if ($Data[0] === KR_READY) {
                    $this->ApplyChanges();
                }
                break;

            default:
                break;
        }
    }

    /**
     * Liefert alle Geräte.
     *
     * @return array configlist all devices
     */
    private function Get_ListConfiguration()
    {
        $config_list = [];
        $location_id = $this->RequestDataFromParent('location_id');
        if ($location_id != '') {
            $GardenaInstanceIDList = IPS_GetInstanceListByModuleID('{3B073BE1-6556-037C-42FB-6311BC452C68}'); // Gardena Devices
            $snapshot = $this->RequestSnapshotBuffer(); // Get Snapshot
            $this->SendDebug('Gardena Config', $snapshot, 0);
            $payload = json_decode($snapshot, true);
            $counter = count($payload);
            if ($counter > 0) {
                $included = $payload['included'];
                foreach ($included as $device) {
                    $instanceID = 0;
                    $type = $device['type'];
                    if ($type == 'COMMON') {
                        $data = $this->GetDeviceType($device);
                        if(!empty($data))
                        {
                            $id = $data['id'];
                            $name = $data['name'];
                            $serial = $data['serial'];
                            $rf_link_state = $data['rf_link_state'];
                            $model_type = $data['model_type'];
                            foreach ($GardenaInstanceIDList as $GardenaInstanceID) {
                                if (IPS_GetProperty($GardenaInstanceID, 'id') == $id) { // todo  InstanceInterface is not available
                                    $instanceID = $GardenaInstanceID;
                                }
                            }
                            $config_list[] = ["instanceID" => $instanceID,
                                "name" => $name,
                                "serial" => $serial,
                                "rf_link_state" => $rf_link_state,
                                "model_type" => $model_type,
                                "create" => [
                                    [
                                        "moduleID" => "{3B073BE1-6556-037C-42FB-6311BC452C68}",
                                        "configuration" => [
                                            "id" => $id,
                                            "name" => $name,
                                            "serial" => $serial,
                                            "model_type" => $model_type,
                                        ],
                                        "location" => $this->SetLocation()
                                    ]
                                ]
                            ];
                        }
                    }
                }
            }
        }
        return $config_list;
    }

    private function SetLocation()
    {
        $category = $this->ReadPropertyInteger("ImportCategoryID");
        $tree_position[] = IPS_GetName($category);
        $parent = IPS_GetObject($category)['ParentID'];
        $tree_position[] = IPS_GetName($parent);
        do {
            $parent = IPS_GetObject($parent)['ParentID'];
            $tree_position[] = IPS_GetName($parent);
        } while ($parent > 0);
        // delete last key
        end($tree_position);
        $lastkey = key($tree_position);
        unset($tree_position[$lastkey]);
        // reverse array
        $tree_position = array_reverse($tree_position);
        return $tree_position;
    }

    /***********************************************************
     * Configuration Form
     ***********************************************************/

    /**
     * build configuration form
     * @return string
     */
    public function GetConfigurationForm()
    {
        // return current form
        $Form = json_encode([
            'elements' => $this->FormHead(),
            'actions' => $this->FormActions(),
            'status' => $this->FormStatus()
        ]);
        $this->SendDebug('FORM', $Form, 0);
        $this->SendDebug('FORM', json_last_error_msg(), 0);
        return $Form;
    }

    /**
     * return form configurations on configuration step
     * @return array
     */
    protected function FormHead()
    {
        $form = [
            [
                'type' => 'Label',
                'caption' => 'category for color picker devices'
            ],
            [
                'name' => 'ImportCategoryID',
                'type' => 'SelectCategory',
                'caption' => 'category color pickera devices'
            ],
            [
                'name' => 'ColorPickerConfiguration',
                'type' => 'Configurator',
                'visible' => true,
                'rowCount' => 20,
                'add' => false,
                'delete' => false,
                'sort' => [
                    'column' => 'name',
                    'direction' => 'ascending'
                ],
                'columns' => [
                    [
                        'caption' => 'ID',
                        'name' => 'id',
                        'width' => '200px',
                        'visible' => false
                    ],
                    [
                        'name' => 'name',
                        'caption' => 'name',
                        'width' => 'auto'
                    ],
                    [
                        'name' => 'serial',
                        'caption' => 'serial',
                        'width' => '150px'
                    ],
                    [
                        'name' => 'rf_link_state',
                        'caption' => 'rf link state',
                        'width' => '150px'
                    ],
                    [
                        'name' => 'model_type',
                        'caption' => 'model type',
                        'width' => '300px'
                    ]
                ],
                'values' => $this->Get_ListConfiguration()
            ]
        ];
        return $form;
    }

    /**
     * return form actions by token
     * @return array
     */
    protected function FormActions()
    {
        $form = [
            [
                'type' => 'Label',
                'visible' => true,
                'caption' => 'Read configuration:'
            ],
            [
                'type' => 'Button',
                'visible' => true,
                'caption' => 'Read configuration',
                'onClick' => 'SymconJSLive_GetConfiguration($id);'
            ]
        ];
        return $form;
    }

    /**
     * return from status
     * @return array
     */
    protected function FormStatus()
    {
        $form = [
            [
                'code' => IS_CREATING,
                'icon' => 'inactive',
                'caption' => 'Creating instance.'
            ],
            [
                'code' => IS_ACTIVE,
                'icon' => 'active',
                'caption' => 'configuration valid.'
            ],
            [
                'code' => IS_INACTIVE,
                'icon' => 'inactive',
                'caption' => 'interface closed.'
            ],
            [
                'code' => 201,
                'icon' => 'inactive',
                'caption' => 'Please follow the instructions.'
            ],
            [
                'code' => 202,
                'icon' => 'error',
                'caption' => 'no category selected.'
            ]
        ];

        return $form;
    }

}
