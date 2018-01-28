<?
	
if($_IPS['SENDER'] == "RunScript")
{
	$IPS_SENDER = $_IPS['SENDER'];
	$IPS_SELF = $_IPS['SELF'];
	$IPS_THREAD = $_IPS['THREAD'];
	foreach($_IPS as $key => $val)
	{
		if(!in_array($key, Array("SENDER", "SELF", "THREAD")))
		{
			${$key} = $val;
		}
	}
} else {
	foreach($_IPS as $key => $val)
	{
		${"IPS_".$key} = $val;
	}
}
 
switch($_IPS['SENDER'])
{
	case "HeatingControl":
		$HC_INSTANCES = $_IPS['INSTANCES'];
		$HC_INVERTS = $_IPS['INVERTS'];
		$HC_VALUE = $_IPS['VALUE'];
		break;
	case "ShutterControl":
		$SC_INSTANCE = $_IPS['INSTANCE'];
		$SC_INSTANCE2 = $_IPS['INSTANCE2'];
		$SC_DIRECTION = $_IPS['DIRECTION'];
		$SC_DURATION = $_IPS['DURATION'];
		break;
	case "ISDN":
		$ISDN_CONNECTION = $_IPS['CONNECTION'];
		$ISDN_EVENT = $_IPS['EVENT'];
		$ISDN_DATA = $_IPS['DATA'];
		break;
	case "WebFront":
		//$REMOTE_ADDR = $_IPS['REMOTE_ADDR'];
		break;
	case "Designer":
		//$REMOTE_ADDR = $_IPS['REMOTE_ADDR'];
		//$REMOTE_HOST = $_IPS['REMOTE_HOST'];
		break;
}

if (!function_exists('IPS_IsPersistent'))	
{
	function IPS_IsPersistent($ObjectID)
	{
		return true;
	}
}
 
if (!function_exists('IPS_SetEventCyclicDateBounds')) {

	function IPS_SetEventCyclicDateBounds($EventID, $FromDate, $ToDate)
	{
		$ret = true;
		if($FromDate == 0) {
			$ret = $ret & IPS_SetEventCyclicDateFrom($EventID, 0, 0, 0);
		} else {
			$ret = $ret & IPS_SetEventCyclicDateFrom($EventID, (int)date("d", $FromDate), (int)date("m", $FromDate), (int)date("Y", $FromDate));
		}
		if($ToDate == 0) {
			$ret = $ret & IPS_SetEventCyclicDateTo($EventID, 0, 0, 0);
		} else {
			$ret = $ret & IPS_SetEventCyclicDateTo($EventID, (int)date("d", $ToDate), (int)date("m", $ToDate), (int)date("Y", $ToDate));
		}	
		return $ret;
	}

}

if (!function_exists('IPS_SetEventCyclicTimeBounds')) {

	function IPS_SetEventCyclicTimeBounds($EventID, $FromTime, $ToTime)
	{
		$ret = true;
		if($FromTime == 0) {
			$ret = $ret & IPS_SetEventCyclicTimeFrom($EventID, 0, 0, 0);
		} else {
			$ret = $ret & IPS_SetEventCyclicTimeFrom($EventID, (int)date("H", $FromTime), (int)date("i", $FromTime), (int)date("s", $FromTime));
		}
		if($ToTime == 0) {
			$ret = $ret & IPS_SetEventCyclicTimeTo($EventID, 0, 0, 0);
		} else {
			$ret = $ret & IPS_SetEventCyclicTimeTo($EventID, (int)date("H", $ToTime), (int)date("i", $ToTime), (int)date("s", $ToTime));
		}	
		return $ret;		
	}	

}
 
if (!function_exists('IPS_GetEventCompatibility')) {

	function IPS_GetEventCompatibility($EventID)
	{
		$event = IPS_GetEvent($EventID);
		$x = $event['CyclicDateFrom'];
		$event['CyclicDateFrom'] = mktime(0, 0, 0, $x['Month'], $x['Day'], $x['Year']);
		$x = $event['CyclicDateTo'];
		$event['CyclicDateTo'] = mktime(0, 0, 0, $x['Month'], $x['Day'], $x['Year']);
		$x = $event['CyclicTimeFrom'];
		$event['CyclicTimeFrom'] = mktime($x['Hour'], $x['Minute'], $x['Second']);
		$x = $event['CyclicTimeTo'];
		$event['CyclicTimeTo'] = mktime($x['Hour'], $x['Minute'], $x['Second']);
		return $event;
	}

}  
 
if (!function_exists('IPS_StatusVariableExists'))	
{
	function IPS_StatusVariableExists($InstanceID, $VariableIdent)
	{
		return !(@IPS_GetObjectIDByIdent($VariableIdent, $InstanceID) === false);
	}
}

if (!function_exists('IPS_GetStatusVariable'))	
{
	function IPS_GetStatusVariable($InstanceID, $VariableIdent)
	{
	   $id = IPS_GetObjectIDByIdent($VariableIdent, $InstanceID);
	   $v = IPS_GetVariable($id);
	   return Array(
			"VariableID" => $id,
			"VariableIdent" => $VariableIdent,
			"VariableName" => "N/A",
			"VariablePosition" => 0,
			"VariableProfile" => $v['VariableProfile'],
			"VariableType" => $v['VariableValue']['ValueType'],
			"VariableHasAction" => ($v['VariableAction'] > 0),
			"VariableUseAction" => ($v['VariableAction'] > 0)
		);
	}
}

if (!function_exists('IPS_GetStatusVariableIdents'))	
{
	function IPS_GetStatusVariableIdents($InstanceID)
	{
		$r = Array();
		$cids = IPS_GetChildrenIDs($InstanceID);
		foreach($cids as $cid)
		{
		   $o = IPS_GetObject($cid);
		   if($o['ObjectIdent'] != "")
			  $r[] = $o['ObjectIdent'];
		}
		return $r;
	}
}

if (!function_exists('IPS_GetStatusVariableID'))	
{
	function IPS_GetStatusVariableID($InstanceID, $VariableIdent)
	{
		$migrateIdents = Array(
			"F05_StatusVar" => "Status",
			"F10_TemperatureVar" => "Temperature",
			"F12_Var0" => "Status0",
			"F12_Var1" => "Status1",
			"F1D_CounterVar1" => "Counter1",
			"F1D_CounterVar2" => "Counter2",
			"F20_Var0" => "Port0",
			"F20_Var1" => "Port1",
			"F20_Var2" => "Port2",
			"F20_Var3" => "Port3",
			"F26_TemperatureVar" => "Temperature",
			"F26_VDDVar" => "VDD",
			"F26_VADVar" => "VAD",
			"F26_XSENSVar" => "XSENS",
			"F28_TemperatureVar" => "Temperature",
			"F29_Var0" => "Status0",
			"F29_LatchVar0" => "Latch0",
			"F29_Var1" => "Status1",
			"F29_LatchVar1" => "Latch1",
			"F29_Var2" => "Status2",
			"F29_LatchVar2" => "Latch2",
			"F29_Var3" => "Status3",
			"F29_LatchVar3" => "Latch3",
			"F29_Var4" => "Status4",
			"F29_LatchVar4" => "Latch4",
			"F29_Var5" => "Status5",
			"F29_LatchVar5" => "Latch5",
			"F29_Var6" => "Status6",
			"F29_LatchVar6" => "Latch6",
			"F29_Var7" => "Status7",
			"F29_LatchVar7" => "Latch7",
			"F2C_PositionVar" => "Position",
			"F3A_Var0" => "Status0",
			"F3A_Var1" => "Status1"
		);
		
		if(isset($migrateIdents[$VariableIdent]))
			$VariableIdent = $migrateIdents[$VariableIdent];
		
		return IPS_GetObjectIDByIdent($VariableIdent, $InstanceID);
	}		
}

if (!function_exists('IPS_SetLinkChildID'))	
{
	function IPS_SetLinkChildID($LinkID, $TargetID)
	{
		return IPS_SetLinkTargetID($LinkID, $TargetID);
		//trigger_error("The function SetLinkChildID was renamed and is now deprecated. Use SetLinkTargetID instead.", E_USER_WARNING);
	}		
}

if (!function_exists('IPS_HasInstanceParent'))	
{
	function IPS_HasInstanceParent($InstanceID)
	{
		return (IPS_GetInstance($InstanceID)['ConnectionID'] > 0);
	}		
}

if (!function_exists('IPS_GetInstanceParentID'))	
{
	function IPS_GetInstanceParentID($InstanceID)
	{
		return IPS_GetInstance($InstanceID)['ConnectionID'];
	}		
}

if (!function_exists('IPS_HasInstanceChildren'))	
{
	function IPS_HasInstanceChildren($InstanceID)
	{
		return sizeof(IPS_GetInstanceChildrenIDs($InstanceID)) > 0;
	}		
}

if (!function_exists('IPS_GetInstanceChildrenIDs'))	
{
	function IPS_GetInstanceChildrenIDs($InstanceID)
	{
		$result = Array();
		$InstanceIDs = IPS_GetInstanceList();
		foreach($InstanceIDs as $IID)
			if(IPS_GetInstance($IID)['ConnectionID'] == $InstanceID)
				$result[] = $IID;
	}		
}

if (!function_exists('LCN_GetStatus'))	
{
	function LCN_GetStatus($InstanceID)
	{
		$i = IPS_GetInstance($InstanceID);
		switch($i['InstanceStatus'])
		{
			case 102: //LCN_Loggedin
				return 2;
			case 201: //LCN_Connected
				return 1;
			case 202: //LCN_Disconnected
				return 0;
			case 203: //LCN_AuthError
				return 3;
			case 204: //LCN_LicenseError
				return 4;
			case 205: //LCN_UnknownError
				return 5;
		}
		return -1;
	}		
}

//Instance configuration
if (!function_exists('CSCK_SetOpen'))	
{
	function CSCK_SetOpen($InstanceID, $Value)
	{
		IPS_SetProperty($InstanceID, 'Open', $Value);
	}
}

if (!function_exists('CSCK_GetOpen'))	
{
	function CSCK_GetOpen($InstanceID)
	{
		return IPS_GetProperty($InstanceID, 'Open');
	}
}

if (!function_exists('CSCK_SetHost'))	
{
	function CSCK_SetHost($InstanceID, $Value)
	{
		IPS_SetProperty($InstanceID, 'Host', $Value);
	}
}

if (!function_exists('CSCK_GetHost'))	
{
	function CSCK_GetHost($InstanceID)
	{
		return IPS_GetProperty($InstanceID, 'Host');
	}
}

if (!function_exists('CSCK_SetPort'))	
{
	function CSCK_SetPort($InstanceID, $Value)
	{
		IPS_SetProperty($InstanceID, 'Port', $Value);
	}
}

if (!function_exists('CSCK_GetPort'))	
{
	function CSCK_GetPort($InstanceID)
	{
		return IPS_GetProperty($InstanceID, 'Port');
	}
}

if (!function_exists('SSCK_SetOpen'))	
{
	function SSCK_SetOpen($InstanceID, $Value)
	{
		IPS_SetProperty($InstanceID, 'Open', $Value);
	}
}

if (!function_exists('SSCK_GetOpen'))	
{
	function SSCK_GetOpen($InstanceID)
	{
		return IPS_GetProperty($InstanceID, 'Open');
	}
}

if (!function_exists('SSCK_SetPort'))	
{
	function SSCK_SetPort($InstanceID, $Value)
	{
		IPS_SetProperty($InstanceID, 'Port', $Value);
	}
}

if (!function_exists('SSCK_GetPort'))	
{
	function SSCK_GetPort($InstanceID)
	{
		return IPS_GetProperty($InstanceID, 'Port');
	}
}

if (!function_exists('WWWReader_SetPage'))
{
	function WWWReader_SetPage($InstanceID, $URL)
 	{
		IPS_SetProperty($InstanceID, 'URL', $URL);
	}
}

if (!function_exists('WWWReader_SetUseTimer'))
{
	function WWWReader_SetUseTimer($InstanceID, $Enabled)
 	{
		IPS_SetProperty($InstanceID, 'Active', $Enabled);
	}
}

if (!function_exists('WWWReader_SetInterval'))
{
	function WWWReader_SetInterval($InstanceID, $Seconds)
 	{
		IPS_SetProperty($InstanceID, 'Interval', $Seconds);
	}
}

if (!function_exists('WWWReader_SetUseProxy'))
{
	function WWWReader_SetUseProxy($InstanceID, $Enabled)
 	{
		IPS_SetProperty($InstanceID, 'UseProxy', $Enabled);
	}
}

if (!function_exists('WWWReader_SetProxyHost'))
{
	function WWWReader_SetProxyHost($InstanceID, $Host)
 	{
		IPS_SetProperty($InstanceID, 'ProxyHost', $Host);
	}
}

if (!function_exists('WWWReader_SetProxyPort'))
{
	function WWWReader_SetProxyPort($InstanceID, $Port)
 	{
		IPS_SetProperty($InstanceID, 'ProxyPort', $Port);
	}
}

if (!function_exists('WWWReader_SetProxyUsername'))
{
	function WWWReader_SetProxyUsername($InstanceID, $Username)
 	{
		IPS_SetProperty($InstanceID, 'ProxyUsername', $Username);
	}
}

if (!function_exists('WWWReader_SetProxyPassword'))
{
	function WWWReader_SetProxyPassword($InstanceID, $Password)
 	{
		IPS_SetProperty($InstanceID, 'ProxyPassword', $Password);
	}
}

if (!function_exists('WWWReader_SetUseBasicAuth'))
{
	function WWWReader_SetUseBasicAuth($InstanceID, $Enabled)
 	{
		IPS_SetProperty($InstanceID, 'UseBasicAuth', $Enabled);
	}
}

if (!function_exists('WWWReader_SetAuthUsername'))
{
	function WWWReader_SetAuthUsername($InstanceID, $Username)
 	{
		IPS_SetProperty($InstanceID, 'AuthUsername', $Username);
	}
}

if (!function_exists('WWWReader_SetAuthPassword'))
{
	function WWWReader_SetAuthPassword($InstanceID, $Password)
 	{
		IPS_SetProperty($InstanceID, 'AuthPassword', $Password);
	}
}

if (!function_exists('WWWReader_GetPage'))
{
	function WWWReader_GetPage($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'URL');
	}
}

if (!function_exists('WWWReader_GetUseTimer'))
{
	function WWWReader_GetUseTimer($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Active');
	}
}

if (!function_exists('WWWReader_GetInterval'))
{
	function WWWReader_GetInterval($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Interval');
	}
}

if (!function_exists('WWWReader_GetUseProxy'))
{
	function WWWReader_GetUseProxy($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'UseProxy');
	}
}

if (!function_exists('WWWReader_GetProxyHost'))
{
	function WWWReader_GetProxyHost($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'ProxyHost');
	}
}

if (!function_exists('WWWReader_GetProxyPort'))
{
	function WWWReader_GetProxyPort($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'ProxyPort');
	}
}

if (!function_exists('WWWReader_GetProxyUsername'))
{
	function WWWReader_GetProxyUsername($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'ProxyUsername');
	}
}

if (!function_exists('WWWReader_GetProxyPassword'))
{
	function WWWReader_GetProxyPassword($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'ProxyPassword');
	}
}

if (!function_exists('WWWReader_GetUseBasicAuth'))
{
	function WWWReader_GetUseBasicAuth($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'UseBasicAuth');
	}
}

if (!function_exists('WWWReader_GetAuthUsername'))
{
	function WWWReader_GetAuthUsername($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'AuthUsername');
	}
}

if (!function_exists('WWWReader_GetAuthPassword'))
{
	function WWWReader_GetAuthPassword($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'AuthPassword');
	}
}

if (!function_exists('WWWReader_RetrievePage'))
{
	function WWWReader_RetrievePage($InstanceID, $URL)
 	{
		return Sys_GetURLContent($URL);
	}
}

