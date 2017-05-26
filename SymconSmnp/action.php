<?
    $instance = $_IPS['VARIABLE'];
    $value = $_IPS['VALUE'];
    $device_id = IPS_GetParent($instance);

    IPSWINSNMP_ChangeValue($device_id, $instance, $value)
?>