if (!function_exists('WWWReader_UpdatePage'))
{
	function WWWReader_UpdatePage($InstanceID)
 	{
		return WWW_UpdatePage($InstanceID);
	}
}

if (!function_exists('COMPort_SetBaudRate'))
{
	function COMPort_SetBaudRate($InstanceID, $BaudRate)
 	{
		IPS_SetProperty($InstanceID, 'BaudRate', $BaudRate);
	}
}

if (!function_exists('COMPort_SetStopBits'))
{
	function COMPort_SetStopBits($InstanceID, $StopBits)
 	{
		IPS_SetProperty($InstanceID, 'StopBits', $StopBits);
	}
}

if (!function_exists('COMPort_SetDataBits'))
{
	function COMPort_SetDataBits($InstanceID, $DataBits)
 	{
		IPS_SetProperty($InstanceID, 'DataBits', $DataBits);
	}
}

if (!function_exists('COMPort_SetParity'))
{
	function COMPort_SetParity($InstanceID, $Parity)
 	{
		IPS_SetProperty($InstanceID, 'Parity', $Parity);
	}
}

if (!function_exists('COMPort_GetBaudRate'))
{
	function COMPort_GetBaudRate($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'BaudRate');
	}
}

if (!function_exists('COMPort_GetStopBits'))
{
	function COMPort_GetStopBits($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'StopBits');
	}
}

if (!function_exists('COMPort_GetDataBits'))
{
	function COMPort_GetDataBits($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DataBits');
	}
}

if (!function_exists('COMPort_GetParity'))
{
	function COMPort_GetParity($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Parity');
	}
}

if (!function_exists('COMPort_GetDTR'))
{
	function COMPort_GetDTR($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DTR');
	}
}

if (!function_exists('COMPort_GetRTS'))
{
	function COMPort_GetRTS($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'RTS');
	}
}

if (!function_exists('COMPort_SetOpen'))
{
	function COMPort_SetOpen($InstanceID, $Open)
 	{
		IPS_SetProperty($InstanceID, 'Open', $Open);
	}
}

if (!function_exists('COMPort_SetPort'))
{
	function COMPort_SetPort($InstanceID, $Port)
 	{
		IPS_SetProperty($InstanceID, 'Port', $Port);
	}
}

if (!function_exists('COMPort_GetOpen'))
{
	function COMPort_GetOpen($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Open');
	}
}

if (!function_exists('COMPort_GetPort'))
{
	function COMPort_GetPort($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Port');
	}
}

if (!function_exists('COMPort_GetDevices'))
{
	function COMPort_GetDevices($InstanceID)
 	{
		$result = Array();
		$json = json_decode(IPS_GetConfigurationForm($InstanceID));
		foreach($json->elements as $element)
		{
			if(isset($element->name) && ($element->name == "Port"))
			{
				foreach($element->options as $option)
					$result[] = $option->value;
			}
		}
		return $result;
	}
}

if (!function_exists('COMPort_SendText'))
{
	function COMPort_SendText($InstanceID, $Data)
 	{
		return SPRT_SendText($InstanceID, $Data);
	}
}

if (!function_exists('COMPort_SetDTR'))
{
	function COMPort_SetDTR($InstanceID, $OnOff)
 	{
		return SPRT_SetDTR($InstanceID, $OnOff);
	}
}

if (!function_exists('COMPort_SetRTS'))
{
	function COMPort_SetRTS($InstanceID, $OnOff)
 	{
		return SPRT_SetRTS($InstanceID, $OnOff);
	}
}

if (!function_exists('COMPort_SetDTRFlowControl'))
{
	function COMPort_SetDTRFlowControl($InstanceID, $Mode)
 	{
		return SPRT_SetDTRFlowControl($InstanceID, $Mode);
	}
}

if (!function_exists('COMPort_SetRTSFlowControl'))
{
	function COMPort_SetRTSFlowControl($InstanceID, $Mode)
 	{
		return SPRT_SetRTSFlowControl($InstanceID, $Mode);
	}
}

if (!function_exists('COMPort_SetBreak'))
{
	function COMPort_SetBreak($InstanceID, $OnOff)
 	{
		return SPRT_SetBreak($InstanceID, $OnOff);
	}
}

if (!function_exists('SSCK_SetLimit'))
{
	function SSCK_SetLimit($InstanceID, $Limit)
 	{
		IPS_SetProperty($InstanceID, 'Limit', $Limit);
	}
}

if (!function_exists('SSCK_GetLimit'))
{
	function SSCK_GetLimit($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Limit');
	}
}

if (!function_exists('USCK_SetOpen'))
{
	function USCK_SetOpen($InstanceID, $Open)
 	{
		IPS_SetProperty($InstanceID, 'Open', $Open);
	}
}

if (!function_exists('USCK_SetBindIPAddress'))
{
	function USCK_SetBindIPAddress($InstanceID, $Address)
 	{
		IPS_SetProperty($InstanceID, 'BindIPAddress', $Address);
	}
}

if (!function_exists('USCK_SetBindPort'))
{
	function USCK_SetBindPort($InstanceID, $Port)
 	{
		IPS_SetProperty($InstanceID, 'BindPort', $Port);
	}
}

if (!function_exists('USCK_SetHost'))
{
	function USCK_SetHost($InstanceID, $Host)
 	{
		IPS_SetProperty($InstanceID, 'Host', $Host);
	}
}

if (!function_exists('USCK_SetPort'))
{
	function USCK_SetPort($InstanceID, $Port)
 	{
		IPS_SetProperty($InstanceID, 'Port', $Port);
	}
}

if (!function_exists('USCK_GetOpen'))
{
	function USCK_GetOpen($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Open');
	}
}

if (!function_exists('USCK_GetBindIPAddress'))
{
	function USCK_GetBindIPAddress($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'BindIPAddress');
	}
}

if (!function_exists('USCK_GetBindPort'))
{
	function USCK_GetBindPort($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'BindPort');
	}
}

if (!function_exists('USCK_GetHost'))
{
	function USCK_GetHost($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Host');
	}
}

if (!function_exists('USCK_GetPort'))
{
	function USCK_GetPort($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Port');
	}
}

if (!function_exists('FTDI_SetBaudRate'))
{
	function FTDI_SetBaudRate($InstanceID, $BaudRate)
 	{
		IPS_SetProperty($InstanceID, 'BaudRate', $BaudRate);
	}
}

if (!function_exists('FTDI_SetStopBits'))
{
	function FTDI_SetStopBits($InstanceID, $StopBits)
 	{
		IPS_SetProperty($InstanceID, 'StopBits', $StopBits);
	}
}

if (!function_exists('FTDI_SetDataBits'))
{
	function FTDI_SetDataBits($InstanceID, $DataBits)
 	{
		IPS_SetProperty($InstanceID, 'DataBits', $DataBits);
	}
}

if (!function_exists('FTDI_SetParity'))
{
	function FTDI_SetParity($InstanceID, $Parity)
 	{
		IPS_SetProperty($InstanceID, 'Parity', $Parity);
	}
}

if (!function_exists('FTDI_GetBaudRate'))
{
	function FTDI_GetBaudRate($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'BaudRate');
	}
}

if (!function_exists('FTDI_GetStopBits'))
{
	function FTDI_GetStopBits($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'StopBits');
	}
}

if (!function_exists('FTDI_GetDataBits'))
{
	function FTDI_GetDataBits($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DataBits');
	}
}

if (!function_exists('FTDI_GetParity'))
{
	function FTDI_GetParity($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Parity');
	}
}

if (!function_exists('FTDI_SetOpen'))
{
	function FTDI_SetOpen($InstanceID, $Open)
 	{
		IPS_SetProperty($InstanceID, 'Open', $Open);
	}
}

if (!function_exists('FTDI_SetPort'))
{
	function FTDI_SetPort($InstanceID, $Port)
 	{
		IPS_SetProperty($InstanceID, 'Port', $Port);
	}
}

if (!function_exists('FTDI_GetOpen'))
{
	function FTDI_GetOpen($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Open');
	}
}

if (!function_exists('FTDI_GetPort'))
{
	function FTDI_GetPort($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Port');
	}
}

if (!function_exists('FTDI_GetDevices'))
{
	function FTDI_GetDevices($InstanceID)
 	{
		$result = Array();
		$json = json_decode(IPS_GetConfigurationForm($InstanceID));
		foreach($json->elements as $element)
		{
			if(isset($element->name) && ($element->name == "Port"))
			{
				foreach($element->options as $option)
					$result[] = $option->value;
			}
		}
		return $result;
	}
}

if (!function_exists('HID_GetOpen'))
{
	function HID_GetOpen($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Open');
	}
}

if (!function_exists('HID_SetOpen'))
{
	function HID_SetOpen($InstanceID, $Open)
 	{
		IPS_SetProperty($InstanceID, 'Open', $Open);
	}
}

if (!function_exists('HID_SetDeviceSerial'))
{
	function HID_SetDeviceSerial($InstanceID, $DeviceSerial)
 	{
		IPS_SetProperty($InstanceID, 'DeviceSerial', $DeviceSerial);
	}
}

if (!function_exists('HID_SetDeviceVendorID'))
{
	function HID_SetDeviceVendorID($InstanceID, $DeviceVendorID)
 	{
		IPS_SetProperty($InstanceID, 'DeviceVendorID', $DeviceVendorID);
	}
}

if (!function_exists('HID_SetDeviceProductID'))
{
	function HID_SetDeviceProductID($InstanceID, $DeviceProductID)
 	{
		IPS_SetProperty($InstanceID, 'DeviceProductID', $DeviceProductID);
	}
}

if (!function_exists('HID_GetPort'))
{
	function HID_GetPort($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Port');
	}
}

if (!function_exists('HID_GetDeviceSerial'))
{
	function HID_GetDeviceSerial($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DeviceSerial');
	}
}

if (!function_exists('HID_GetDeviceVendorID'))
{
	function HID_GetDeviceVendorID($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DeviceVendorID');
	}
}

if (!function_exists('HID_GetDeviceProductID'))
{
	function HID_GetDeviceProductID($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DeviceProductID');
	}
}

if (!function_exists('HID_GetDevices'))
{
	function HID_GetDevices($InstanceID)
 	{
		$result = Array();
		$json = json_decode(IPS_GetConfigurationForm($InstanceID));
		foreach($json->elements as $element)
		{
			if(isset($element->name) && ($element->name == "Device"))
			{
				foreach($element->options as $option) {
					foreach($option->value as $property) {
						$property->name = str_replace('DeviceVendorID', 'VendorID', $property->name);
						$property->name = str_replace('DeviceProductID', 'ProductID', $property->name);
						$device[$property->name] = $property->value;
					}
					$split = explode(":", $option->label);
					$device['DeviceVendor'] = trim($split[0]);
					$device['DeviceName'] = trim($split[1]);
					$result[] = $device;
				}
			}
		}
		return $result;
	}
}

if (!function_exists('USBXp_SetBaudRate'))
{
	function USBXp_SetBaudRate($InstanceID, $BaudRate)
 	{
		IPS_SetProperty($InstanceID, 'BaudRate', $BaudRate);
	}
}

if (!function_exists('USBXp_SetStopBits'))
{
	function USBXp_SetStopBits($InstanceID, $StopBits)
 	{
		IPS_SetProperty($InstanceID, 'StopBits', $StopBits);
	}
}

if (!function_exists('USBXp_SetDataBits'))
{
	function USBXp_SetDataBits($InstanceID, $DataBits)
 	{
		IPS_SetProperty($InstanceID, 'DataBits', $DataBits);
	}
}

if (!function_exists('USBXp_SetParity'))
{
	function USBXp_SetParity($InstanceID, $Parity)
 	{
		IPS_SetProperty($InstanceID, 'Parity', $Parity);
	}
}

if (!function_exists('USBXp_GetBaudRate'))
{
	function USBXp_GetBaudRate($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'BaudRate');
	}
}

if (!function_exists('USBXp_GetStopBits'))
{
	function USBXp_GetStopBits($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'StopBits');
	}
}

if (!function_exists('USBXp_GetDataBits'))
{
	function USBXp_GetDataBits($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DataBits');
	}
}

if (!function_exists('USBXp_GetParity'))
{
	function USBXp_GetParity($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Parity');
	}
}

if (!function_exists('USBXp_SetOpen'))
{
	function USBXp_SetOpen($InstanceID, $Open)
 	{
		IPS_SetProperty($InstanceID, 'Open', $Open);
	}
}

if (!function_exists('USBXp_SetPort'))
{
	function USBXp_SetPort($InstanceID, $Port)
 	{
		IPS_SetProperty($InstanceID, 'Port', $Port);
	}
}

if (!function_exists('USBXp_GetOpen'))
{
	function USBXp_GetOpen($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Open');
	}
}

if (!function_exists('USBXp_GetPort'))
{
	function USBXp_GetPort($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Port');
	}
}

if (!function_exists('ALL4000_SetIPAddress'))
{
	function ALL4000_SetIPAddress($InstanceID, $IPAddress)
 	{
		IPS_SetProperty($InstanceID, 'IPAddress', $IPAddress);
	}
}

if (!function_exists('ALL4000_SetInterval'))
{
	function ALL4000_SetInterval($InstanceID, $Seconds)
 	{
		IPS_SetProperty($InstanceID, 'Interval', $Seconds);
	}
}

if (!function_exists('ALL4000_GetIPAddress'))
{
	function ALL4000_GetIPAddress($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'IPAddress');
	}
}

if (!function_exists('ALL4000_GetInterval'))
{
	function ALL4000_GetInterval($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Interval');
	}
}

if (!function_exists('ALL_SetDeviceType'))
{
	function ALL_SetDeviceType($InstanceID, $DeviceType)
 	{
		IPS_SetProperty($InstanceID, 'DeviceType', $DeviceType);
	}
}

if (!function_exists('ALL_SetIPAddress'))
{
	function ALL_SetIPAddress($InstanceID, $IPAddress)
 	{
		IPS_SetProperty($InstanceID, 'IPAddress', $IPAddress);
	}
}

if (!function_exists('ALL_SetInterval'))
{
	function ALL_SetInterval($InstanceID, $Seconds)
 	{
		IPS_SetProperty($InstanceID, 'Interval', $Seconds);
	}
}

if (!function_exists('ALL_SetDeviceNum'))
{
	function ALL_SetDeviceNum($InstanceID, $DeviceNum)
 	{
		IPS_SetProperty($InstanceID, 'DeviceNum', $DeviceNum);
	}
}

if (!function_exists('ALL_GetDeviceType'))
{
	function ALL_GetDeviceType($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DeviceType');
	}
}

if (!function_exists('ALL_GetIPAddress'))
{
	function ALL_GetIPAddress($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'IPAddress');
	}
}

if (!function_exists('ALL_GetInterval'))
{
	function ALL_GetInterval($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Interval');
	}
}

if (!function_exists('ALL_GetDeviceNum'))
{
	function ALL_GetDeviceNum($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DeviceNum');
	}
}

if (!function_exists('ALL3690_SetIPAddress'))
{
	function ALL3690_SetIPAddress($InstanceID, $IPAddress)
 	{
		IPS_SetProperty($InstanceID, 'IPAddress', $IPAddress);
	}
}

if (!function_exists('ALL3690_SetInterval'))
{
	function ALL3690_SetInterval($InstanceID, $Seconds)
 	{
		IPS_SetProperty($InstanceID, 'Interval', $Seconds);
	}
}

if (!function_exists('ALL3690_GetIPAddress'))
{
	function ALL3690_GetIPAddress($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'IPAddress');
	}
}

if (!function_exists('ALL3690_GetInterval'))
{
	function ALL3690_GetInterval($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Interval');
	}
}

if (!function_exists('ALL3691_SetIPAddress'))
{
	function ALL3691_SetIPAddress($InstanceID, $IPAddress)
 	{
		IPS_SetProperty($InstanceID, 'IPAddress', $IPAddress);
	}
}

if (!function_exists('ALL3691_SetInterval'))
{
	function ALL3691_SetInterval($InstanceID, $Seconds)
 	{
		IPS_SetProperty($InstanceID, 'Interval', $Seconds);
	}
}

if (!function_exists('ALL3691_GetIPAddress'))
{
	function ALL3691_GetIPAddress($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'IPAddress');
	}
}

if (!function_exists('ALL3691_GetInterval'))
{
	function ALL3691_GetInterval($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Interval');
	}
}

if (!function_exists('WUT_SetAddress'))
{
	function WUT_SetAddress($InstanceID, $IPAddress)
 	{
		IPS_SetProperty($InstanceID, 'Address', $IPAddress);
	}
}

if (!function_exists('WUT_SetInterval'))
{
	function WUT_SetInterval($InstanceID, $Seconds)
 	{
		IPS_SetProperty($InstanceID, 'Interval', $Seconds);
	}
}

if (!function_exists('WUT_SetPassword'))
{
	function WUT_SetPassword($InstanceID, $Password)
 	{
		IPS_SetProperty($InstanceID, 'Password', $Password);
	}
}

if (!function_exists('WUT_GetAddress'))
{
	function WUT_GetAddress($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Address');
	}
}

if (!function_exists('WUT_GetInterval'))
{
	function WUT_GetInterval($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Interval');
	}
}

if (!function_exists('WUT_GetPassword'))
{
	function WUT_GetPassword($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Password');
	}
}

if (!function_exists('WUT_SetIONum'))
{
	function WUT_SetIONum($InstanceID, $IONum)
 	{
		IPS_SetProperty($InstanceID, 'IONum', $IONum);
	}
}

if (!function_exists('WUT_GetIONum'))
{
	function WUT_GetIONum($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'IONum');
	}
}

if (!function_exists('WUT_SetImpulses'))
{
	function WUT_SetImpulses($InstanceID, $Impulses)
 	{
		IPS_SetProperty($InstanceID, 'Impulses', $Impulses);
	}
}

if (!function_exists('WUT_GetImpulses'))
{
	function WUT_GetImpulses($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Impulses');
	}
}

if (!function_exists('EZCT_SetIPAddress'))
{
	function EZCT_SetIPAddress($InstanceID, $Address)
 	{
		IPS_SetProperty($InstanceID, 'IPAddress', $Address);
	}
}

if (!function_exists('EZCT_GetIPAddress'))
{
	function EZCT_GetIPAddress($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'IPAddress');
	}
}

if (!function_exists('MF420_SetAddress'))
{
	function MF420_SetAddress($InstanceID, $IPAddress)
 	{
		IPS_SetProperty($InstanceID, 'Address', $IPAddress);
	}
}

if (!function_exists('MF420_SetInterval'))
{
	function MF420_SetInterval($InstanceID, $Seconds)
 	{
		IPS_SetProperty($InstanceID, 'Interval', $Seconds);
	}
}

if (!function_exists('MF420_GetAddress'))
{
	function MF420_GetAddress($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Address');
	}
}

if (!function_exists('MF420_GetInterval'))
{
	function MF420_GetInterval($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Interval');
	}
}

if (!function_exists('PJ_SetDeviceID'))
{
	function PJ_SetDeviceID($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'DeviceID', $Value);
	}
}

if (!function_exists('PJ_GetDeviceID'))
{
	function PJ_GetDeviceID($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DeviceID');
	}
}

if (!function_exists('PJ_SetCounterID'))
{
	function PJ_SetCounterID($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'CounterID', $Value);
	}
}

if (!function_exists('PJ_GetCounterID'))
{
	function PJ_GetCounterID($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'CounterID');
	}
}

if (!function_exists('PJ_SetMode'))
{
	function PJ_SetMode($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'Mode', $Value);
	}
}

if (!function_exists('PJ_GetMode'))
{
	function PJ_GetMode($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Mode');
	}
}

if (!function_exists('PJ_SetInterval'))
{
	function PJ_SetInterval($InstanceID, $Seconds)
 	{
		IPS_SetProperty($InstanceID, 'Interval', $Seconds);
	}
}

if (!function_exists('PJ_GetInterval'))
{
	function PJ_GetInterval($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Interval');
	}
}

if (!function_exists('PJ_SetImpulses'))
{
	function PJ_SetImpulses($InstanceID, $Impulses)
 	{
		IPS_SetProperty($InstanceID, 'Impulses', $Impulses);
	}
}

if (!function_exists('PJ_GetImpulses'))
{
	function PJ_GetImpulses($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Impulses');
	}
}

if (!function_exists('PJ_SetOutputID'))
{
	function PJ_SetOutputID($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'OutputID', $Value);
	}
}

if (!function_exists('PJ_GetOutputID'))
{
	function PJ_GetOutputID($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'OutputID');
	}
}

if (!function_exists('PJ_SetTrackerID'))
{
	function PJ_SetTrackerID($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'TrackerID', $Value);
	}
}

if (!function_exists('PJ_GetTrackerID'))
{
	function PJ_GetTrackerID($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'TrackerID');
	}
}

if (!function_exists('PJ_SetInputID'))
{
	function PJ_SetInputID($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'InputID', $Value);
	}
}

if (!function_exists('PJ_GetInputID'))
{
	function PJ_GetInputID($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'InputID');
	}
}

if (!function_exists('UVR_SetDeviceCount'))
{
	function UVR_SetDeviceCount($InstanceID, $Count)
 	{
		IPS_SetProperty($InstanceID, 'DeviceCount', $Count);
	}
}

if (!function_exists('UVR_SetInterval'))
{
	function UVR_SetInterval($InstanceID, $Seconds)
 	{
		IPS_SetProperty($InstanceID, 'Interval', $Seconds);
	}
}

if (!function_exists('UVR_GetDeviceCount'))
{
	function UVR_GetDeviceCount($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DeviceCount');
	}
}

if (!function_exists('UVR_GetInterval'))
{
	function UVR_GetInterval($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Interval');
	}
}

if (!function_exists('CGWM24_SetPoller'))
{
	function CGWM24_SetPoller($InstanceID, $Milliseconds)
 	{
		IPS_SetProperty($InstanceID, 'Poller', $Milliseconds);
	}
}

if (!function_exists('CGWM24_SetPhase'))
{
	function CGWM24_SetPhase($InstanceID, $Phase)
 	{
		IPS_SetProperty($InstanceID, 'Phase', $Phase);
	}
}

if (!function_exists('CGWM24_GetPoller'))
{
	function CGWM24_GetPoller($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Poller');
	}
}

if (!function_exists('CGWM24_GetPhase'))
{
	function CGWM24_GetPhase($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Phase');
	}
}

if (!function_exists('MBUS_SetAddress'))
{
	function MBUS_SetAddress($InstanceID, $Address)
 	{
		IPS_SetProperty($InstanceID, 'Address', $Address);
	}
}

if (!function_exists('MBUS_SetInterval'))
{
	function MBUS_SetInterval($InstanceID, $Minutes)
 	{
		IPS_SetProperty($InstanceID, 'Interval', $Minutes);
	}
}

if (!function_exists('MBUS_GetAddress'))
{
	function MBUS_GetAddress($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Address');
	}
}

if (!function_exists('MBUS_GetInterval'))
{
	function MBUS_GetInterval($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Interval');
	}
}

if (!function_exists('MBUS_GetDeviceInfo'))
{
	function MBUS_GetDeviceInfo($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DeviceInfo');
	}
}

if (!function_exists('WI_SetActive'))
{
	function WI_SetActive($InstanceID, $Active)
 	{
		IPS_SetProperty($InstanceID, 'Active', $Active);
	}
}

if (!function_exists('WI_SetIPAddress'))
{
	function WI_SetIPAddress($InstanceID, $Address)
 	{
		IPS_SetProperty($InstanceID, 'Server', $Address);
	}
}

if (!function_exists('WI_SetPort'))
{
	function WI_SetPort($InstanceID, $Port)
 	{
		IPS_SetProperty($InstanceID, 'Port', $Port);
	}
}

if (!function_exists('WI_SetHomeDir'))
{
	function WI_SetHomeDir($InstanceID, $Path)
 	{
		IPS_SetProperty($InstanceID, 'HomeDir', $Path);
	}
}

if (!function_exists('WI_SetEnableLogfile'))
{
	function WI_SetEnableLogfile($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'EnableLogFile', $Value);
	}
}

if (!function_exists('WI_SetEnableSSL'))
{
	function WI_SetEnableSSL($InstanceID, $EnableSSL)
 	{
		IPS_SetProperty($InstanceID, 'EnableSSL', $EnableSSL);
	}
}

if (!function_exists('WI_SetEnableBasicAuth'))
{
	function WI_SetEnableBasicAuth($InstanceID, $EnableBasicAuth)
 	{
		IPS_SetProperty($InstanceID, 'EnableBasicAuth', $EnableBasicAuth);
	}
}

if (!function_exists('WI_SetAuthUsername'))
{
	function WI_SetAuthUsername($InstanceID, $Username)
 	{
		IPS_SetProperty($InstanceID, 'AuthUsername', $Username);
	}
}

if (!function_exists('WI_SetAuthPassword'))
{
	function WI_SetAuthPassword($InstanceID, $Password)
 	{
		IPS_SetProperty($InstanceID, 'AuthPassword', $Password);
	}
}

if (!function_exists('WI_GetActive'))
{
	function WI_GetActive($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Active');
	}
}

if (!function_exists('WI_GetIPAddress'))
{
	function WI_GetIPAddress($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Server');
	}
}

if (!function_exists('WI_GetPort'))
{
	function WI_GetPort($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Port');
	}
}

if (!function_exists('WI_GetHomeDir'))
{
	function WI_GetHomeDir($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'HomeDir');
	}
}

if (!function_exists('WI_GetEnableLogfile'))
{
	function WI_GetEnableLogfile($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'EnableLogfile');
	}
}

if (!function_exists('WI_GetEnableSSL'))
{
	function WI_GetEnableSSL($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'EnableSSL');
	}
}

if (!function_exists('WI_GetEnableBasicAuth'))
{
	function WI_GetEnableBasicAuth($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'EnableBasicAuth');
	}
}

if (!function_exists('WI_GetAuthUsername'))
{
	function WI_GetAuthUsername($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'AuthUsername');
	}
}

if (!function_exists('WI_GetAuthPassword'))
{
	function WI_GetAuthPassword($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'AuthPassword');
	}
}

if (!function_exists('IRT_SetDeviceID'))
{
	function IRT_SetDeviceID($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'DeviceID', $Value);
	}
}

if (!function_exists('IRT_GetDeviceID'))
{
	function IRT_GetDeviceID($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DeviceID');
	}
}

if (!function_exists('DMX_SetValue'))
{
	function DMX_SetValue($InstanceID, $Channel, $Value)
 	{
		return DMX_SetChannel($InstanceID, $Channel, $Value);
	}
}

if (!function_exists('DMX_Fade'))
{
	function DMX_Fade($InstanceID, $Channel, $Value, $Time)
 	{
		return DMX_FadeChannel($InstanceID, $Channel, $Value, $Time);
	}
}

if (!function_exists('DMX_FadeDelayed'))
{
	function DMX_FadeDelayed($InstanceID, $Channel, $Value, $Time, $Delay)
 	{
		return DMX_FadeChannelDelayed($InstanceID, $Channel, $Value, $Time, $Delay);
	}
}

if (!function_exists('DMX_GetChannelBase'))
{
	function DMX_GetChannelBase($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'ChannelBase');
	}
}

if (!function_exists('DMX_GetChannelCount'))
{
	function DMX_GetChannelCount($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'ChannelCount');
	}
}

if (!function_exists('DMX_SetChannelBase'))
{
	function DMX_SetChannelBase($InstanceID, $Base)
 	{
		IPS_SetProperty($InstanceID, 'ChannelBase', $Base);
	}
}

if (!function_exists('DMX_SetChannelCount'))
{
	function DMX_SetChannelCount($InstanceID, $Count)
 	{
		IPS_SetProperty($InstanceID, 'ChannelCount', $Count);
	}
}

if (!function_exists('DMXI_SetOpen'))
{
	function DMXI_SetOpen($InstanceID, $Open)
 	{
		IPS_SetProperty($InstanceID, 'Open', $Open);
	}
}

if (!function_exists('DMXI_SetPort'))
{
	function DMXI_SetPort($InstanceID, $Port)
 	{
		IPS_SetProperty($InstanceID, 'Port', $Port);
	}
}

if (!function_exists('DMXI_GetOpen'))
{
	function DMXI_GetOpen($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Open');
	}
}

if (!function_exists('DMXI_GetPort'))
{
	function DMXI_GetPort($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Port');
	}
}

if (!function_exists('DMXI_GetBlackOut'))
{
	function DMXI_GetBlackOut($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'BlackOut');
	}
}

if (!function_exists('DMXI_SetBlackOut'))
{
	function DMXI_SetBlackOut($InstanceID, $BlackoutOn)
 	{
		return DMX_SetBlackOut($InstanceID, $BlackoutOn);
	}
}

if (!function_exists('DMXI_ResetInterface'))
{
	function DMXI_ResetInterface($InstanceID)
 	{
		return DMX_ResetInterface($InstanceID);
	}
}

if (!function_exists('FHZ_GetFHTQueue'))
{
	function FHZ_GetFHTQueue($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'FHTQueue');
	}
}

if (!function_exists('FHZ_GetDataQueue'))
{
	function FHZ_GetDataQueue($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DataQueue');
	}
}

if (!function_exists('HMS_SetDevice'))
{
	function HMS_SetDevice($InstanceID, $DeviceType, $DeviceID)
 	{
		IPS_SetProperty($InstanceID, 'DeviceType', $DeviceType);
		IPS_SetProperty($InstanceID, 'DeviceID', $DeviceID);
	}
}

if (!function_exists('HMS_GetDevice'))
{
	function HMS_GetDevice($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Device');
	}
}

if (!function_exists('FS10_SetDeviceType'))
{
	function FS10_SetDeviceType($InstanceID, $_Type)
 	{
		IPS_SetProperty($InstanceID, 'DeviceType', $_Type);
	}
}

if (!function_exists('FS10_GetDeviceType'))
{
	function FS10_GetDeviceType($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DeviceType');
	}
}

if (!function_exists('FS10_SetDeviceID'))
{
	function FS10_SetDeviceID($InstanceID, $ID)
 	{
		IPS_SetProperty($InstanceID, 'DeviceID', $ID);
	}
}

if (!function_exists('FS10_GetDeviceID'))
{
	function FS10_GetDeviceID($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DeviceID');
	}
}

if (!function_exists('HM_SetMode'))
{
	function HM_SetMode($InstanceID, $Mode)
 	{
		IPS_SetProperty($InstanceID, 'Mode', $Mode);
	}
}

if (!function_exists('HM_SetOpen'))
{
	function HM_SetOpen($InstanceID, $Open)
 	{
		IPS_SetProperty($InstanceID, 'Open', $Open);
	}
}

if (!function_exists('HM_SetHost'))
{
	function HM_SetHost($InstanceID, $Host)
 	{
		IPS_SetProperty($InstanceID, 'Host', $Host);
	}
}

if (!function_exists('HM_SetIPAddress'))
{
	function HM_SetIPAddress($InstanceID, $Address)
 	{
		IPS_SetProperty($InstanceID, 'IPAddress', $Address);
	}
}

if (!function_exists('HM_SetPort'))
{
	function HM_SetPort($InstanceID, $Port)
 	{
		IPS_SetProperty($InstanceID, 'Port', $Port);
	}
}

if (!function_exists('HM_SetWRPort'))
{
	function HM_SetWRPort($InstanceID, $Port)
 	{
		IPS_SetProperty($InstanceID, 'WRPort', $Port);
	}
}

if (!function_exists('HM_SetRFPort'))
{
	function HM_SetRFPort($InstanceID, $Port)
 	{
		IPS_SetProperty($InstanceID, 'RFPort', $Port);
	}
}

if (!function_exists('HM_GetMode'))
{
	function HM_GetMode($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Mode');
	}
}

if (!function_exists('HM_GetOpen'))
{
	function HM_GetOpen($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Open');
	}
}

if (!function_exists('HM_GetHost'))
{
	function HM_GetHost($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Host');
	}
}

if (!function_exists('HM_GetIPAddress'))
{
	function HM_GetIPAddress($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'IPAddress');
	}
}

if (!function_exists('HM_GetPort'))
{
	function HM_GetPort($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Port');
	}
}

if (!function_exists('HM_GetWRPort'))
{
	function HM_GetWRPort($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'WRPort');
	}
}

if (!function_exists('HM_GetRFPort'))
{
	function HM_GetRFPort($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'RFPort');
	}
}

if (!function_exists('HM_SetProtocol'))
{
	function HM_SetProtocol($InstanceID, $Protocol)
 	{
		IPS_SetProperty($InstanceID, 'Protocol', $Protocol);
	}
}

if (!function_exists('HM_SetAddress'))
{
	function HM_SetAddress($InstanceID, $Address)
 	{
		IPS_SetProperty($InstanceID, 'Address', $Address);
	}
}

if (!function_exists('HM_SetEmulateStatus'))
{
	function HM_SetEmulateStatus($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'EmulateStatus', $Value);
	}
}

if (!function_exists('HM_GetProtocol'))
{
	function HM_GetProtocol($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Protocol');
	}
}

if (!function_exists('HM_GetAddress'))
{
	function HM_GetAddress($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Address');
	}
}

if (!function_exists('HM_GetEmulateStatus'))
{
	function HM_GetEmulateStatus($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'EmulateStatus');
	}
}

if (!function_exists('TMEXA_SetOpen'))
{
	function TMEXA_SetOpen($InstanceID, $Open)
 	{
		IPS_SetProperty($InstanceID, 'Open', $Open);
	}
}

if (!function_exists('TMEXA_SetAdapter'))
{
	function TMEXA_SetAdapter($InstanceID, $PortNum, $PortType, $PortSpeed, $PortLevel)
 	{
		IPS_SetProperty($InstanceID, 'AdapterPort', $PortNum);
		IPS_SetProperty($InstanceID, 'AdapterType', $PortType);
		IPS_SetProperty($InstanceID, 'AdapterSpeed', $PortSpeed);
		IPS_SetProperty($InstanceID, 'AdapterLevel', $PortLevel);
	}
}

if (!function_exists('TMEXA_GetOpen'))
{
	function TMEXA_GetOpen($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Open');
	}
}

if (!function_exists('TMEXA_GetAdapter'))
{
	function TMEXA_GetAdapter($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Adapter');
	}
}

if (!function_exists('TMEXA_GetQueue'))
{
	function TMEXA_GetQueue($InstanceID)
 	{
		return TMEX_GetQueue($InstanceID);
	}
}

if (!function_exists('TMEXA_EnumerateDevices'))
{
	function TMEXA_EnumerateDevices($InstanceID)
 	{
		if(IPS_GetInstance($InstanceID)['ModuleInfo']['ModuleID'] == "{CED1D815-2477-4B05-8F65-0E4475913063}") {
			$ids = IPS_GetInstanceListByModuleID("{F462BFF3-6772-4720-8450-49E6E2820643}");
			foreach($ids as $id) {
				if(IPS_GetInstance($id)['ConnectionID'] == IPS_GetInstance($InstanceID)['ConnectionID']) {
					return TMEX_EnumerateDevices($id);
				}
			}
		}

		//Fallback
		return TMEX_EnumerateDevices($InstanceID);
	}
}

if (!function_exists('LCN_SetUsername'))
{
	function LCN_SetUsername($InstanceID, $Username)
 	{
		IPS_SetProperty($InstanceID, 'Username', $Username);
	}
}

if (!function_exists('LCN_SetPassword'))
{
	function LCN_SetPassword($InstanceID, $Password)
 	{
		IPS_SetProperty($InstanceID, 'Password', $Password);
	}
}

if (!function_exists('LCN_GetUsername'))
{
	function LCN_GetUsername($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Username');
	}
}

if (!function_exists('LCN_GetPassword'))
{
	function LCN_GetPassword($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Password');
	}
}

if (!function_exists('LCN_SetSegment'))
{
	function LCN_SetSegment($InstanceID, $Segment)
 	{
		IPS_SetProperty($InstanceID, 'Segment', $Segment);
	}
}

if (!function_exists('LCN_SetTarget'))
{
	function LCN_SetTarget($InstanceID, $Target)
 	{
		IPS_SetProperty($InstanceID, 'Target', $Target);
	}
}

if (!function_exists('LCN_GetSegment'))
{
	function LCN_GetSegment($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Segment');
	}
}

if (!function_exists('LCN_GetTarget'))
{
	function LCN_GetTarget($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Target');
	}
}

if (!function_exists('LCN_SetUnit'))
{
	function LCN_SetUnit($InstanceID, $_Unit)
 	{
		IPS_SetProperty($InstanceID, 'Unit', $_Unit);
	}
}

if (!function_exists('LCN_SetChannel'))
{
	function LCN_SetChannel($InstanceID, $Channel)
 	{
		IPS_SetProperty($InstanceID, 'Channel', $Channel);
	}
}

if (!function_exists('LCN_GetUnit'))
{
	function LCN_GetUnit($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Unit');
	}
}

if (!function_exists('LCN_GetChannel'))
{
	function LCN_GetChannel($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Channel');
	}
}

if (!function_exists('LCN_SetEmulateStatus'))
{
	function LCN_SetEmulateStatus($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'EmulateStatus', $Value);
	}
}

if (!function_exists('LCN_SetValueType'))
{
	function LCN_SetValueType($InstanceID, $Value, $_Type)
 	{
		switch($Value)
		{
			case 0:
				IPS_SetProperty($InstanceID, "TValueType", $_Type);
				break;
			case 1:
				IPS_SetProperty($InstanceID, "R1ValueType", $_Type);
				break;
			case 3:
				IPS_SetProperty($InstanceID, "R2ValueType", $_Type);
				break;
		}		
	}
}

if (!function_exists('LCN_SetInterval'))
{
	function LCN_SetInterval($InstanceID, $Seconds)
 	{
		IPS_SetProperty($InstanceID, 'Interval', $Seconds);
	}
}

if (!function_exists('LCN_GetEmulateStatus'))
{
	function LCN_GetEmulateStatus($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'EmulateStatus');
	}
}

if (!function_exists('LCN_GetValues'))
{
	function LCN_GetValues($InstanceID)
 	{
		return json_decode(IPS_GetProperty($InstanceID, 'Values'));
	}
}

if (!function_exists('LCN_GetValueType'))
{
	function LCN_GetValueType($InstanceID, $Value)
 	{
		switch($Value)
		{
			case 0:
				return IPS_GetProperty($InstanceID, "TValueType");
			case 1:
				return IPS_GetProperty($InstanceID, "R1ValueType");
			case 3:
				return IPS_GetProperty($InstanceID, "R2ValueType");
		}
	}
}

if (!function_exists('LCN_GetInterval'))
{
	function LCN_GetInterval($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Interval');
	}
}

if (!function_exists('LCN_SetDataType'))
{
	function LCN_SetDataType($InstanceID, $_Type)
 	{
		IPS_SetProperty($InstanceID, 'DataType', $_Type);
	}
}

if (!function_exists('LCN_GetDataType'))
{
	function LCN_GetDataType($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DataType');
	}
}

if (!function_exists('LCN_SetMoveChannel'))
{
	function LCN_SetMoveChannel($InstanceID, $Channel)
 	{
		IPS_SetProperty($InstanceID, 'MoveChannel', $Channel);
	}
}

if (!function_exists('LCN_GetMoveChannel'))
{
	function LCN_GetMoveChannel($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'MoveChannel');
	}
}

if (!function_exists('LCN_SetDirectionChannel'))
{
	function LCN_SetDirectionChannel($InstanceID, $Channel)
 	{
		IPS_SetProperty($InstanceID, 'DirectionChannel', $Channel);
	}
}

if (!function_exists('LCN_GetDirectionChannel'))
{
	function LCN_GetDirectionChannel($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DirectionChannel');
	}
}

if (!function_exists('TTS_SetEngine'))
{
	function TTS_SetEngine($InstanceID, $Engine)
 	{
		IPS_SetProperty($InstanceID, 'Engine', $Engine);
	}
}

if (!function_exists('TTS_SetAudioOutput'))
{
	function TTS_SetAudioOutput($InstanceID, $Output)
 	{
		IPS_SetProperty($InstanceID, 'AudioOutput', $Output);
	}
}

if (!function_exists('TTS_GetEngine'))
{
	function TTS_GetEngine($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Engine');
	}
}

if (!function_exists('TTS_GetAudioOutput'))
{
	function TTS_GetAudioOutput($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'AudioOutput');
	}
}

if (!function_exists('RegVar_SetRXObjectID'))
{
	function RegVar_SetRXObjectID($InstanceID, $ObjectID)
 	{
		IPS_SetProperty($InstanceID, 'RXObjectID', $ObjectID);
	}
}

if (!function_exists('RegVar_GetRXObjectID'))
{
	function RegVar_GetRXObjectID($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'RXObjectID');
	}
}

if (!function_exists('Cutter_SetParseType'))
{
	function Cutter_SetParseType($InstanceID, $_Type)
 	{
		IPS_SetProperty($InstanceID, 'ParseType', $_Type);
	}
}

if (!function_exists('Cutter_SetLeftCutChar'))
{
	function Cutter_SetLeftCutChar($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'LeftCutChar', $Value);
	}
}

if (!function_exists('Cutter_SetRightCutChar'))
{
	function Cutter_SetRightCutChar($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'RightCutChar', $Value);
	}
}

if (!function_exists('Cutter_SetDeleteCutChars'))
{
	function Cutter_SetDeleteCutChars($InstanceID, $Delete)
 	{
		IPS_SetProperty($InstanceID, 'DeleteCutChars', $Delete);
	}
}

if (!function_exists('Cutter_SetInputLength'))
{
	function Cutter_SetInputLength($InstanceID, $Length)
 	{
		IPS_SetProperty($InstanceID, 'InputLength', $Length);
	}
}

if (!function_exists('Cutter_SetSyncChar'))
{
	function Cutter_SetSyncChar($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'SyncChar', $Value);
	}
}

if (!function_exists('Cutter_SetTimeout'))
{
	function Cutter_SetTimeout($InstanceID, $Milliseconds)
 	{
		IPS_SetProperty($InstanceID, 'Timeout', $Milliseconds);
	}
}

if (!function_exists('Cutter_GetParseType'))
{
	function Cutter_GetParseType($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'ParseType');
	}
}

if (!function_exists('Cutter_GetLeftCutChar'))
{
	function Cutter_GetLeftCutChar($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'LeftCutChar');
	}
}

if (!function_exists('Cutter_GetRightCutChar'))
{
	function Cutter_GetRightCutChar($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'RightCutChar');
	}
}

if (!function_exists('Cutter_GetDeleteCutChars'))
{
	function Cutter_GetDeleteCutChars($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DeleteCutChars');
	}
}

if (!function_exists('Cutter_GetInputLength'))
{
	function Cutter_GetInputLength($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'InputLength');
	}
}

if (!function_exists('Cutter_GetSyncChar'))
{
	function Cutter_GetSyncChar($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'SyncChar');
	}
}

if (!function_exists('Cutter_GetTimeout'))
{
	function Cutter_GetTimeout($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Timeout');
	}
}

if (!function_exists('MXC_SetDataPoint'))
{
	function MXC_SetDataPoint($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'DataPoint', $Value);
	}
}

if (!function_exists('MXC_GetDataPoint'))
{
	function MXC_GetDataPoint($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DataPoint');
	}
}

if (!function_exists('MXC_SetEmulateStatus'))
{
	function MXC_SetEmulateStatus($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'EmulateStatus', $Value);
	}
}

if (!function_exists('MXC_GetEmulateStatus'))
{
	function MXC_GetEmulateStatus($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'EmulateStatus');
	}
}

if (!function_exists('MXC_SetValueType'))
{
	function MXC_SetValueType($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'ValueType', $Value);
	}
}

if (!function_exists('MXC_GetValueType'))
{
	function MXC_GetValueType($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'ValueType');
	}
}

if (!function_exists('MXC_SetEnergyType'))
{
	function MXC_SetEnergyType($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'EnergyType', $Value);
	}
}

if (!function_exists('MXC_GetEnergyType'))
{
	function MXC_GetEnergyType($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'EnergyType');
	}
}

if (!function_exists('MXC_SetImpulses'))
{
	function MXC_SetImpulses($InstanceID, $Impulses)
 	{
		IPS_SetProperty($InstanceID, 'Impulses', $Impulses);
	}
}

if (!function_exists('MXC_GetImpulses'))
{
	function MXC_GetImpulses($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Impulses');
	}
}

if (!function_exists('MXC_SetTemperatureVariableID'))
{
	function MXC_SetTemperatureVariableID($InstanceID, $VariableID)
 	{
		IPS_SetProperty($InstanceID, 'TemperatureVariableID', $VariableID);
	}
}

if (!function_exists('MXC_GetTemperatureVariableID'))
{
	function MXC_GetTemperatureVariableID($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'TemperatureVariableID');
	}
}

if (!function_exists('MBT_SetGatewayMode'))
{
	function MBT_SetGatewayMode($InstanceID, $GatewayMode)
 	{
		IPS_SetProperty($InstanceID, 'GatewayMode', $GatewayMode);
	}
}

if (!function_exists('MBT_GetGatewayMode'))
{
	function MBT_GetGatewayMode($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'GatewayMode');
	}
}

if (!function_exists('MBT_SetDeviceID'))
{
	function MBT_SetDeviceID($InstanceID, $DeviceID)
 	{
		IPS_SetProperty($InstanceID, 'DeviceID', $DeviceID);
	}
}

if (!function_exists('MBT_GetDeviceID'))
{
	function MBT_GetDeviceID($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DeviceID');
	}
}

if (!function_exists('MBT_SetSwapWords'))
{
	function MBT_SetSwapWords($InstanceID, $SwapWords)
 	{
		IPS_SetProperty($InstanceID, 'SwapWords', $SwapWords);
	}
}

if (!function_exists('MBT_GetSwapWords'))
{
	function MBT_GetSwapWords($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'SwapWords');
	}
}

if (!function_exists('ModBus_SetType'))
{
	function ModBus_SetType($InstanceID, $DataType)
 	{
		IPS_SetProperty($InstanceID, 'DataType', $DataType);
	}
}

if (!function_exists('ModBus_SetWriteAddress'))
{
	function ModBus_SetWriteAddress($InstanceID, $Address)
 	{
		IPS_SetProperty($InstanceID, 'WriteAddress', $Address);
	}
}

if (!function_exists('ModBus_SetReadAddress'))
{
	function ModBus_SetReadAddress($InstanceID, $Address)
 	{
		IPS_SetProperty($InstanceID, 'ReadAddress', $Address);
	}
}

if (!function_exists('ModBus_SetPoller'))
{
	function ModBus_SetPoller($InstanceID, $Milliseconds)
 	{
		IPS_SetProperty($InstanceID, 'Poller', $Milliseconds);
	}
}

if (!function_exists('ModBus_SetReadOnly'))
{
	function ModBus_SetReadOnly($InstanceID, $ReadOnly)
 	{
		IPS_SetProperty($InstanceID, 'ReadOnly', $ReadOnly);
	}
}

if (!function_exists('ModBus_GetType'))
{
	function ModBus_GetType($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DataType');
	}
}

if (!function_exists('ModBus_GetWriteAddress'))
{
	function ModBus_GetWriteAddress($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'WriteAddress');
	}
}

if (!function_exists('ModBus_GetReadAddress'))
{
	function ModBus_GetReadAddress($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'ReadAddress');
	}
}

if (!function_exists('ModBus_GetPoller'))
{
	function ModBus_GetPoller($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Poller');
	}
}

if (!function_exists('ModBus_GetReadOnly'))
{
	function ModBus_GetReadOnly($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'ReadOnly');
	}
}

if (!function_exists('S7_SetOpen'))
{
	function S7_SetOpen($InstanceID, $Open)
 	{
		IPS_SetProperty($InstanceID, 'Open', $Open);
	}
}

if (!function_exists('S7_SetProtocolType'))
{
	function S7_SetProtocolType($InstanceID, $ProtocolType)
 	{
		IPS_SetProperty($InstanceID, 'ProtocolType', $ProtocolType);
	}
}

if (!function_exists('S7_SetIPAddress'))
{
	function S7_SetIPAddress($InstanceID, $IPAddress)
 	{
		IPS_SetProperty($InstanceID, 'IPAddress', $IPAddress);
	}
}

if (!function_exists('S7_SetComPort'))
{
	function S7_SetComPort($InstanceID, $Port)
 	{
		IPS_SetProperty($InstanceID, 'ComPort', $Port);
	}
}

if (!function_exists('S7_SetCPURack'))
{
	function S7_SetCPURack($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'CPURack', $Value);
	}
}

if (!function_exists('S7_SetCPUSlot'))
{
	function S7_SetCPUSlot($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'CPUSlot', $Value);
	}
}

if (!function_exists('S7_SetMPISpeed'))
{
	function S7_SetMPISpeed($InstanceID, $Speed)
 	{
		IPS_SetProperty($InstanceID, 'MPISpeed', $Speed);
	}
}

if (!function_exists('S7_SetMPILocal'))
{
	function S7_SetMPILocal($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'MPILocal', $Value);
	}
}

if (!function_exists('S7_SetMPIRemote'))
{
	function S7_SetMPIRemote($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'MPIRemote', $Value);
	}
}

if (!function_exists('S7_GetOpen'))
{
	function S7_GetOpen($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Open');
	}
}

if (!function_exists('S7_GetProtocolType'))
{
	function S7_GetProtocolType($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'ProtocolType');
	}
}

if (!function_exists('S7_GetIPAddress'))
{
	function S7_GetIPAddress($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'IPAddress');
	}
}

if (!function_exists('S7_GetComPort'))
{
	function S7_GetComPort($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'ComPort');
	}
}

if (!function_exists('S7_GetCPURack'))
{
	function S7_GetCPURack($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'CPURack');
	}
}

if (!function_exists('S7_GetCPUSlot'))
{
	function S7_GetCPUSlot($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'CPUSlot');
	}
}

if (!function_exists('S7_GetMPISpeed'))
{
	function S7_GetMPISpeed($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'MPISpeed');
	}
}

if (!function_exists('S7_GetMPILocal'))
{
	function S7_GetMPILocal($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'MPILocal');
	}
}

if (!function_exists('S7_GetMPIRemote'))
{
	function S7_GetMPIRemote($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'MPIRemote');
	}
}

if (!function_exists('S7_SetType'))
{
	function S7_SetType($InstanceID, $DataType)
 	{
		IPS_SetProperty($InstanceID, 'DataType', $DataType);
	}
}

if (!function_exists('S7_SetArea'))
{
	function S7_SetArea($InstanceID, $Area)
 	{
		IPS_SetProperty($InstanceID, 'Area', $Area);
	}
}

if (!function_exists('S7_SetAreaAddress'))
{
	function S7_SetAreaAddress($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'AreaAddress', $Value);
	}
}

if (!function_exists('S7_SetAddress'))
{
	function S7_SetAddress($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'Address', $Value);
	}
}

if (!function_exists('S7_SetBit'))
{
	function S7_SetBit($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'Bit', $Value);
	}
}

if (!function_exists('S7_SetPoller'))
{
	function S7_SetPoller($InstanceID, $Milliseconds)
 	{
		IPS_SetProperty($InstanceID, 'Poller', $Milliseconds);
	}
}

if (!function_exists('S7_SetReadOnly'))
{
	function S7_SetReadOnly($InstanceID, $ReadOnly)
 	{
		IPS_SetProperty($InstanceID, 'ReadOnly', $ReadOnly);
	}
}

if (!function_exists('S7_GetType'))
{
	function S7_GetType($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DataType');
	}
}

if (!function_exists('S7_GetArea'))
{
	function S7_GetArea($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Area');
	}
}

if (!function_exists('S7_GetAreaAddress'))
{
	function S7_GetAreaAddress($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'AreaAddress');
	}
}

if (!function_exists('S7_GetAddress'))
{
	function S7_GetAddress($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Address');
	}
}

if (!function_exists('S7_GetBit'))
{
	function S7_GetBit($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Bit');
	}
}

if (!function_exists('S7_GetPoller'))
{
	function S7_GetPoller($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Poller');
	}
}

if (!function_exists('S7_GetReadOnly'))
{
	function S7_GetReadOnly($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'ReadOnly');
	}
}

if (!function_exists('ISDN_SetController'))
{
	function ISDN_SetController($InstanceID, $ControllerID)
 	{
		IPS_SetProperty($InstanceID, 'Controller', $ControllerID);
	}
}

if (!function_exists('ISDN_GetController'))
{
	function ISDN_GetController($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Controller');
	}
}

if (!function_exists('ISDN_SetMSNIncoming'))
{
	function ISDN_SetMSNIncoming($InstanceID, $MSN)
 	{
		IPS_SetProperty($InstanceID, 'MSNIncoming', $MSN);
	}
}

if (!function_exists('ISDN_GetMSNIncoming'))
{
	function ISDN_GetMSNIncoming($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'MSNIncoming');
	}
}

if (!function_exists('ISDN_SetMSNOutgoing'))
{
	function ISDN_SetMSNOutgoing($InstanceID, $MSN)
 	{
		IPS_SetProperty($InstanceID, 'MSNOutgoing', $MSN);
	}
}

if (!function_exists('ISDN_GetMSNOutgoing'))
{
	function ISDN_GetMSNOutgoing($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'MSNOutgoing');
	}
}

if (!function_exists('ISDN_SetScriptID'))
{
	function ISDN_SetScriptID($InstanceID, $ScriptID)
 	{
		IPS_SetProperty($InstanceID, 'ScriptID', $ScriptID);
	}
}

if (!function_exists('ISDN_GetScriptID'))
{
	function ISDN_GetScriptID($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'ScriptID');
	}
}

if (!function_exists('ENO_SetGatewayMode'))
{
	function ENO_SetGatewayMode($InstanceID, $Mode)
 	{
		IPS_SetProperty($InstanceID, 'GatewayMode', $Mode);
	}
}

if (!function_exists('ENO_GetGatewayMode'))
{
	function ENO_GetGatewayMode($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'GatewayMode');
	}
}

if (!function_exists('ENO_SetDeviceID'))
{
	function ENO_SetDeviceID($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'DeviceID', $Value);
	}
}

if (!function_exists('ENO_GetDeviceID'))
{
	function ENO_GetDeviceID($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DeviceID');
	}
}

if (!function_exists('ENO_SetButtonMode'))
{
	function ENO_SetButtonMode($InstanceID, $Mode)
 	{
		IPS_SetProperty($InstanceID, 'ButtonMode', $Mode);
	}
}

if (!function_exists('ENO_GetButtonMode'))
{
	function ENO_GetButtonMode($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'ButtonMode');
	}
}

if (!function_exists('ENO_SetTemperatureRange'))
{
	function ENO_SetTemperatureRange($InstanceID, $Range)
 	{
		IPS_SetProperty($InstanceID, 'TemperatureRange', $Range);
	}
}

if (!function_exists('ENO_GetTemperatureRange'))
{
	function ENO_GetTemperatureRange($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'TemperatureRange');
	}
}

if (!function_exists('ENO_SetTemperatureOffset'))
{
	function ENO_SetTemperatureOffset($InstanceID, $Offset)
 	{
		IPS_SetProperty($InstanceID, 'TemperatureOffset', $Offset);
	}
}

if (!function_exists('ENO_GetTemperatureOffset'))
{
	function ENO_GetTemperatureOffset($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'TemperatureOffset');
	}
}

if (!function_exists('ENO_SetReturnID'))
{
	function ENO_SetReturnID($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'ReturnID', $Value);
	}
}

if (!function_exists('ENO_GetReturnID'))
{
	function ENO_GetReturnID($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'ReturnID');
	}
}

if (!function_exists('ENO_SetEmulateStatus'))
{
	function ENO_SetEmulateStatus($InstanceID, $Value)
 	{
		IPS_SetProperty($InstanceID, 'EmulateStatus', $Value);
	}
}

if (!function_exists('ENO_GetEmulateStatus'))
{
	function ENO_GetEmulateStatus($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'EmulateStatus');
	}
}

if (!function_exists('XBee_ReadCoordinatorID'))
{
	function XBee_ReadCoordinatorID($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, '_ReadCoordinatorID');
	}
}

if (!function_exists('XBee_SetDeviceID'))
{
	function XBee_SetDeviceID($InstanceID, $DeviceID)
 	{
		IPS_SetProperty($InstanceID, 'DeviceID', $DeviceID);
	}
}

if (!function_exists('XBee_GetDeviceID'))
{
	function XBee_GetDeviceID($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DeviceID');
	}
}

if (!function_exists('EIB_SetGatewayMode'))
{
	function EIB_SetGatewayMode($InstanceID, $GatewayMode)
 	{
		IPS_SetProperty($InstanceID, 'GatewayMode', $GatewayMode);
	}
}

if (!function_exists('EIB_GetGatewayMode'))
{
	function EIB_GetGatewayMode($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'GatewayMode');
	}
}

if (!function_exists('EIB_GetPysicalAddress'))
{
	function EIB_GetPysicalAddress($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'PysicalAddress');
	}
}

if (!function_exists('EIB_GetSerialNumber'))
{
	function EIB_GetSerialNumber($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'SerialNumber');
	}
}

if (!function_exists('EIB_SetGroupAddress'))
{
	function EIB_SetGroupAddress($InstanceID, $GA1, $GA2, $GA3)
 	{
		IPS_SetProperty($InstanceID, 'GroupAddress1', $GA1);
		IPS_SetProperty($InstanceID, 'GroupAddress2', $GA2);
		IPS_SetProperty($InstanceID, 'GroupAddress3', $GA3);
	}
}

if (!function_exists('EIB_GetGroupAddress'))
{
	function EIB_GetGroupAddress($InstanceID)
 	{
		return Array(
			"GA1" => IPS_GetProperty($InstanceID, 'GroupAddress1'),
			"GA2" => IPS_GetProperty($InstanceID, 'GroupAddress2'),
			"GA3" => IPS_GetProperty($InstanceID, 'GroupAddress3')
		);
	}
}

if (!function_exists('EIB_SetGroupFunction'))
{
	function EIB_SetGroupFunction($InstanceID, $GF)
 	{
		IPS_SetProperty($InstanceID, 'GroupFunction', $GF);
	}
}

if (!function_exists('EIB_GetGroupFunction'))
{
	function EIB_GetGroupFunction($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'GroupFunction');
	}
}

if (!function_exists('EIB_SetGroupInterpretation'))
{
	function EIB_SetGroupInterpretation($InstanceID, $GI)
 	{
		IPS_SetProperty($InstanceID, 'GroupInterpretation', $GI);
	}
}

if (!function_exists('EIB_GetGroupInterpretation'))
{
	function EIB_GetGroupInterpretation($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'GroupInterpretation');
	}
}

if (!function_exists('EIB_SetGroupCapabilities'))
{
	function EIB_SetGroupCapabilities($InstanceID, $GC)
 	{
		IPS_SetProperty($InstanceID, 'GroupCapabilityReceive', in_array(0, $GC));
		IPS_SetProperty($InstanceID, 'GroupCapabilityRead', in_array(1, $GC));
		IPS_SetProperty($InstanceID, 'GroupCapabilityTransmit', in_array(2, $GC));
		IPS_SetProperty($InstanceID, 'GroupCapabilityWrite', in_array(3, $GC));
	}
}

if (!function_exists('EIB_GetGroupCapabilities'))
{
	function EIB_GetGroupCapabilities($InstanceID)
 	{
		$array = Array();
		if(IPS_GetProperty($InstanceID, 'GroupCapabilityReceive'))
			$array[] = 0;
		if(IPS_GetProperty($InstanceID, 'GroupCapabilityRead'))
			$array[] = 1;
		if(IPS_GetProperty($InstanceID, 'GroupCapabilityTransmit'))
			$array[] = 2;
		if(IPS_GetProperty($InstanceID, 'GroupCapabilityWrite'))
			$array[] = 3;
		return $array;
	}
}

if (!function_exists('FS20_SetDeviceAddress'))
{
	function FS20_SetDeviceAddress($InstanceID, $HomeCode, $SubAddress, $Address)
 	{
		IPS_SetProperty($InstanceID, 'HomeCode', $HomeCode);
		IPS_SetProperty($InstanceID, 'SubAddress', $SubAddress);
		IPS_SetProperty($InstanceID, 'Address', $Address);
	}
}

if (!function_exists('FS20_GetDeviceAddress'))
{
	function FS20_GetDeviceAddress($InstanceID)
 	{
		return Array(
			"HomeCode" => IPS_GetProperty($InstanceID, 'HomeCode'),
			"SubAddress" => IPS_GetProperty($InstanceID, 'SubAddress'),
			"Address" => IPS_GetProperty($InstanceID, 'Address')
		);
	}
}

if (!function_exists('FS20_SetEnableReceive'))
{
	function FS20_SetEnableReceive($InstanceID, $EnableReceive)
 	{
		IPS_SetProperty($InstanceID, 'EnableReceive', $EnableReceive);
	}
}

if (!function_exists('FS20_GetEnableReceive'))
{
	function FS20_GetEnableReceive($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'EnableReceive');
	}
}

if (!function_exists('FS20_SetEnableTimer'))
{
	function FS20_SetEnableTimer($InstanceID, $EnableTimer)
 	{
		IPS_SetProperty($InstanceID, 'EnableTimer', $EnableTimer);
	}
}

if (!function_exists('FS20_GetEnableTimer'))
{
	function FS20_GetEnableTimer($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'EnableTimer');
	}
}

if (!function_exists('FHT_SetAddress'))
{
	function FHT_SetAddress($InstanceID, $Address)
 	{
		IPS_SetProperty($InstanceID, 'Address', $Address);
	}
}

if (!function_exists('FHT_GetAddress'))
{
	function FHT_GetAddress($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Address');
	}
}


if (!function_exists('FHT_SetEmulateStatus'))
{
	function FHT_SetEmulateStatus($InstanceID, $EmulateStatus)
 	{
		IPS_SetProperty($InstanceID, 'EmulateStatus', $EmulateStatus);
	}
}

if (!function_exists('FHT_GetEmulateStatus'))
{
	function FHT_GetEmulateStatus($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'EmulateStatus');
	}
}

if (!function_exists('WMRS200_SetDeviceType'))
{
	function WMRS200_SetDeviceType($InstanceID, $_Type)
 	{
		IPS_SetProperty($InstanceID, 'DeviceType', $_Type);
	}
}

if (!function_exists('WMRS200_GetDeviceType'))
{
	function WMRS200_GetDeviceType($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DeviceType');
	}
}

if (!function_exists('WMRS200_SetDeviceID'))
{
	function WMRS200_SetDeviceID($InstanceID, $ID)
 	{
		IPS_SetProperty($InstanceID, 'DeviceID', $ID);
	}
}

if (!function_exists('WMRS200_GetDeviceID'))
{
	function WMRS200_GetDeviceID($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DeviceID');
	}
}

if (!function_exists('SMTP_SetHost'))
{
	function SMTP_SetHost($InstanceID, $Host)
 	{
		IPS_SetProperty($InstanceID, 'Host', $Host);
	}
}

if (!function_exists('SMTP_SetPort'))
{
	function SMTP_SetPort($InstanceID, $Port)
 	{
		IPS_SetProperty($InstanceID, 'Port', $Port);
	}
}

if (!function_exists('SMTP_SetUseAuthentication'))
{
	function SMTP_SetUseAuthentication($InstanceID, $UseAuthentication)
 	{
		IPS_SetProperty($InstanceID, 'UseAuthentication', $UseAuthentication);
	}
}

if (!function_exists('SMTP_SetUsername'))
{
	function SMTP_SetUsername($InstanceID, $Username)
 	{
		IPS_SetProperty($InstanceID, 'Username', $Username);
	}
}

if (!function_exists('SMTP_SetPassword'))
{
	function SMTP_SetPassword($InstanceID, $Password)
 	{
		IPS_SetProperty($InstanceID, 'Password', $Password);
	}
}

if (!function_exists('SMTP_SetUseSSL'))
{
	function SMTP_SetUseSSL($InstanceID, $UseSSL)
 	{
		IPS_SetProperty($InstanceID, 'UseSSL', $UseSSL);
	}
}

if (!function_exists('SMTP_GetHost'))
{
	function SMTP_GetHost($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Host');
	}
}

if (!function_exists('SMTP_GetPort'))
{
	function SMTP_GetPort($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Port');
	}
}

if (!function_exists('SMTP_GetUseAuthentication'))
{
	function SMTP_GetUseAuthentication($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'UseAuthentication');
	}
}

if (!function_exists('SMTP_GetUsername'))
{
	function SMTP_GetUsername($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Username');
	}
}

if (!function_exists('SMTP_GetPassword'))
{
	function SMTP_GetPassword($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Password');
	}
}

if (!function_exists('SMTP_GetUseSSL'))
{
	function SMTP_GetUseSSL($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'UseSSL');
	}
}

if (!function_exists('SMTP_SetSenderName'))
{
	function SMTP_SetSenderName($InstanceID, $Name)
 	{
		IPS_SetProperty($InstanceID, 'SenderName', $Name);
	}
}

if (!function_exists('SMTP_SetSenderAddress'))
{
	function SMTP_SetSenderAddress($InstanceID, $Address)
 	{
		IPS_SetProperty($InstanceID, 'SenderAddress', $Address);
	}
}

if (!function_exists('SMTP_SetRecipient'))
{
	function SMTP_SetRecipient($InstanceID, $Address)
 	{
		IPS_SetProperty($InstanceID, 'Recipient', $Address);
	}
}

if (!function_exists('SMTP_GetSenderName'))
{
	function SMTP_GetSenderName($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'SenderName');
	}
}

if (!function_exists('SMTP_GetSenderAddress'))
{
	function SMTP_GetSenderAddress($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'SenderAddress');
	}
}

if (!function_exists('SMTP_GetRecipient'))
{
	function SMTP_GetRecipient($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Recipient');
	}
}

if (!function_exists('IMAP_SetHost'))
{
	function IMAP_SetHost($InstanceID, $Host)
 	{
		IPS_SetProperty($InstanceID, 'Host', $Host);
	}
}

if (!function_exists('IMAP_SetPort'))
{
	function IMAP_SetPort($InstanceID, $Port)
 	{
		IPS_SetProperty($InstanceID, 'Port', $Port);
	}
}

if (!function_exists('IMAP_SetUseAuthentication'))
{
	function IMAP_SetUseAuthentication($InstanceID, $UseAuthentication)
 	{
		IPS_SetProperty($InstanceID, 'UseAuthentication', $UseAuthentication);
	}
}

if (!function_exists('IMAP_SetUsername'))
{
	function IMAP_SetUsername($InstanceID, $Username)
 	{
		IPS_SetProperty($InstanceID, 'Username', $Username);
	}
}

if (!function_exists('IMAP_SetPassword'))
{
	function IMAP_SetPassword($InstanceID, $Password)
 	{
		IPS_SetProperty($InstanceID, 'Password', $Password);
	}
}

if (!function_exists('IMAP_SetUseSSL'))
{
	function IMAP_SetUseSSL($InstanceID, $UseSSL)
 	{
		IPS_SetProperty($InstanceID, 'UseSSL', $UseSSL);
	}
}

if (!function_exists('IMAP_GetHost'))
{
	function IMAP_GetHost($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Host');
	}
}

if (!function_exists('IMAP_GetPort'))
{
	function IMAP_GetPort($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Port');
	}
}

if (!function_exists('IMAP_GetUseAuthentication'))
{
	function IMAP_GetUseAuthentication($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'UseAuthentication');
	}
}

if (!function_exists('IMAP_GetUsername'))
{
	function IMAP_GetUsername($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Username');
	}
}

if (!function_exists('IMAP_GetPassword'))
{
	function IMAP_GetPassword($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Password');
	}
}

if (!function_exists('IMAP_GetUseSSL'))
{
	function IMAP_GetUseSSL($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'UseSSL');
	}
}

if (!function_exists('IMAP_SetCacheSize'))
{
	function IMAP_SetCacheSize($InstanceID, $Size)
 	{
		IPS_SetProperty($InstanceID, 'CacheSize', $Size);
	}
}

if (!function_exists('IMAP_GetCacheSize'))
{
	function IMAP_GetCacheSize($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'CacheSize');
	}
}

if (!function_exists('IMAP_SetCacheInterval'))
{
	function IMAP_SetCacheInterval($InstanceID, $Seconds)
 	{
		IPS_SetProperty($InstanceID, 'CacheInterval', $Seconds);
	}
}

if (!function_exists('IMAP_GetCacheInterval'))
{
	function IMAP_GetCacheInterval($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'CacheInterval');
	}
}

if (!function_exists('POP3_SetHost'))
{
	function POP3_SetHost($InstanceID, $Host)
 	{
		IPS_SetProperty($InstanceID, 'Host', $Host);
	}
}

if (!function_exists('POP3_SetPort'))
{
	function POP3_SetPort($InstanceID, $Port)
 	{
		IPS_SetProperty($InstanceID, 'Port', $Port);
	}
}

if (!function_exists('POP3_SetUseAuthentication'))
{
	function POP3_SetUseAuthentication($InstanceID, $UseAuthentication)
 	{
		IPS_SetProperty($InstanceID, 'UseAuthentication', $UseAuthentication);
	}
}

if (!function_exists('POP3_SetUsername'))
{
	function POP3_SetUsername($InstanceID, $Username)
 	{
		IPS_SetProperty($InstanceID, 'Username', $Username);
	}
}

if (!function_exists('POP3_SetPassword'))
{
	function POP3_SetPassword($InstanceID, $Password)
 	{
		IPS_SetProperty($InstanceID, 'Password', $Password);
	}
}

if (!function_exists('POP3_SetUseSSL'))
{
	function POP3_SetUseSSL($InstanceID, $UseSSL)
 	{
		IPS_SetProperty($InstanceID, 'UseSSL', $UseSSL);
	}
}

if (!function_exists('POP3_GetHost'))
{
	function POP3_GetHost($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Host');
	}
}

if (!function_exists('POP3_GetPort'))
{
	function POP3_GetPort($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Port');
	}
}

if (!function_exists('POP3_GetUseAuthentication'))
{
	function POP3_GetUseAuthentication($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'UseAuthentication');
	}
}

if (!function_exists('POP3_GetUsername'))
{
	function POP3_GetUsername($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Username');
	}
}

if (!function_exists('POP3_GetPassword'))
{
	function POP3_GetPassword($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Password');
	}
}

if (!function_exists('POP3_GetUseSSL'))
{
	function POP3_GetUseSSL($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'UseSSL');
	}
}

if (!function_exists('POP3_SetCacheSize'))
{
	function POP3_SetCacheSize($InstanceID, $Size)
 	{
		IPS_SetProperty($InstanceID, 'CacheSize', $Size);
	}
}

if (!function_exists('POP3_GetCacheSize'))
{
	function POP3_GetCacheSize($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'CacheSize');
	}
}

if (!function_exists('POP3_SetCacheInterval'))
{
	function POP3_SetCacheInterval($InstanceID, $Seconds)
 	{
		IPS_SetProperty($InstanceID, 'CacheInterval', $Seconds);
	}
}

if (!function_exists('POP3_GetCacheInterval'))
{
	function POP3_GetCacheInterval($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'CacheInterval');
	}
}

if (!function_exists('SMS_SetSender'))
{
	function SMS_SetSender($InstanceID, $Number)
 	{
		IPS_SetProperty($InstanceID, 'Sender', $Number);
	}
}

if (!function_exists('SMS_SetUsername'))
{
	function SMS_SetUsername($InstanceID, $Username)
 	{
		IPS_SetProperty($InstanceID, 'Username', $Username);
	}
}

if (!function_exists('SMS_SetPassword'))
{
	function SMS_SetPassword($InstanceID, $Password)
 	{
		IPS_SetProperty($InstanceID, 'Password', $Password);
	}
}

if (!function_exists('SMS_SetAPIID'))
{
	function SMS_SetAPIID($InstanceID, $ID)
 	{
		IPS_SetProperty($InstanceID, 'APIID', $ID);
	}
}

if (!function_exists('SMS_GetSender'))
{
	function SMS_GetSender($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Sender');
	}
}

if (!function_exists('SMS_GetUsername'))
{
	function SMS_GetUsername($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Username');
	}
}

if (!function_exists('SMS_GetPassword'))
{
	function SMS_GetPassword($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Password');
	}
}

if (!function_exists('SMS_GetAPIID'))
{
	function SMS_GetAPIID($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'APIID');
	}
}

if (!function_exists('IG_SetImageAddress'))
{
	function IG_SetImageAddress($InstanceID, $URL)
 	{
		IPS_SetProperty($InstanceID, 'ImageAddress', $URL);
	}
}

if (!function_exists('IG_SetImageType'))
{
	function IG_SetImageType($InstanceID, $ImageType)
 	{
		IPS_SetProperty($InstanceID, 'ImageType', $ImageType);
	}
}

if (!function_exists('IG_SetInterval'))
{
	function IG_SetInterval($InstanceID, $Seconds)
 	{
		IPS_SetProperty($InstanceID, 'Interval', $Seconds);
	}
}

if (!function_exists('IG_SetUseBasicAuth'))
{
	function IG_SetUseBasicAuth($InstanceID, $Enabled)
 	{
		IPS_SetProperty($InstanceID, 'UseBasicAuth', $Enabled);
	}
}

if (!function_exists('IG_SetAuthUsername'))
{
	function IG_SetAuthUsername($InstanceID, $Username)
 	{
		IPS_SetProperty($InstanceID, 'AuthUsername', $Username);
	}
}

if (!function_exists('IG_SetAuthPassword'))
{
	function IG_SetAuthPassword($InstanceID, $Password)
 	{
		IPS_SetProperty($InstanceID, 'AuthPassword', $Password);
	}
}

if (!function_exists('IG_GetImageAddress'))
{
	function IG_GetImageAddress($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'ImageAddress');
	}
}

if (!function_exists('IG_GetImageType'))
{
	function IG_GetImageType($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'ImageType');
	}
}

if (!function_exists('IG_GetInterval'))
{
	function IG_GetInterval($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Interval');
	}
}

if (!function_exists('IG_GetUseBasicAuth'))
{
	function IG_GetUseBasicAuth($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'UseBasicAuth');
	}
}

if (!function_exists('IG_GetAuthUsername'))
{
	function IG_GetAuthUsername($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'AuthUsername');
	}
}

if (!function_exists('IG_GetAuthPassword'))
{
	function IG_GetAuthPassword($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'AuthPassword');
	}
}

if (!function_exists('WAC_GetDevices'))
{
	function WAC_GetDevices($InstanceID)
 	{
		$result = Array();
		$json = json_decode(IPS_GetConfigurationForm($InstanceID));
		foreach($json->elements as $element)
		{
			if(isset($element->name) && ($element->name == "Device"))
			{
				foreach($element->options as $option)
					$result[] = $option->label;
			}
		}
		return $result;
	}
}

if (!function_exists('WAC_SetDeviceID'))
{
	function WAC_SetDeviceID($InstanceID, $DeviceID)
 	{
		$searchByName = function($properties, $name) {
			foreach($properties as $property) {
				if($property->name == $name) {
					return $property->value;
				}
			}
		};
		
		$json = json_decode(IPS_GetConfigurationForm($InstanceID));
		foreach($json->elements as $element)
		{
			if(isset($element->name) && ($element->name == "Device"))
			{
				$option = $element->options[$DeviceID];
				IPS_SetProperty($InstanceID, 'DeviceNum', $searchByName($option->value, 'DeviceNum'));
				IPS_SetProperty($InstanceID, 'DeviceDriver', $searchByName($option->value, 'DeviceDriver'));
				IPS_SetProperty($InstanceID, 'DeviceName', $searchByName($option->value, 'DeviceName'));
			}
		}	
	}
}

if (!function_exists('WAC_SetUpdateInterval'))
{
	function WAC_SetUpdateInterval($InstanceID, $Seconds)
 	{
		IPS_SetProperty($InstanceID, 'UpdateInterval', $Seconds);
	}
}

if (!function_exists('WAC_GetDeviceID'))
{
	function WAC_GetDeviceID($InstanceID)
 	{
		$searchByName = function($properties, $name) {
			foreach($properties as $property) {
				if($property->name == $name) {
					return $property->value;
				}
			}
		};
	
		$json = json_decode(IPS_GetConfigurationForm($InstanceID));
		foreach($json->elements as $element)
		{
			if(isset($element->name) && ($element->name == "Device"))
			{
				foreach($element->options as $option) {
					if(IPS_GetProperty($InstanceID, 'DeviceDriver') == $searchByName($option->value, 'DeviceDriver')) {
						return $searchByName($option->value, 'DeviceNum');
					}
				}	
				foreach($element->options as $option) {
					if(IPS_GetProperty($InstanceID, 'DeviceName') == $searchByName($option->value, 'DeviceName')) {
						return $searchByName($option->value, 'DeviceNum');
					}
				}	
				foreach($element->options as $option) {
					if(IPS_GetProperty($InstanceID, 'DeviceNum') == $searchByName($option->value, 'DeviceNum')) {
						return $searchByName($option->value, 'DeviceNum');
					}
				}					
				return -1;
			}
		}	
	}
}

if (!function_exists('WAC_GetUpdateInterval'))
{
	function WAC_GetUpdateInterval($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'UpdateInterval');
	}
}

if (!function_exists('ZW_SetNodeID'))
{
	function ZW_SetNodeID($InstanceID, $Value)
 	{
		return IPS_SetProperty($InstanceID, 'NodeID', $Value);
	}
}

if (!function_exists('ZW_GetNodeID'))
{
	function ZW_GetNodeID($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'NodeID');
	}
}

if (!function_exists('ZW_SetEnforceBasicClass'))
{
	function ZW_SetEnforceBasicClass($InstanceID, $Value)
 	{
		return IPS_SetProperty($InstanceID, 'EnforceBasicClass', $Value);
	}
}

if (!function_exists('ZW_GetEnforceBasicClass'))
{
	function ZW_GetEnforceBasicClass($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'EnforceBasicClass');
	}
}

if (!function_exists('ZW_SetEnableSceneActivationClass'))
{
	function ZW_SetEnableSceneActivationClass($InstanceID, $Value)
 	{
		return IPS_SetProperty($InstanceID, 'EnableSceneActivationClass', $Value);
	}
}

if (!function_exists('ZW_GetEnableSceneActivationClass'))
{
	function ZW_GetEnableSceneActivationClass($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'EnableSceneActivationClass');
	}
}

if (!function_exists('ZW_SetMultiInstanceID'))
{
	function ZW_SetMultiInstanceID($InstanceID, $Value)
 	{
		return IPS_SetProperty($InstanceID, 'MultiInstanceID', $Value);
	}
}

if (!function_exists('ZW_GetMultiInstanceID'))
{
	function ZW_GetMultiInstanceID($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'MultiInstanceID');
	}
}

if (!function_exists('ZW_GetNodeClasses'))
{
	function ZW_GetNodeClasses($InstanceID)
 	{
		return json_decode(IPS_GetProperty($InstanceID, 'NodeClasses'));
	}
}

if (!function_exists('ZW_GetMultiInstanceCount'))
{
	function ZW_GetMultiInstanceCount($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'MultiInstanceCount');
	}
}

if (!function_exists('ZW_GetMultiInstanceClasses'))
{
	function ZW_GetMultiInstanceClasses($InstanceID)
 	{
		return json_decode(IPS_GetProperty($InstanceID, 'MultiInstanceClasses'));
	}
}

if (!function_exists('TextParser_GetRules'))
{
	function TextParser_GetRules($InstanceID)
 	{
		return json_decode(IPS_GetProperty($InstanceID, 'Rules'));
	}
}

if (!function_exists('TextParser_AddRule'))
{
	function TextParser_AddRule($InstanceID, $ParseType, $TagOne, $TagTwo, $Variable)
 	{
		IPS_ApplyChanges($InstanceID);
		$rules = json_decode(IPS_GetProperty($InstanceID, 'Rules'));
		$rules[] = Array("ParseType" => $ParseType, "TagOne" => $TagOne, "TagTwo" => $TagTwo, "Variable" => $Variable);
		return IPS_SetProperty($InstanceID, 'Rules', json_encode($rules));
	}
}

if (!function_exists('TextParser_EditRule'))
{
	function TextParser_EditRule($InstanceID, $Index, $ParseType, $TagOne, $TagTwo, $Variable)
 	{
		IPS_ApplyChanges($InstanceID);
		$rules = json_decode(IPS_GetProperty($InstanceID, 'Rules'));
		if(!isset($Index)) {
			throw Exception("Index not found!");
		}		
		$rules[$Index] = Array("ParseType" => $ParseType, "TagOne" => $TagOne, "TagTwo" => $TagTwo, "Variable" => $Variable);
		return IPS_SetProperty($InstanceID, 'Rules', json_encode($rules));
	}
}

if (!function_exists('TextParser_DeleteRule'))
{
	function TextParser_DeleteRule($InstanceID)
 	{
		IPS_ApplyChanges($InstanceID);
		$rules = json_decode(IPS_GetProperty($InstanceID, 'Rules'));
		if(!isset($Index)) {
			throw Exception("Index not found!");
		}		
		unset($rules[$Index]);
		return IPS_SetProperty($InstanceID, 'Rules', json_encode($rules));
	}
}

if (!function_exists('EC_SetStartupScript'))
{
	function EC_SetStartupScript($InstanceID, $ScriptID)
 	{
		return IPS_SetProperty($InstanceID, 'StartupScript', $ScriptID);
	}
}

if (!function_exists('EC_GetStartupScript'))
{
	function EC_GetStartupScript($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'StartupScript');
	}
}

if (!function_exists('EC_SetShutdownScript'))
{
	function EC_SetShutdownScript($InstanceID, $ScriptID)
 	{
		return IPS_SetProperty($InstanceID, 'ShutdownScript', $ScriptID);
	}
}

if (!function_exists('EC_GetShutdownScript'))
{
	function EC_GetShutdownScript($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'ShutdownScript');
	}
}

if (!function_exists('EC_SetWatchdogScript'))
{
	function EC_SetWatchdogScript($InstanceID, $ScriptID)
 	{
		return IPS_SetProperty($InstanceID, 'WatchdogScript', $ScriptID);
	}
}

if (!function_exists('EC_GetWatchdogScript'))
{
	function EC_GetWatchdogScript($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'WatchdogScript');
	}
}

if (!function_exists('EC_AddStatusEvent'))
{
	function EC_AddStatusEvent($InstanceID, $DeviceID, $ScriptID)
 	{
		IPS_ApplyChanges($InstanceID);
		$events = json_decode(IPS_GetProperty($InstanceID, 'StatusEvents'));
		$events[] = Array("DeviceID" => $DeviceID, "ScriptID" => $ScriptID);
		return IPS_SetProperty($InstanceID, 'StatusEvents', json_encode($events));
	}
}

if (!function_exists('EC_DeleteStatusEvent'))
{
	function EC_DeleteStatusEvent($InstanceID, $DeviceID)
 	{
		IPS_ApplyChanges($InstanceID);
		$events = json_decode(IPS_GetProperty($InstanceID, 'StatusEvents'));
		foreach($events as $key => $value) {
			if($value["DeviceID"] == $DeviceID) {
				unset($events[$key]);
			}
		}
		return IPS_SetProperty($InstanceID, 'StatusEvents', json_encode($events));
	}
}

if (!function_exists('EC_GetStatusEvent'))
{
	function EC_GetStatusEvent($InstanceID)
 	{
		return json_decode(IPS_GetProperty($InstanceID, 'StatusEvents'));
	}
}

if (!function_exists('SC_SetTransmitDevice'))
{
	function SC_SetTransmitDevice($InstanceID, $DeviceID)
 	{
		return IPS_SetProperty($InstanceID, 'TransmitDevice', $DeviceID);
	}
}

if (!function_exists('SC_GetTransmitDevice'))
{
	function SC_GetTransmitDevice($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'TransmitDevice');
	}
}

if (!function_exists('SC_SetTransmitDevice2'))
{
	function SC_SetTransmitDevice2($InstanceID, $DeviceID)
 	{
		return IPS_SetProperty($InstanceID, 'TransmitDevice2', $DeviceID);
	}
}

if (!function_exists('SC_GetTransmitDevice2'))
{
	function SC_GetTransmitDevice2($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'TransmitDevice2');
	}
}

if (!function_exists('SC_SetShutterType'))
{
	function SC_SetShutterType($InstanceID, $Type)
 	{
		return IPS_SetProperty($InstanceID, 'ShutterType', $Type);
	}
}

if (!function_exists('SC_GetShutterType'))
{
	function SC_GetShutterType($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'ShutterType');
	}
}

if (!function_exists('SC_SetDriveDownTimings'))
{
	function SC_SetDriveDownTimings($InstanceID, $Half, $Down, $Close)
 	{
		IPS_SetProperty($InstanceID, 'DriveDownTimeHalf', $Half);
		IPS_SetProperty($InstanceID, 'DriveDownTimeDown', $Down);
		IPS_SetProperty($InstanceID, 'DriveDownTimeClose', $Close);
	}
}

if (!function_exists('SC_GetDriveDownTimings'))
{
	function SC_GetDriveDownTimings($InstanceID)
 	{
		return Array(
			"Half" => IPS_GetProperty($InstanceID, 'DriveDownTimeHalf'),
			"Down" => IPS_GetProperty($InstanceID, 'DriveDownTimeDown'),
			"Close" => IPS_GetProperty($InstanceID, 'DriveDownTimeClose')
		);
	}
}

if (!function_exists('SC_SetDriveUpTimings'))
{
	function SC_SetDriveUpTimings($InstanceID, $Down, $Half, $Open)
 	{
		IPS_SetProperty($InstanceID, 'DriveUpTimeDown', $Down);
		IPS_SetProperty($InstanceID, 'DriveUpTimeHalf', $Half);
		IPS_SetProperty($InstanceID, 'DriveUpTimeOpen', $Open);
	}
}

if (!function_exists('SC_GetDriveUpTimings'))
{
	function SC_GetDriveUpTimings($InstanceID)
 	{
		return Array(
			"Down" => IPS_GetProperty($InstanceID, 'DriveUpTimeDown'),
			"Half" => IPS_GetProperty($InstanceID, 'DriveUpTimeHalf'),
			"Open" => IPS_GetProperty($InstanceID, 'DriveUpTimeOpen')
		);
	}
}

if (!function_exists('SC_SetMotorDelay'))
{
	function SC_SetMotorDelay($InstanceID, $Milliseconds)
 	{
		return IPS_SetProperty($InstanceID, 'MotorDelay', $Milliseconds);
	}
}

if (!function_exists('SC_GetMotorDelay'))
{
	function SC_GetMotorDelay($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'MotorDelay');
	}
}

if (!function_exists('SC_SetHandlerScript'))
{
	function SC_SetHandlerScript($InstanceID, $ScriptID)
 	{
		return IPS_SetProperty($InstanceID, 'HandlerScript', $ScriptID);
	}
}

if (!function_exists('SC_GetHandlerScript'))
{
	function SC_GetHandlerScript($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'HandlerScript');
	}
}

if (!function_exists('HC_SetHandlerScript'))
{
	function HC_SetHandlerScript($InstanceID, $ScriptID)
 	{
		return IPS_SetProperty($InstanceID, 'HandlerScript', $ScriptID);
	}
}

if (!function_exists('HC_GetHandlerScript'))
{
	function HC_GetHandlerScript($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'HandlerScript');
	}
}

if (!function_exists('HC_SetHysteresis'))
{
	function HC_SetHysteresis($InstanceID, $Hysteresis)
 	{
		return IPS_SetProperty($InstanceID, 'Hysteresis', $Hysteresis);
	}
}

if (!function_exists('HC_GetHysteresis'))
{
	function HC_GetHysteresis($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Hysteresis');
	}
}

if (!function_exists('HC_SetSetBack'))
{
	function HC_SetSetBack($InstanceID, $SetBack)
 	{
		return IPS_SetProperty($InstanceID, 'SetBack', $SetBack);
	}
}

if (!function_exists('HC_GetSetBack'))
{
	function HC_GetSetBack($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'SetBack');
	}
}

if (!function_exists('HC_SetSourceVariable'))
{
	function HC_SetSourceVariable($InstanceID, $VariableID)
 	{
		return IPS_SetProperty($InstanceID, 'SourceVariable', $VariableID);
	}
}

if (!function_exists('HC_GetSourceVariable'))
{
	function HC_GetSourceVariable($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'SourceVariable');
	}
}

if (!function_exists('HC_SetResendInterval'))
{
	function HC_SetResendInterval($InstanceID, $Minutes)
 	{
		return IPS_SetProperty($InstanceID, 'ResendInterval', $Minutes);
	}
}

if (!function_exists('HC_GetResendInterval'))
{
	function HC_GetResendInterval($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'ResendInterval');
	}
}

if (!function_exists('HC_AddTransmitDevice'))
{
	function HC_AddTransmitDevice($InstanceID, $DeviceID, $Invert)
 	{
		IPS_ApplyChanges($InstanceID);
		$devices = json_decode(IPS_GetProperty($InstanceID, 'TransmitDevices'));
		$devices[] = Array("DeviceID" => $DeviceID, "Invert" => $Invert);
		return IPS_SetProperty($InstanceID, 'TransmitDevices', json_encode($devices));
	}
}

if (!function_exists('HC_DeleteTransmitDevice'))
{
	function HC_DeleteTransmitDevice($InstanceID, $DeviceID)
 	{
		IPS_ApplyChanges($InstanceID);
		$devices = json_decode(IPS_GetProperty($InstanceID, 'TransmitDevices'));
		foreach($devices as $key => $value) {
			if($value["DeviceID"] == $DeviceID) {
				unset($devices[$key]);
			}
		}
		return IPS_SetProperty($InstanceID, 'TransmitDevices', json_encode($devices));
	}
}

if (!function_exists('HC_GetTransmitDevices'))
{
	function HC_GetTransmitDevices($InstanceID)
 	{
		return json_decode(IPS_GetProperty($InstanceID, 'TransmitDevices'));
	}
}

if (!function_exists('HC_AddSetBackVariable'))
{
	function HC_AddSetBackVariable($InstanceID, $VariableID, $Invert)
 	{
		IPS_ApplyChanges($InstanceID);
		$variables = json_decode(IPS_GetProperty($InstanceID, 'SetBackVariables'));
		$variables[] = Array("VariableID" => $VariableID, "Invert" => $Invert);
		return IPS_SetProperty($InstanceID, 'SetBackVariables', json_encode($variables));
	}
}

if (!function_exists('HC_DeleteSetBackVariable'))
{
	function HC_DeleteSetBackVariable($InstanceID, $VariableID)
 	{
		IPS_ApplyChanges($InstanceID);
		$variables = json_decode(IPS_GetProperty($InstanceID, 'SetBackVariables'));
		foreach($variables as $key => $value) {
			if($value["VariableID"] == $VariableID) {
				unset($variables[$key]);
			}
		}
		return IPS_SetProperty($InstanceID, 'SetBackVariables', json_encode($variables));
	}
}

if (!function_exists('HC_GetSetBackVariables'))
{
	function HC_GetSetBackVariables($InstanceID)
 	{
		return json_decode(IPS_GetProperty($InstanceID, 'SetBackVariables'));
	}
}

if (!function_exists('HC_AddOverrideVariable'))
{
	function HC_AddOverrideVariable($InstanceID, $VariableID, $Invert)
 	{
		IPS_ApplyChanges($InstanceID);
		$variables = json_decode(IPS_GetProperty($InstanceID, 'OverrideVariables'));
		$variables[] = Array("VariableID" => $VariableID, "Invert" => $Invert);
		return IPS_SetProperty($InstanceID, 'OverrideVariables', json_encode($variables));
	}
}

if (!function_exists('HC_DeleteOverrideVariable'))
{
	function HC_DeleteOverrideVariable($InstanceID, $VariableID)
 	{
		IPS_ApplyChanges($InstanceID);
		$variables = json_decode(IPS_GetProperty($InstanceID, 'OverrideVariables'));
		foreach($variables as $key => $value) {
			if($value["VariableID"] == $VariableID) {
				unset($variables[$key]);
			}
		}
		return IPS_SetProperty($InstanceID, 'OverrideVariables', json_encode($variables));
	}
}

if (!function_exists('HC_GetOverrideVariable'))
{
	function HC_GetOverrideVariable($InstanceID)
 	{
		return json_decode(IPS_GetProperty($InstanceID, 'OverrideVariables'));
	}
}
 
if (!function_exists('TMEX_RequestRead'))
{
	function TMEX_RequestRead($InstanceID)
 	{
		return OW_RequestStatus($InstanceID);
	}
} 

if (!function_exists('TMEX_GetQueue'))
{
	function TMEX_GetQueue($InstanceID)
 	{
		return OW_GetQueue($InstanceID);
	}
} 

if (!function_exists('TMEX_EnumerateDevices'))
{
	function TMEX_EnumerateDevices($InstanceID)
	{
		foreach(IPS_GetInstanceListByModuleID("{F462BFF3-6772-4720-8450-49E6E2820643}") as $ID)
		{
			if (IPS_GetInstance($ID)['ConnectionID'] == $InstanceID)
			{
				foreach (OW_GetKnownDevices($ID) as $Device)
				{
					$result[] = $Device['DeviceID'];
				}
				return $result;
			}
		}
		throw new Exception("Cannot find OneWire Configurator instance");
	}
} 

if (!function_exists('TMEX_F05_TogglePin'))
{
	function TMEX_F05_TogglePin($InstanceID)
 	{
		return OW_ToggleMode($InstanceID);
	}
} 

if (!function_exists('TMEX_F05_SetPin'))
{
	function TMEX_F05_SetPin($InstanceID, $Status)
 	{
		return OW_SwitchMode($InstanceID, $Status);
	}
} 

if (!function_exists('TMEX_F12_SetPin'))
{
	function TMEX_F12_SetPin($InstanceID, $Pin, $Status)
 	{
		return OW_SetPin($InstanceID, $Pin, $Status);
	}
} 

if (!function_exists('TMEX_F20_SetPin'))
{
	function TMEX_F20_SetPin($InstanceID, $Pin, $Status)
 	{
		return OW_SetPin($InstanceID, $Pin, $Status);
	}
} 

if (!function_exists('TMEX_F29_SetPin'))
{
	function TMEX_F29_SetPin($InstanceID, $Pin, $Status)
 	{
		return OW_SetPin($InstanceID, $Pin, $Status);
	}
} 

if (!function_exists('TMEX_F29_SetPort'))
{
	function TMEX_F29_SetPort($InstanceID, $Bitmask)
 	{
		return OW_SetPort($InstanceID, $Bitmask);
	}
} 

if (!function_exists('TMEX_F29_SetStrobe'))
{
	function TMEX_F29_SetStrobe($InstanceID, $Status)
 	{
		return OW_SetStrobe($InstanceID, $Status);
	}
} 

if (!function_exists('TMEX_F29_WriteBytes'))
{
	function TMEX_F29_WriteBytes($InstanceID, $Data)
 	{
		return OW_WriteBytes($InstanceID, $Data);
	}
} 

if (!function_exists('TMEX_F29_WriteBytesMasked'))
{
	function TMEX_F29_WriteBytesMasked($InstanceID, $Data, $Mask)
 	{
		return OW_WriteBytesMasked($InstanceID, $Data, $Mask);
	}
} 

if (!function_exists('TMEX_F2C_SetPosition'))
{
	function TMEX_F2C_SetPosition($InstanceID, $Value)
 	{
		return OW_SetPosition($InstanceID, $Value);
	}
} 

if (!function_exists('TMEX_F3A_SetPin'))
{
	function TMEX_F3A_SetPin($InstanceID, $Pin, $Status)
 	{
		return OW_SetPin($InstanceID, $Pin, $Status);
	}
} 

if (!function_exists('TMEX_SetDevice'))
{
	function TMEX_SetDevice($InstanceID, $DeviceID)
 	{
		return IPS_SetProperty($InstanceID, 'DeviceID', $DeviceID);
	}
}

if (!function_exists('TMEX_GetDevice'))
{
	function TMEX_GetDevice($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'DeviceID');
	}
}

if (!function_exists('TMEX_SetInterval'))
{
	function TMEX_SetInterval($InstanceID, $Seconds)
 	{
		return IPS_SetProperty($InstanceID, 'Interval', $Seconds);
	}
}

if (!function_exists('TMEX_GetInterval'))
{
	function TMEX_GetInterval($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Interval');
	}
}

if (!function_exists('TMEX_SetF05Invert'))
{
	function TMEX_SetF05Invert($InstanceID, $Invert)
 	{
		return IPS_SetProperty($InstanceID, 'Invert', $Invert);
	}
}

if (!function_exists('TMEX_GetF05Invert'))
{
	function TMEX_GetF05Invert($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Invert');
	}
}

if (!function_exists('TMEX_SetF05PinType'))
{
	function TMEX_SetF05PinType($InstanceID, $IsInput)
 	{
		if($IsInput)
			return IPS_SetProperty($InstanceID, 'PortSettings', 0);
		else
			return IPS_SetProperty($InstanceID, 'PortSettings', 1);		
	}
}

if (!function_exists('TMEX_GetF05PinType'))
{
	function TMEX_GetF05PinType($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'PortSettings') == 0;
	}
}

if (!function_exists('TMEX_SetF12Invert'))
{
	function TMEX_SetF12Invert($InstanceID, $Pin, $Invert)
 	{
		return IPS_SetProperty($InstanceID, 'Invert'.$Pin, $Invert);
	}
}

if (!function_exists('TMEX_GetF12Invert'))
{
	function TMEX_GetF12Invert($InstanceID, $Pin)
 	{
		return IPS_GetProperty($InstanceID, 'Invert'.$Pin);
	}
}

if (!function_exists('TMEX_SetF12PinType'))
{
	function TMEX_SetF12PinType($InstanceID, $Pin, $IsInput)
 	{
		if($IsInput)
			return IPS_SetProperty($InstanceID, 'PortSettings'.$Pin, 0);
		else
			return IPS_SetProperty($InstanceID, 'PortSettings'.$Pin, 1);		
	}
}

if (!function_exists('TMEX_GetF12PinType'))
{
	function TMEX_GetF12PinType($InstanceID, $Pin)
 	{
		return IPS_GetProperty($InstanceID, 'PortSettings'.$Pin) == 0;
	}
}

if (!function_exists('TMEX_SetF20PortType'))
{
	function TMEX_SetF20PortType($InstanceID, $Port, $IsAnalogInput)
 	{
		if($IsAnalogInput)
			return IPS_SetProperty($InstanceID, 'PortType'.$Port, 0);
		else
			return IPS_SetProperty($InstanceID, 'PortType'.$Port, 1);		
	}
}

if (!function_exists('TMEX_GetF20PortType'))
{
	function TMEX_GetF20PortType($InstanceID, $Port)
 	{
		return IPS_GetProperty($InstanceID, 'PortType'.$Port) == 0;
	}
}

if (!function_exists('TMEX_SetF20Resolution'))
{
	function TMEX_SetF20Resolution($InstanceID, $Port, $Resolution)
 	{
		return IPS_SetProperty($InstanceID, 'Resolution'.$Port, $Resolution);
	}
}

if (!function_exists('TMEX_GetF20Resolution'))
{
	function TMEX_GetF20Resolution($InstanceID, $Port)
 	{
		return IPS_GetProperty($InstanceID, 'Resolution'.$Port);
	}
}

if (!function_exists('TMEX_SetF20Resolution'))
{
	function TMEX_SetF20Resolution($InstanceID, $Port, $Resolution)
 	{
		return IPS_SetProperty($InstanceID, 'Resolution'.$Port, $Resolution);
	}
}

if (!function_exists('TMEX_GetF20Resolution'))
{
	function TMEX_GetF20Resolution($InstanceID, $Port)
 	{
		return IPS_GetProperty($InstanceID, 'Resolution'.$Port);
	}
}

if (!function_exists('TMEX_SetF20Voltage'))
{
	function TMEX_SetF20Voltage($InstanceID, $Port, $Is510Volt)
 	{
		if($Is510Volt)
			return IPS_SetProperty($InstanceID, 'Voltage'.$Port, 1);
		else
			return IPS_SetProperty($InstanceID, 'Voltage'.$Port, 0);
	}
}

if (!function_exists('TMEX_GetF20Voltage'))
{
	function TMEX_GetF20Voltage($InstanceID, $Port)
 	{
		return IPS_GetProperty($InstanceID, 'Voltage'.$Port) == 1;
	}
}

if (!function_exists('TMEX_SetF2CUseCPC'))
{
	function TMEX_SetF2CUseCPC($InstanceID, $UseCPC)
 	{
		return IPS_SetProperty($InstanceID, 'UseCPC', $UseCPC);
	}
}

if (!function_exists('TMEX_GetF2CUseCPC'))
{
	function TMEX_GetF2CUseCPC($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'UseCPC');
	}
}

if (!function_exists('TMEX_SetF28Precision'))
{
	function TMEX_SetF28Precision($InstanceID, $Precision)
 	{
		return IPS_SetProperty($InstanceID, 'Precision', $Precision);
	}
}

if (!function_exists('TMEX_GetF28Precision'))
{
	function TMEX_GetF28Precision($InstanceID)
 	{
		return IPS_GetProperty($InstanceID, 'Precision');
	}
}

if (!function_exists('TMEX_SetF29Invert'))
{
	function TMEX_SetF29Invert($InstanceID, $Pin, $Invert)
 	{
		return IPS_SetProperty($InstanceID, 'Invert'.$Pin, $Invert);
	}
}

if (!function_exists('TMEX_GetF29Invert'))
{
	function TMEX_GetF29Invert($InstanceID, $Pin)
 	{
		return IPS_GetProperty($InstanceID, 'Invert'.$Pin);
	}
}

if (!function_exists('TMEX_SetF29PinType'))
{
	function TMEX_SetF29PinType($InstanceID, $Pin, $IsInput)
 	{
		if($IsInput)
			return IPS_SetProperty($InstanceID, 'PortSettings'.$Pin, 0);
		else
			return IPS_SetProperty($InstanceID, 'PortSettings'.$Pin, 1);		
	}
}

if (!function_exists('TMEX_GetF29PinType'))
{
	function TMEX_GetF29PinType($InstanceID, $Pin)
 	{
		return IPS_GetProperty($InstanceID, 'PortSettings'.$Pin) == 0;
	}
}

if (!function_exists('TMEX_SetF3AInvert'))
{
	function TMEX_SetF3AInvert($InstanceID, $Pin, $Invert)
 	{
		return IPS_SetProperty($InstanceID, 'Invert'.$Pin, $Invert);
	}
}

if (!function_exists('TMEX_GetF3AInvert'))
{
	function TMEX_GetF3AInvert($InstanceID, $Pin)
 	{
		return IPS_GetProperty($InstanceID, 'Invert'.$Pin);
	}
}

if (!function_exists('TMEX_SetF3APinType'))
{
	function TMEX_SetF3APinType($InstanceID, $Pin, $IsInput)
 	{
		if($IsInput)
			return IPS_SetProperty($InstanceID, 'PortSettings'.$Pin, 0);
		else
			return IPS_SetProperty($InstanceID, 'PortSettings'.$Pin, 1);		
	}
}

if (!function_exists('TMEX_GetF3APinType'))
{
	function TMEX_GetF3APinType($InstanceID, $Pin)
 	{
		return IPS_GetProperty($InstanceID, 'PortSettings'.$Pin) == 0;
	}
}

if (!function_exists('ALL4000_ReadConfiguration'))
{
	function ALL4000_ReadConfiguration($InstanceID)
 	{
		return ALL_ReadConfiguration($InstanceID);
	}
} 

if (!function_exists('ALL4000_UpdateValues'))
{
	function ALL4000_UpdateValues($InstanceID)
 	{
		return ALL_UpdateValues($InstanceID);
	}
} 

if (!function_exists('ALL3690_UpdateValues'))
{
	function ALL3690_UpdateValues($InstanceID)
 	{
		return ALL_UpdateValues($InstanceID);
	}
} 
 
if (!function_exists('ALL3691_UpdateValues'))
{
	function ALL3691_UpdateValues($InstanceID)
 	{
		return ALL_UpdateValues($InstanceID);
	}
}  

if (!function_exists('ALL5000_SwitchMode'))
{
	function ALL5000_SwitchMode($InstanceID, $Actor, $Status)
 	{
		return ALL_SwitchActor($InstanceID, $Actor, $Status);
	}
} 
 
if (!function_exists('IPS_GetVariableCompatibility'))
{
	function IPS_GetVariableCompatibility($VariableID)
 	{
		$variable = IPS_GetVariable($VariableID);
		$variable['VariableValue'] = Array(
			"ValueType" => $variable['VariableType'],
			"ValueBoolean" => false,
			"ValueInteger" => 0,
			"ValueFloat" => 0,
			"ValueString" => "",
		);
		switch($variable['VariableType']) {
			case 0:
				$variable['VariableValue']['ValueBoolean'] = GetValueBoolean($VariableID);
				break;
			case 1:
				$variable['VariableValue']['ValueInteger'] = GetValueInteger($VariableID);
				break;
			case 2:
				$variable['VariableValue']['ValueFloat'] = GetValueFloat($VariableID);
				break;
			case 3:
				$variable['VariableValue']['ValueString'] = GetValueString($VariableID);
				break;
		}
		$variable['VariableIsBinary'] = false;
		return $variable;
	}
}

if (!function_exists('IPS_GetScriptCompatibility'))
{
	function IPS_GetScriptCompatibility($ScriptID)
 	{
		$script = IPS_GetScript($ScriptID);
		$script['LastExecute'] = $script['ScriptExecuted'];
		$script['IsBroken'] = $script['ScriptIsBroken'];		
		return $script;
	}
}

if (!function_exists('IPS_GetMediaCompatibility'))
{
	function IPS_GetMediaCompatibility($MediaID)
 	{
		$media = IPS_GetMedia($MediaID);
		$media['IsAvailable'] = $media['MediaIsAvailable'];
		$media['IsLinked'] = false;
		$media['LastUpdate'] = $media['MediaUpdated'];
		$media['SendEvent'] = true;
		return $media;
	}
}

if (!function_exists('IPS_LinkCompatibility'))
{
	function IPS_LinkCompatibility($LinkID)
 	{
		$link = IPS_GetVariable($LinkID);
		$link['LinkChildID'] = $link['TargetID'];
		return $link;
	}
}

if (!function_exists('IPS_SetStatusVariableUseAction'))
{
	function IPS_SetStatusVariableUseAction($InstanceID, $VariableIdent, $UseAction)
 	{
		$id = IPS_GetObjectIDByIdent($VariableIdent, $InstanceID);
		if($id !== false) {
			if($UseAction) {
				IPS_SetVariableCustomAction($id, 0);
			} else {
				IPS_SetVariableCustomAction($id, 1);
			}
		}
	}
}

if (!function_exists('IPS_GetUptime'))
{
	function IPS_GetUptime()
 	{
		return IPS_GetKernelStartTime();
	}
}

if (!function_exists('ZW_RoutingGetNodes'))
{
	function ZW_RoutingGetNodes($GatewayID, $NodeID)
 	{
		foreach(IPS_GetInstanceListByModuleID("{101352E1-88C7-4F16-998B-E20D50779AF6}" /* Z-Wave Module */) as $instanceID) {
			
			if(IPS_GetProperty($instanceID, "NodeID") == $NodeID) {
				if (IPS_GetInstance($instanceID)['ConnectionID'] == $GatewayID) {
					return ZW_RequestRoutingList($instanceID);
				}
			}
		}
		throw new Exception("Could not find the InstanceID for NodeID");
	}
}

if (!function_exists('ZW_RoutingTestNode'))
{
	function ZW_RoutingTestNode($GatewayID, $NodeID)
 	{
		foreach(IPS_GetInstanceListByModuleID("{101352E1-88C7-4F16-998B-E20D50779AF6}" /* Z-Wave Module */) as $instanceID) {
			
			if(IPS_GetProperty($instanceID, "NodeID") == $NodeID) {
				if (IPS_GetInstance($instanceID)['ConnectionID'] == $GatewayID) {
					return ZW_Test($instanceID);
				}
			}
		}
		throw new Exception("Could not find the InstanceID for NodeID");
	}
}

if (!function_exists('ZW_RoutingOptimize'))
{
	function ZW_RoutingOptimize($GatewayID)
 	{
		throw new Exception("Z-Wave Routing will be optimized automatically");
	}
}

if (!function_exists('S7_WriteShortInt'))
{
	function S7_WriteShortInt($InstanceID, $Value)
 	{
		return S7_WriteChar($InstanceID, $Value);
	}
}

if (!function_exists('S7_WriteSmallInt'))
{
	function S7_WriteSmallInt($InstanceID, $Value)
 	{
		return S7_WriteShort($InstanceID, $Value);
	}
}

if (!function_exists('ModBus_WriteShortInt'))
{
	function ModBus_WriteShortInt($InstanceID, $Value)
 	{
		return ModBus_WriteChar($InstanceID, $Value);
	}
}

if (!function_exists('ModBus_WriteSmallInt'))
{
	function ModBus_WriteSmallInt($InstanceID, $Value)
 	{
		return ModBus_WriteShort($InstanceID, $Value);
	}
}

?>