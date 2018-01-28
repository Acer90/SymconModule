<?

class boolean{}; class bool{}; class integer{}; class int{}; class float{}; class string{};
class IPSTypeHintHandler {
	public static function CatchError($ErrLevel, $ErrMessage) {
		if ($ErrLevel == E_RECOVERABLE_ERROR) {
			return strpos($ErrMessage, 'must be an instance of string')
				|| strpos($ErrMessage, 'must be an instance of integer')
				|| strpos($ErrMessage, 'must be an instance of int')
				|| strpos($ErrMessage, 'must be an instance of float')
				|| strpos($ErrMessage, 'must be an instance of boolean')
				|| strpos($ErrMessage, 'must be an instance of bool');
		}
		return false;
	}
}

function IPSView_Resize($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{67E0285A-B72C-446D-BF1A-8EF56EC9D7B9}") {
		require_once('C:\IP-Symcon\modules\IPSView\IPSViewResize\module.php');
		$result = (new IPSViewResize($InstanceID))->Resize();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function HM_ReadPrograms($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{A5010577-C443-4A85-ABF2-3F2D6CDD2465}") {
		require_once('C:\IP-Symcon\modules\IPSHomematicExtended\Programme\module.php');
		$result = (new HomeMaticProgramme($InstanceID))->ReadPrograms();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GDSP_Update($InstanceID, $Filename)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{17B9A5B8-A080-43D7-84A7-2FC72D917D64}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Display\module.php');
		$result = (new IPS2GPIO_Display($InstanceID))->Update($Filename);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GBMP180_Measurement($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{9D970308-36E7-428D-8AC0-D8C1496DDCCA}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_BMP180\module.php');
		$result = (new IPS2GPIO_BMP180($InstanceID))->Measurement();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function ARM_TriggerAlert($InstanceID, $SourceID, $SourceValue)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{98612669-D883-4040-AB6E-2D8E3EAF61DF}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\Alarmierung\module.php');
		$result = (new Alarmierung($InstanceID))->TriggerAlert($SourceID, $SourceValue);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function HM_WritePara($InstanceID, $Parameter)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{F3BF69A4-97F8-4C25-A9B0-79F5D6D2C2C7}") {
		require_once('C:\IP-Symcon\modules\IPSHomematicExtended\ParaInterface\module.php');
		$result = (new ParaInterface($InstanceID))->WritePara($Parameter);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GVS_MotorControl($InstanceID, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{3471AFDF-10CB-4058-9368-F8F1B8034443}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_VideoScreen\module.php');
		$result = (new IPS2GPIO_VideoScreen($InstanceID))->MotorControl($Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GDMR_Set_Intensity($InstanceID, $value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{FCE0DF96-16EE-42BF-A102-71C6FBEA658C}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Dimmer\module.php');
		$result = (new IPS2GPIO_Dimmer($InstanceID))->Set_Intensity($value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2G_SSH_Connect($InstanceID, $Command)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{ED89906D-5B78-4D47-AB62-0BDCEB9AD330}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO\module.php');
		$result = (new IPS2GPIO_IO($InstanceID))->SSH_Connect($Command);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function BA_AddImage($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{FC4367A7-3847-442D-95FD-ABCDEF8D9F65}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\BildArchiv\module.php');
		$result = (new BildArchiv($InstanceID))->AddImage();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GGPS_WarmStart($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{73A6E482-1958-4657-8208-BFF58663426F}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_GPS\module.php');
		$result = (new IPS2GPIO_GPS($InstanceID))->WarmStart();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GOUT_Get_Status($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{62A7308D-1829-4A20-BC4D-8CC27518053B}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Output\module.php');
		$result = (new IPS2GPIO_Output($InstanceID))->Get_Status();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function WD_GetAlertTargets($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{87F47896-4321-8756-94FD-9990BD8D9F54}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\Watchdog\module.php');
		$result = (new Watchdog($InstanceID))->GetAlertTargets();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function HM_WriteValueString2($InstanceID, $Parameter, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{AF50C42B-7183-4992-B04A-FAFB07BB1B90}") {
		require_once('C:\IP-Symcon\modules\IPSHomematicExtended\Systemvariablen\module.php');
		$result = (new HomeMaticSystemvariablen($InstanceID))->WriteValueString2($Parameter, $Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function SamsungTizen_TogglePower($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{65BF76B4-042C-4971-A5CC-292FA5E49C86}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSamsungTizen\module.php');
		$result = (new SamsungTizen($InstanceID))->TogglePower();
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{65BF76B4-042C-4971-A5CC-292FA5E49C86}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSamsungTizen\module.php');
		$result = (new SamsungTizen($InstanceID))->TogglePower();
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{65BF76B4-042C-4971-A5CC-292FA5E49C86}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSamsungTizen\module.php');
		$result = (new SamsungTizen($InstanceID))->TogglePower();
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{65BF76B4-042C-4971-A5CC-292FA5E49C86}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSamsungTizen\module.php');
		$result = (new SamsungTizen($InstanceID))->TogglePower();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GOUT_GetOutput($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{62A7308D-1829-4A20-BC4D-8CC27518053B}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Output\module.php');
		$result = (new IPS2GPIO_Output($InstanceID))->GetOutput();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2G2413_SetPortStatus($InstanceID, $Port, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{3FD4AA09-C291-49D2-8513-4040FC5AA079}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_DS2413\module.php');
		$result = (new IPS2GPIO_DS2413($InstanceID))->SetPortStatus($Port, $Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function HM_ReadSystemVariables($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{AF50C42B-7183-4992-B04A-FAFB07BB1B90}") {
		require_once('C:\IP-Symcon\modules\IPSHomematicExtended\Systemvariablen\module.php');
		$result = (new HomeMaticSystemvariablen($InstanceID))->ReadSystemVariables();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function WSC_SendPacket($InstanceID, $Fin, $OPCode, $Text)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{3AB77A94-3467-4E66-8A73-840B4AD89582}") {
		require_once('C:\IP-Symcon\modules\IPSNetwork\WebSocketClient\module.php');
		$result = (new WebsocketClient($InstanceID))->SendPacket($Fin, $OPCode, $Text);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function AS_GetNextSimulationData($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{87F47896-DD54-442D-94FD-9990BD8D9F54}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\AnwesenheitsSimulation\module.php');
		$result = (new AnwesenheitsSimulation($InstanceID))->GetNextSimulationData();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function BlueIrisCam_CamConfig($InstanceID, $reset, $enable, $pause, $motion, $schedule, $ptzcycle, $ptzevents, $alerts, $record)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIrisCam\module.php');
		$result = (new BlueIrisCam($InstanceID))->CamConfig($reset, $enable, $pause, $motion, $schedule, $ptzcycle, $ptzevents, $alerts, $record);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIrisCam\module.php');
		$result = (new BlueIrisCam($InstanceID))->CamConfig($reset, $enable, $pause, $motion, $schedule, $ptzcycle, $ptzevents, $alerts, $record);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIrisCam\module.php');
		$result = (new BlueIrisCam($InstanceID))->CamConfig($reset, $enable, $pause, $motion, $schedule, $ptzcycle, $ptzevents, $alerts, $record);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIrisCam\module.php');
		$result = (new BlueIrisCam($InstanceID))->CamConfig($reset, $enable, $pause, $motion, $schedule, $ptzcycle, $ptzevents, $alerts, $record);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GCn_Set_Status($InstanceID, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{A2FFEE44-7BA0-4EF9-9B96-975C6A55F4EC}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Circulation\module.php');
		$result = (new IPS2GPIO_Circulation($InstanceID))->Set_Status($Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function HM_AlarmReceipt($InstanceID, $Ident)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{AF50C42B-7183-4992-B04A-FAFB07BB1B90}") {
		require_once('C:\IP-Symcon\modules\IPSHomematicExtended\Systemvariablen\module.php');
		$result = (new HomeMaticSystemvariablen($InstanceID))->AlarmReceipt($Ident);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GSR4_Measurement($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{160688F6-95BF-49A1-9403-6B590DE90B05}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_HCSR04\module.php');
		$result = (new IPS2GPIO_HCSR04($InstanceID))->Measurement();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GDMR_Set_StatusEx($InstanceID, $value, $FadeTime)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{FCE0DF96-16EE-42BF-A102-71C6FBEA658C}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Dimmer\module.php');
		$result = (new IPS2GPIO_Dimmer($InstanceID))->Set_StatusEx($value, $FadeTime);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function BlueIris_SyncData($InstanceID, $createVar)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->SyncData($createVar);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->SyncData($createVar);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->SyncData($createVar);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->SyncData($createVar);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function IQLEX_SendMail($InstanceID, $to, $subject, $content)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{B5D1BEFB-DA80-4063-BB84-92C8BCB5150C}") {
		require_once('C:\IP-Symcon\modules\IQLExchange\IQLExchange\module.php');
		$result = (new IQLExchange($InstanceID))->SendMail($to, $subject, $content);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function SymconWS2812_WakeUp($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{4BF95816-240B-441A-8897-E2BDBF342207}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconWS2812\module.php');
		$result = (new SymconWS2812($InstanceID))->WakeUp();
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{4BF95816-240B-441A-8897-E2BDBF342207}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconWS2812\module.php');
		$result = (new SymconWS2812($InstanceID))->WakeUp();
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{4BF95816-240B-441A-8897-E2BDBF342207}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconWS2812\module.php');
		$result = (new SymconWS2812($InstanceID))->WakeUp();
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{4BF95816-240B-441A-8897-E2BDBF342207}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconWS2812\module.php');
		$result = (new SymconWS2812($InstanceID))->WakeUp();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function BlueIris_AlertList($InstanceID, $session, $camera, $startdate, $reset)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->AlertList($session, $camera, $startdate, $reset);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->AlertList($session, $camera, $startdate, $reset);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->AlertList($session, $camera, $startdate, $reset);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->AlertList($session, $camera, $startdate, $reset);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function AS_UpdateData($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{87F47896-DD54-442D-94FD-9990BD8D9F54}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\AnwesenheitsSimulation\module.php');
		$result = (new AnwesenheitsSimulation($InstanceID))->UpdateData();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GCn_Get_Status($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{A2FFEE44-7BA0-4EF9-9B96-975C6A55F4EC}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Circulation\module.php');
		$result = (new IPS2GPIO_Circulation($InstanceID))->Get_Status();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function IPSView_RestoreByFileName($InstanceID, $file)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{97F5AFD2-DAFB-436D-9C8F-F6672AA96F73}") {
		require_once('C:\IP-Symcon\modules\IPSView\IPSViewBackup\module.php');
		$result = (new IPSViewBackup($InstanceID))->RestoreByFileName($file);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GRPi_PiReboot($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{2586FE50-71F9-4471-8245-955B5839A0F6}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_RPi\module.php');
		$result = (new IPS2GPIO_RPi($InstanceID))->PiReboot();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GGPS_FullColdStart($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{73A6E482-1958-4657-8208-BFF58663426F}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_GPS\module.php');
		$result = (new IPS2GPIO_GPS($InstanceID))->FullColdStart();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function HM_WriteValueFloat2($InstanceID, $Parameter, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{AF50C42B-7183-4992-B04A-FAFB07BB1B90}") {
		require_once('C:\IP-Symcon\modules\IPSHomematicExtended\Systemvariablen\module.php');
		$result = (new HomeMaticSystemvariablen($InstanceID))->WriteValueFloat2($Parameter, $Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function BlueIris_CamList($InstanceID, $session)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->CamList($session);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->CamList($session);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->CamList($session);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->CamList($session);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GGPS_Send($InstanceID, $Message)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{73A6E482-1958-4657-8208-BFF58663426F}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_GPS\module.php');
		$result = (new IPS2GPIO_GPS($InstanceID))->Send($Message);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function DataFlowGenerator_CreateScripts($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{CB4BD64D-7088-42F0-919F-644C00CF0EBE}") {
		require_once('C:\IP-Symcon\modules\IPSymconDataFlowGenerator\PHPModuleFileGenerator\module.php');
		$result = (new DataFlowGenerator($InstanceID))->CreateScripts();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function THL_SetActive($InstanceID, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{9D5546FA-CDB2-49BB-9B1D-F40F21E8219B}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\Treppenhauslichtsteuerung\module.php');
		$result = (new Treppenhauslichtsteuerung($InstanceID))->SetActive($Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GRGB_Toggle_StatusEx($InstanceID, $FadeTime)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{98EDCDCC-79D2-4182-9F0B-6E79D475B358}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_RGB\module.php');
		$result = (new IPS2GPIO_RGB($InstanceID))->Toggle_StatusEx($FadeTime);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GServo_SetOutput($InstanceID, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{0D748199-657B-4B56-80C2-943EE531429F}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Servo\module.php');
		$result = (new IPS2GPIO_Servo($InstanceID))->SetOutput($Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function BlueIris_ClipList($InstanceID, $session, $camera, $startdate, $enddate, $tiles)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->ClipList($session, $camera, $startdate, $enddate, $tiles);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->ClipList($session, $camera, $startdate, $enddate, $tiles);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->ClipList($session, $camera, $startdate, $enddate, $tiles);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->ClipList($session, $camera, $startdate, $enddate, $tiles);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function UMR_Calculate($InstanceID, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{D40C120A-C525-4DEC-9A44-ED6E43890C61}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\Umrechnen\module.php');
		$result = (new Umrechnen($InstanceID))->Calculate($Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GOUT_Set_Status($InstanceID, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{62A7308D-1829-4A20-BC4D-8CC27518053B}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Output\module.php');
		$result = (new IPS2GPIO_Output($InstanceID))->Set_Status($Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GBME680_Measurement($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{54EBA6FB-A557-4CB9-B384-933D6F5155B6}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_BME680\module.php');
		$result = (new IPS2GPIO_BME680($InstanceID))->Measurement();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GServo_GetOutput($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{0D748199-657B-4B56-80C2-943EE531429F}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Servo\module.php');
		$result = (new IPS2GPIO_Servo($InstanceID))->GetOutput();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function IQL4SH_ConvertToV2($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{3F0154A4-AC42-464A-9E9A-6818D775EFC4}") {
		require_once('C:\IP-Symcon\modules\IQL4Symcon\IQL4SmartHome\module.php');
		$result = (new IQL4SmartHome($InstanceID))->ConvertToV2();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GGPS_HotStart($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{73A6E482-1958-4657-8208-BFF58663426F}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_GPS\module.php');
		$result = (new IPS2GPIO_GPS($InstanceID))->HotStart();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GPTLB10VE_GetStatus($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{6DF0D014-EE3E-4FB7-AD45-A8579ABDD2FC}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_PTLB10VE\module.php');
		$result = (new IPS2GPIO_PTLB10VE($InstanceID))->GetStatus();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function WSC_SendPing($InstanceID, $Text)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{3AB77A94-3467-4E66-8A73-840B4AD89582}") {
		require_once('C:\IP-Symcon\modules\IPSNetwork\WebSocketClient\module.php');
		$result = (new WebsocketClient($InstanceID))->SendPing($Text);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GRGBW_Set_RGB($InstanceID, $R, $G, $B)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{E51C7E8D-40B3-45F6-BB91-7E505A614207}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_RGBW\module.php');
		$result = (new IPS2GPIO_RGBW($InstanceID))->Set_RGB($R, $G, $B);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function IQLEX_GetVacation($InstanceID, $user)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{B5D1BEFB-DA80-4063-BB84-92C8BCB5150C}") {
		require_once('C:\IP-Symcon\modules\IQLExchange\IQLExchange\module.php');
		$result = (new IQLExchange($InstanceID))->GetVacation($user);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function ICCN_GetNotifierPresenceReason($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{F22703FF-8576-4AB1-A0E7-02E3116CD3BA}") {
		require_once('C:\IP-Symcon\modules\iCal-Calendar\iCalCalendarNotifier\module.php');
		$result = (new iCalCalendarNotifier($InstanceID))->GetNotifierPresenceReason();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function BlueIris_Logout($InstanceID, $session)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->Logout($session);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->Logout($session);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->Logout($session);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->Logout($session);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2G_OWSearchStart($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{ED89906D-5B78-4D47-AB62-0BDCEB9AD330}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO\module.php');
		$result = (new IPS2GPIO_IO($InstanceID))->OWSearchStart();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function WGW_UpdateStormWarningData($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{EDF6BF77-0E16-4FCD-90E4-9E5C0F91B921}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\WundergroundWeather\module.php');
		$result = (new WundergroundWeather($InstanceID))->UpdateStormWarningData();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GOUT_Toggle_Status($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{62A7308D-1829-4A20-BC4D-8CC27518053B}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Output\module.php');
		$result = (new IPS2GPIO_Output($InstanceID))->Toggle_Status();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GL298N_MotorControl($InstanceID, $Motor, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{24C46FA2-04CC-4421-A5DB-1900ED6B8FD0}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_L298N\module.php');
		$result = (new IPS2GPIO_L298N($InstanceID))->MotorControl($Motor, $Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function FM_SetActive($InstanceID, $Active)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{AB3467A7-CD43-442D-95FD-BCDBCF8D9A67}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\FertigMelder\module.php');
		$result = (new FertigMelder($InstanceID))->SetActive($Active);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function WSTest_SendTestClient($InstanceID, $FrameTyp, $Data, $Fin)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{FC11DB7C-4999-4EA7-B57A-82A878ADD273}") {
		require_once('C:\IP-Symcon\modules\IPSNetwork\WebSocketServerIfTest\module.php');
		$result = (new WebSocketInterfaceTest($InstanceID))->SendTestClient($FrameTyp, $Data, $Fin);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GiAQ_Measurement($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1ABC9D19-31BF-4482-8FE0-6D3843D1D77A}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_iAQ\module.php');
		$result = (new IPS2GPIO_iAQ($InstanceID))->Measurement();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function HM_WriteValueInteger2($InstanceID, $Parameter, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{AF50C42B-7183-4992-B04A-FAFB07BB1B90}") {
		require_once('C:\IP-Symcon\modules\IPSHomematicExtended\Systemvariablen\module.php');
		$result = (new HomeMaticSystemvariablen($InstanceID))->WriteValueInteger2($Parameter, $Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GRGB_Set_RGB($InstanceID, $R, $G, $B)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{98EDCDCC-79D2-4182-9F0B-6E79D475B358}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_RGB\module.php');
		$result = (new IPS2GPIO_RGB($InstanceID))->Set_RGB($R, $G, $B);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function SZS_CallScene($InstanceID, $SceneNumber)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{87F46796-CC43-442D-94FD-AAA0BD8D9F54}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\SzenenSteuerung\module.php');
		$result = (new SzenenSteuerung($InstanceID))->CallScene($SceneNumber);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function HM_RunScript($InstanceID, $Script)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{246EDB89-70BC-403B-A1FA-3B3B1B540401}") {
		require_once('C:\IP-Symcon\modules\IPSHomematicExtended\HomeMaticScript\module.php');
		$result = (new HomeMaticRemoteScript($InstanceID))->RunScript($Script);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function HM_WriteValueDisplayEx($InstanceID, $Text1, $Icon1, $Text2, $Icon2, $Text3, $Icon3, $Chime, $Repeat, $Wait, $Color)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{E64ED916-FA6C-45B2-B8E3-EDC3191BC4C0}") {
		require_once('C:\IP-Symcon\modules\IPSHomematicExtended\ePaperStatusAnzeige\module.php');
		$result = (new HomeMaticDisEPWM55($InstanceID))->WriteValueDisplayEx($Text1, $Icon1, $Text2, $Icon2, $Text3, $Icon3, $Chime, $Repeat, $Wait, $Color);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function IPSView_RestoreByFileIdx($InstanceID, $idx)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{97F5AFD2-DAFB-436D-9C8F-F6672AA96F73}") {
		require_once('C:\IP-Symcon\modules\IPSView\IPSViewBackup\module.php');
		$result = (new IPSViewBackup($InstanceID))->RestoreByFileIdx($idx);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GIO1_Read_Status($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{E1E9F012-A15A-4C05-834E-7893DFE34526}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_PCF8574\module.php');
		$result = (new IPS2GPIO_PCF8574($InstanceID))->Read_Status();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function WD_Weathernow($InstanceID, $value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{EDF6BF77-0E16-4FCD-90E4-9E5C0F91B81F}") {
		require_once('C:\IP-Symcon\modules\Wunderground\Wunderground\module.php');
		$result = (new WundergroundWetter($InstanceID))->Weathernow($value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function WD_Weathernexthours($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{EDF6BF77-0E16-4FCD-90E4-9E5C0F91B81F}") {
		require_once('C:\IP-Symcon\modules\Wunderground\Wunderground\module.php');
		$result = (new WundergroundWetter($InstanceID))->Weathernexthours();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GRGBW_Toggle_StatusEx($InstanceID, $FadeTime)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{E51C7E8D-40B3-45F6-BB91-7E505A614207}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_RGBW\module.php');
		$result = (new IPS2GPIO_RGBW($InstanceID))->Toggle_StatusEx($FadeTime);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GIO1_SetPinOutput($InstanceID, $Pin, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{E1E9F012-A15A-4C05-834E-7893DFE34526}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_PCF8574\module.php');
		$result = (new IPS2GPIO_PCF8574($InstanceID))->SetPinOutput($Pin, $Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GDSP_Send($InstanceID, $Message)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{17B9A5B8-A080-43D7-84A7-2FC72D917D64}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Display\module.php');
		$result = (new IPS2GPIO_Display($InstanceID))->Send($Message);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GVS_Read_Status($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{3471AFDF-10CB-4058-9368-F8F1B8034443}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_VideoScreen\module.php');
		$result = (new IPS2GPIO_VideoScreen($InstanceID))->Read_Status();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GAS3935_Reset($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{BC292F9B-7CAB-4195-A85D-A6228B521E08}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_AS3935\module.php');
		$result = (new IPS2GPIO_AS3935($InstanceID))->Reset();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GRGBW_Get_Status($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{E51C7E8D-40B3-45F6-BB91-7E505A614207}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_RGBW\module.php');
		$result = (new IPS2GPIO_RGBW($InstanceID))->Get_Status();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GRGB_Get_Status($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{98EDCDCC-79D2-4182-9F0B-6E79D475B358}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_RGB\module.php');
		$result = (new IPS2GPIO_RGB($InstanceID))->Get_Status();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GOUT_ToggleOutput($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{62A7308D-1829-4A20-BC4D-8CC27518053B}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Output\module.php');
		$result = (new IPS2GPIO_Output($InstanceID))->ToggleOutput();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GOUT_SetOutput($InstanceID, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{62A7308D-1829-4A20-BC4D-8CC27518053B}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Output\module.php');
		$result = (new IPS2GPIO_Output($InstanceID))->SetOutput($Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function HM_WriteValueDisplayLine($InstanceID, $Line, $Text, $Icon)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{E64ED916-FA6C-45B2-B8E3-EDC3191BC4C0}") {
		require_once('C:\IP-Symcon\modules\IPSHomematicExtended\ePaperStatusAnzeige\module.php');
		$result = (new HomeMaticDisEPWM55($InstanceID))->WriteValueDisplayLine($Line, $Text, $Icon);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GGeCoSRGBW_SetOutputPinStatus($InstanceID, $Group, $Channel, $Status)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{3AB26B93-0DD1-4F5C-AFC8-1C3A855F7D14}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_GeCoS_RGBW\module.php');
		$result = (new IPS2GPIO_GeCoS_RGBW($InstanceID))->SetOutputPinStatus($Group, $Channel, $Status);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2G2438_Measurement($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{53B208B7-96D5-4CFE-B23C-48897493E265}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_DS2438\module.php');
		$result = (new IPS2GPIO_DS2438($InstanceID))->Measurement();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function WD_UpdateWetterWarnung($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{EDF6BF77-0E16-4FCD-90E4-9E5C0F91B81F}") {
		require_once('C:\IP-Symcon\modules\Wunderground\Wunderground\module.php');
		$result = (new WundergroundWetter($InstanceID))->UpdateWetterWarnung();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GAD2_Measurement($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{0EBA825C-47AD-4BC6-AC0D-1ADF9CD55AB2}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_MCP3424\module.php');
		$result = (new IPS2GPIO_MCP3424($InstanceID))->Measurement();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GRGBW_Set_White($InstanceID, $value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{E51C7E8D-40B3-45F6-BB91-7E505A614207}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_RGBW\module.php');
		$result = (new IPS2GPIO_RGBW($InstanceID))->Set_White($value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GBH_Measurement($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{C3884BB9-1D68-4AF7-B73E-357D810042A7}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_BH1750\module.php');
		$result = (new IPS2GPIO_BH1750($InstanceID))->Measurement();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function BlueIrisCam_Trigger($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIrisCam\module.php');
		$result = (new BlueIrisCam($InstanceID))->Trigger();
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIrisCam\module.php');
		$result = (new BlueIrisCam($InstanceID))->Trigger();
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIrisCam\module.php');
		$result = (new BlueIrisCam($InstanceID))->Trigger();
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIrisCam\module.php');
		$result = (new BlueIrisCam($InstanceID))->Trigger();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function OSR_SetValue($InstanceID, $key, $value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{0028DE9E-6155-451A-97E1-7D2D1563F5BA}") {
		require_once('C:\IP-Symcon\modules\SymconLightify\lightifyDevice\module.php');
		$result = (new lightifyDevice($InstanceID))->SetValue($key, $value);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7B315B21-10A7-466B-8F86-8CF069C3F7A2}") {
		require_once('C:\IP-Symcon\modules\SymconLightify\lightifyGroup\module.php');
		$result = (new lightifyGroup($InstanceID))->SetValue($key, $value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function ICCR_CheckCalendarURLSyntax($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5127CDDC-2859-4223-A870-4D26AC83622C}") {
		require_once('C:\IP-Symcon\modules\iCal-Calendar\iCalCalendarReader\module.php');
		$result = (new iCalCalendarReader($InstanceID))->CheckCalendarURLSyntax();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GBT_Measurement($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{A889B9F4-E3B5-4B49-9756-147B62D1C341}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_BT\module.php');
		$result = (new IPS2GPIO_BT($InstanceID))->Measurement();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function HM_ReadWRInterface($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{01C66202-7E94-49C4-8D8F-6A75CE944E87}") {
		require_once('C:\IP-Symcon\modules\IPSHomematicExtended\WRInterface\module.php');
		$result = (new HomeMaticWRInterface($InstanceID))->ReadWRInterface();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function WD_CheckTargets($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{87F47896-4321-8756-94FD-9990BD8D9F54}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\Watchdog\module.php');
		$result = (new Watchdog($InstanceID))->CheckTargets();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function EZS_Update($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{89625B8F-7F45-4D21-9442-9EEC3CAC4A2D}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\EnergiezaehlerStrom\module.php');
		$result = (new EnergiezaehlerStrom($InstanceID))->Update();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GDMR_Toggle_StatusEx($InstanceID, $FadeTime)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{FCE0DF96-16EE-42BF-A102-71C6FBEA658C}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Dimmer\module.php');
		$result = (new IPS2GPIO_Dimmer($InstanceID))->Toggle_StatusEx($FadeTime);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function ZUL_Update($InstanceID, $OldValue, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{57A84449-AA15-4423-80B0-3F98C54EAC03}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\ZaehlerUeberlauf\module.php');
		$result = (new ZaehlerUeberlauf($InstanceID))->Update($OldValue, $Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GDSP_SetBrightness($InstanceID, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{17B9A5B8-A080-43D7-84A7-2FC72D917D64}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Display\module.php');
		$result = (new IPS2GPIO_Display($InstanceID))->SetBrightness($Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GAS3935_Calibrate($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{BC292F9B-7CAB-4195-A85D-A6228B521E08}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_AS3935\module.php');
		$result = (new IPS2GPIO_AS3935($InstanceID))->Calibrate();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GGPS_ColdStart($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{73A6E482-1958-4657-8208-BFF58663426F}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_GPS\module.php');
		$result = (new IPS2GPIO_GPS($InstanceID))->ColdStart();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GDSP_SetSleep($InstanceID, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{17B9A5B8-A080-43D7-84A7-2FC72D917D64}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Display\module.php');
		$result = (new IPS2GPIO_Display($InstanceID))->SetSleep($Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function HM_ReadRFInterfaces($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{6EE35B5B-9DD9-4B23-89F6-37589134852F}") {
		require_once('C:\IP-Symcon\modules\IPSHomematicExtended\RFInterfaceSplitter\module.php');
		$result = (new HomeMaticRFInterfaceSplitter($InstanceID))->ReadRFInterfaces();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function HM_CreateAllRFInstances($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{6EE35B5B-9DD9-4B23-89F6-37589134852F}") {
		require_once('C:\IP-Symcon\modules\IPSHomematicExtended\RFInterfaceSplitter\module.php');
		$result = (new HomeMaticRFInterfaceSplitter($InstanceID))->CreateAllRFInstances();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GAS3935_GetOutput($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{BC292F9B-7CAB-4195-A85D-A6228B521E08}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_AS3935\module.php');
		$result = (new IPS2GPIO_AS3935($InstanceID))->GetOutput();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GBME_Measurement($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{64E6464A-664C-46DE-B49F-8629497ED56F}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_BME280\module.php');
		$result = (new IPS2GPIO_BME280($InstanceID))->Measurement();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GMCP23017_SetOutput($InstanceID, $PortA, $PortB)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{46DD7F84-4844-4F5A-A6AA-7A89C076970B}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_MCP23017\module.php');
		$result = (new IPS2GPIO_MCP23017($InstanceID))->SetOutput($PortA, $PortB);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function RM_Update($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{A7B0B43B-BEB0-4452-B55E-CD8A9A56B052}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\Rechenmodul\module.php');
		$result = (new Rechenmodul($InstanceID))->Update();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function ICCN_GetNotifierPresence($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{F22703FF-8576-4AB1-A0E7-02E3116CD3BA}") {
		require_once('C:\IP-Symcon\modules\iCal-Calendar\iCalCalendarNotifier\module.php');
		$result = (new iCalCalendarNotifier($InstanceID))->GetNotifierPresence();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GCn_Calculate($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{A2FFEE44-7BA0-4EF9-9B96-975C6A55F4EC}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Circulation\module.php');
		$result = (new IPS2GPIO_Circulation($InstanceID))->Calculate();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function IQLEX_SendMailHTML($InstanceID, $to, $subject, $content)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{B5D1BEFB-DA80-4063-BB84-92C8BCB5150C}") {
		require_once('C:\IP-Symcon\modules\IQLExchange\IQLExchange\module.php');
		$result = (new IQLExchange($InstanceID))->SendMailHTML($to, $subject, $content);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GGeCoSRGBW_SetOutputPinValue($InstanceID, $Group, $Channel, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{3AB26B93-0DD1-4F5C-AFC8-1C3A855F7D14}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_GeCoS_RGBW\module.php');
		$result = (new IPS2GPIO_GeCoS_RGBW($InstanceID))->SetOutputPinValue($Group, $Channel, $Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2G_CheckSerial($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{ED89906D-5B78-4D47-AB62-0BDCEB9AD330}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO\module.php');
		$result = (new IPS2GPIO_IO($InstanceID))->CheckSerial();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GL298N_Get_Status($InstanceID, $Motor)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{24C46FA2-04CC-4421-A5DB-1900ED6B8FD0}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_L298N\module.php');
		$result = (new IPS2GPIO_L298N($InstanceID))->Get_Status($Motor);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function BlueIrisCam_AlertList($InstanceID, $startdate, $reset)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIrisCam\module.php');
		$result = (new BlueIrisCam($InstanceID))->AlertList($startdate, $reset);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIrisCam\module.php');
		$result = (new BlueIrisCam($InstanceID))->AlertList($startdate, $reset);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIrisCam\module.php');
		$result = (new BlueIrisCam($InstanceID))->AlertList($startdate, $reset);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIrisCam\module.php');
		$result = (new BlueIrisCam($InstanceID))->AlertList($startdate, $reset);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GVt_Set_Intensity($InstanceID, $value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5F168E40-D7FF-42A5-A82D-1CE19BFD95C4}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Vaillant\module.php');
		$result = (new IPS2GPIO_Vaillant($InstanceID))->Set_Intensity($value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GDMR_Set_Status($InstanceID, $value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{FCE0DF96-16EE-42BF-A102-71C6FBEA658C}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Dimmer\module.php');
		$result = (new IPS2GPIO_Dimmer($InstanceID))->Set_Status($value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function HM_WriteValueBoolean2($InstanceID, $Parameter, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{AF50C42B-7183-4992-B04A-FAFB07BB1B90}") {
		require_once('C:\IP-Symcon\modules\IPSHomematicExtended\Systemvariablen\module.php');
		$result = (new HomeMaticSystemvariablen($InstanceID))->WriteValueBoolean2($Parameter, $Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function ICCR_GetClientConfig($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5127CDDC-2859-4223-A870-4D26AC83622C}") {
		require_once('C:\IP-Symcon\modules\iCal-Calendar\iCalCalendarReader\module.php');
		$result = (new iCalCalendarReader($InstanceID))->GetClientConfig();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function IPSView_CheckMaster($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{67E0285A-B72C-446D-BF1A-8EF56EC9D7B9}") {
		require_once('C:\IP-Symcon\modules\IPSView\IPSViewResize\module.php');
		$result = (new IPSViewResize($InstanceID))->CheckMaster();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function ICCR_UpdateCalendar($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5127CDDC-2859-4223-A870-4D26AC83622C}") {
		require_once('C:\IP-Symcon\modules\iCal-Calendar\iCalCalendarReader\module.php');
		$result = (new iCalCalendarReader($InstanceID))->UpdateCalendar();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function SZS_SaveScene($InstanceID, $SceneNumber)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{87F46796-CC43-442D-94FD-AAA0BD8D9F54}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\SzenenSteuerung\module.php');
		$result = (new SzenenSteuerung($InstanceID))->SaveScene($SceneNumber);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function ICCR_TriggerNotifications($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5127CDDC-2859-4223-A870-4D26AC83622C}") {
		require_once('C:\IP-Symcon\modules\iCal-Calendar\iCalCalendarReader\module.php');
		$result = (new iCalCalendarReader($InstanceID))->TriggerNotifications();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function ICCR_UpdateClientConfig($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5127CDDC-2859-4223-A870-4D26AC83622C}") {
		require_once('C:\IP-Symcon\modules\iCal-Calendar\iCalCalendarReader\module.php');
		$result = (new iCalCalendarReader($InstanceID))->UpdateClientConfig();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function ICCR_GetCachedCalendar($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5127CDDC-2859-4223-A870-4D26AC83622C}") {
		require_once('C:\IP-Symcon\modules\iCal-Calendar\iCalCalendarReader\module.php');
		$result = (new iCalCalendarReader($InstanceID))->GetCachedCalendar();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function HM_ResetTimer($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{271BCAB1-0658-46D9-A164-985AEB641B48}") {
		require_once('C:\IP-Symcon\modules\IPSHomematicExtended\DisplayStatusAnzeige\module.php');
		$result = (new HomeMaticDisWM55($InstanceID))->ResetTimer();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function HM_WriteValueDisplayNotify($InstanceID, $Chime, $Repeat, $Wait, $Color)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{E64ED916-FA6C-45B2-B8E3-EDC3191BC4C0}") {
		require_once('C:\IP-Symcon\modules\IPSHomematicExtended\ePaperStatusAnzeige\module.php');
		$result = (new HomeMaticDisEPWM55($InstanceID))->WriteValueDisplayNotify($Chime, $Repeat, $Wait, $Color);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function HM_WriteValueDisplayLineEx($InstanceID, $Line, $Text, $Icon, $Chime, $Repeat, $Wait, $Color)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{E64ED916-FA6C-45B2-B8E3-EDC3191BC4C0}") {
		require_once('C:\IP-Symcon\modules\IPSHomematicExtended\ePaperStatusAnzeige\module.php');
		$result = (new HomeMaticDisEPWM55($InstanceID))->WriteValueDisplayLineEx($Line, $Text, $Icon, $Chime, $Repeat, $Wait, $Color);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function HM_WriteValueDisplay($InstanceID, $Text1, $Icon1, $Text2, $Icon2, $Text3, $Icon3)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{E64ED916-FA6C-45B2-B8E3-EDC3191BC4C0}") {
		require_once('C:\IP-Symcon\modules\IPSHomematicExtended\ePaperStatusAnzeige\module.php');
		$result = (new HomeMaticDisEPWM55($InstanceID))->WriteValueDisplay($Text1, $Icon1, $Text2, $Icon2, $Text3, $Icon3);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function HM_ReadPara($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{F3BF69A4-97F8-4C25-A9B0-79F5D6D2C2C7}") {
		require_once('C:\IP-Symcon\modules\IPSHomematicExtended\ParaInterface\module.php');
		$result = (new ParaInterface($InstanceID))->ReadPara();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function HM_StartProgram($InstanceID, $Parameter)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{A5010577-C443-4A85-ABF2-3F2D6CDD2465}") {
		require_once('C:\IP-Symcon\modules\IPSHomematicExtended\Programme\module.php');
		$result = (new HomeMaticProgramme($InstanceID))->StartProgram($Parameter);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GMCP23017_GetOutput($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{46DD7F84-4844-4F5A-A6AA-7A89C076970B}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_MCP23017\module.php');
		$result = (new IPS2GPIO_MCP23017($InstanceID))->GetOutput();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function HM_SystemVariablesTimer($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{AF50C42B-7183-4992-B04A-FAFB07BB1B90}") {
		require_once('C:\IP-Symcon\modules\IPSHomematicExtended\Systemvariablen\module.php');
		$result = (new HomeMaticSystemvariablen($InstanceID))->SystemVariablesTimer();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function WSC_SendText($InstanceID, $Text)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{3AB77A94-3467-4E66-8A73-840B4AD89582}") {
		require_once('C:\IP-Symcon\modules\IPSNetwork\WebSocketClient\module.php');
		$result = (new WebsocketClient($InstanceID))->SendText($Text);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function IPSWINSNMP_SyncData($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->SyncData();
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->SyncData();
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->SyncData();
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->SyncData();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function WSC_Keepalive($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{3AB77A94-3467-4E66-8A73-840B4AD89582}") {
		require_once('C:\IP-Symcon\modules\IPSNetwork\WebSocketClient\module.php');
		$result = (new WebsocketClient($InstanceID))->Keepalive();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function WSS_KeepAlive($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7869923C-6E1D-4E66-A0BD-627FAD1679C2}") {
		require_once('C:\IP-Symcon\modules\IPSNetwork\WebSocketServer\module.php');
		$result = (new WebsocketServer($InstanceID))->KeepAlive();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function WSS_SendPing($InstanceID, $ClientIP, $ClientPort, $Text)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7869923C-6E1D-4E66-A0BD-627FAD1679C2}") {
		require_once('C:\IP-Symcon\modules\IPSNetwork\WebSocketServer\module.php');
		$result = (new WebsocketServer($InstanceID))->SendPing($ClientIP, $ClientPort, $Text);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function WSTest_SendTestServer($InstanceID, $ClientIP, $FrameTyp, $Data, $Fin)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{FC11DB7C-4999-4EA7-B57A-82A878ADD273}") {
		require_once('C:\IP-Symcon\modules\IPSNetwork\WebSocketServerIfTest\module.php');
		$result = (new WebSocketInterfaceTest($InstanceID))->SendTestServer($ClientIP, $FrameTyp, $Data, $Fin);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function IPSView_Backup($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{97F5AFD2-DAFB-436D-9C8F-F6672AA96F73}") {
		require_once('C:\IP-Symcon\modules\IPSView\IPSViewBackup\module.php');
		$result = (new IPSViewBackup($InstanceID))->Backup();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GIO1_SetOutput($InstanceID, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{E1E9F012-A15A-4C05-834E-7893DFE34526}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_PCF8574\module.php');
		$result = (new IPS2GPIO_PCF8574($InstanceID))->SetOutput($Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function IPSView_CheckViewBackup($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{97F5AFD2-DAFB-436D-9C8F-F6672AA96F73}") {
		require_once('C:\IP-Symcon\modules\IPSView\IPSViewBackup\module.php');
		$result = (new IPSViewBackup($InstanceID))->CheckViewBackup();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function IPSView_PurgeBackupFiles($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{97F5AFD2-DAFB-436D-9C8F-F6672AA96F73}") {
		require_once('C:\IP-Symcon\modules\IPSView\IPSViewBackup\module.php');
		$result = (new IPSViewBackup($InstanceID))->PurgeBackupFiles();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GRPi_PiShutdown($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{2586FE50-71F9-4471-8245-955B5839A0F6}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_RPi\module.php');
		$result = (new IPS2GPIO_RPi($InstanceID))->PiShutdown();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GGeCoSRGBW_SetOutputPinStatusEx($InstanceID, $Group, $Channel, $Status, $FadeTime)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{3AB26B93-0DD1-4F5C-AFC8-1C3A855F7D14}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_GeCoS_RGBW\module.php');
		$result = (new IPS2GPIO_GeCoS_RGBW($InstanceID))->SetOutputPinStatusEx($Group, $Channel, $Status, $FadeTime);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function IPSView_GetBackupFiles($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{97F5AFD2-DAFB-436D-9C8F-F6672AA96F73}") {
		require_once('C:\IP-Symcon\modules\IPSView\IPSViewBackup\module.php');
		$result = (new IPSViewBackup($InstanceID))->GetBackupFiles();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function DataFlowGenerator_GenerateGUID($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{CB4BD64D-7088-42F0-919F-644C00CF0EBE}") {
		require_once('C:\IP-Symcon\modules\IPSymconDataFlowGenerator\PHPModuleFileGenerator\module.php');
		$result = (new DataFlowGenerator($InstanceID))->GenerateGUID();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function IQL4SH_GetObjectList($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{3F0154A4-AC42-464A-9E9A-6818D775EFC4}") {
		require_once('C:\IP-Symcon\modules\IQL4Symcon\IQL4SmartHome\module.php');
		$result = (new IQL4SmartHome($InstanceID))->GetObjectList();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function IQLEX_Update($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{B5D1BEFB-DA80-4063-BB84-92C8BCB5150C}") {
		require_once('C:\IP-Symcon\modules\IQLExchange\IQLExchange\module.php');
		$result = (new IQLExchange($InstanceID))->Update();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function OWN_ModulSelfUpdate($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{136103EF-9DAA-44EB-8CD6-54260471ADDB}") {
		require_once('C:\IP-Symcon\modules\ownCloud\Cloud\module.php');
		$result = (new IPSownCloud($InstanceID))->ModulSelfUpdate();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function OWN_Update($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{136103EF-9DAA-44EB-8CD6-54260471ADDB}") {
		require_once('C:\IP-Symcon\modules\ownCloud\Cloud\module.php');
		$result = (new IPSownCloud($InstanceID))->Update();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GRGB_Set_Status($InstanceID, $value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{98EDCDCC-79D2-4182-9F0B-6E79D475B358}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_RGB\module.php');
		$result = (new IPS2GPIO_RGB($InstanceID))->Set_Status($value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function OSR_SetValueEx($InstanceID, $key, $value, $transition)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{0028DE9E-6155-451A-97E1-7D2D1563F5BA}") {
		require_once('C:\IP-Symcon\modules\SymconLightify\lightifyDevice\module.php');
		$result = (new lightifyDevice($InstanceID))->SetValueEx($key, $value, $transition);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7B315B21-10A7-466B-8F86-8CF069C3F7A2}") {
		require_once('C:\IP-Symcon\modules\SymconLightify\lightifyGroup\module.php');
		$result = (new lightifyGroup($InstanceID))->SetValueEx($key, $value, $transition);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function OSR_GetValue($InstanceID, $key)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{0028DE9E-6155-451A-97E1-7D2D1563F5BA}") {
		require_once('C:\IP-Symcon\modules\SymconLightify\lightifyDevice\module.php');
		$result = (new lightifyDevice($InstanceID))->GetValue($key);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7B315B21-10A7-466B-8F86-8CF069C3F7A2}") {
		require_once('C:\IP-Symcon\modules\SymconLightify\lightifyGroup\module.php');
		$result = (new lightifyGroup($InstanceID))->GetValue($key);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function OSR_GetValueEx($InstanceID, $key)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{0028DE9E-6155-451A-97E1-7D2D1563F5BA}") {
		require_once('C:\IP-Symcon\modules\SymconLightify\lightifyDevice\module.php');
		$result = (new lightifyDevice($InstanceID))->GetValueEx($key);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7B315B21-10A7-466B-8F86-8CF069C3F7A2}") {
		require_once('C:\IP-Symcon\modules\SymconLightify\lightifyGroup\module.php');
		$result = (new lightifyGroup($InstanceID))->GetValueEx($key);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function OSR_getLightifyData($InstanceID, $localMethod)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{C3859938-D71C-4714-8B02-F2889A62F481}") {
		require_once('C:\IP-Symcon\modules\SymconLightify\lightifyGateway\module.php');
		$result = (new lightifyGateway($InstanceID))->getLightifyData($localMethod);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function ARM_SetAlert($InstanceID, $Status)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{98612669-D883-4040-AB6E-2D8E3EAF61DF}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\Alarmierung\module.php');
		$result = (new Alarmierung($InstanceID))->SetAlert($Status);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function ARM_ConvertToNewVersion($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{98612669-D883-4040-AB6E-2D8E3EAF61DF}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\Alarmierung\module.php');
		$result = (new Alarmierung($InstanceID))->ConvertToNewVersion();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function ARM_SetActive($InstanceID, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{98612669-D883-4040-AB6E-2D8E3EAF61DF}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\Alarmierung\module.php');
		$result = (new Alarmierung($InstanceID))->SetActive($Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function AS_SetSimulation($InstanceID, $SwitchOn)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{87F47896-DD54-442D-94FD-9990BD8D9F54}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\AnwesenheitsSimulation\module.php');
		$result = (new AnwesenheitsSimulation($InstanceID))->SetSimulation($SwitchOn);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function AS_UpdateTargets($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{87F47896-DD54-442D-94FD-9990BD8D9F54}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\AnwesenheitsSimulation\module.php');
		$result = (new AnwesenheitsSimulation($InstanceID))->UpdateTargets();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function FM_CheckEvent($InstanceID, $Eventtype)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{AB3467A7-CD43-442D-95FD-BCDBCF8D9A67}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\FertigMelder\module.php');
		$result = (new FertigMelder($InstanceID))->CheckEvent($Eventtype);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function THL_Start($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{9D5546FA-CDB2-49BB-9B1D-F40F21E8219B}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\Treppenhauslichtsteuerung\module.php');
		$result = (new Treppenhauslichtsteuerung($InstanceID))->Start();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function BlueIris_Login($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->Login();
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->Login();
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->Login();
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->Login();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function THL_Stop($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{9D5546FA-CDB2-49BB-9B1D-F40F21E8219B}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\Treppenhauslichtsteuerung\module.php');
		$result = (new Treppenhauslichtsteuerung($InstanceID))->Stop();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function UMG_Calculate($InstanceID, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{D40D120A-C525-4DFC-9F44-ED6E43890C63}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\UmrechnenMultiGrenzen\module.php');
		$result = (new UmrechnenMultiGrenzen($InstanceID))->Calculate($Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function UWZ_RequestInfo($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{E5AA629B-75BD-45C0-9BCB-845C102B0411}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\Unwetterzentrale\module.php');
		$result = (new Unwetterzentrale($InstanceID))->RequestInfo();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function USBM_FixPorts($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1D7367A7-1337-7331-95FD-1479EF8D9177}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\USBMapper\module.php');
		$result = (new USBMapper($InstanceID))->FixPorts();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function WAA_CheckAlert($InstanceID, $ThresholdName, $BufferName)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{98F467A7-CD43-442D-95FD-AAA0CE8D9F65}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\WasserAlarm\module.php');
		$result = (new WasserAlarm($InstanceID))->CheckAlert($ThresholdName, $BufferName);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function IPSWINSNMP_ChangeValue($InstanceID, $instance, $value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->ChangeValue($instance, $value);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->ChangeValue($instance, $value);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->ChangeValue($instance, $value);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->ChangeValue($instance, $value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function WD_SetActive($InstanceID, $SwitchOn)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{87F47896-4321-8756-94FD-9990BD8D9F54}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\Watchdog\module.php');
		$result = (new Watchdog($InstanceID))->SetActive($SwitchOn);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function WGW_UpdateWeatherData($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{EDF6BF77-0E16-4FCD-90E4-9E5C0F91B921}") {
		require_once('C:\IP-Symcon\modules\SymconMisc\WundergroundWeather\module.php');
		$result = (new WundergroundWeather($InstanceID))->UpdateWeatherData();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function BlueIris_CamConfig($InstanceID, $session, $camera, $reset, $enable, $pause, $motion, $schedule, $ptzcycle, $ptzevents, $alerts, $record)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->CamConfig($session, $camera, $reset, $enable, $pause, $motion, $schedule, $ptzcycle, $ptzevents, $alerts, $record);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->CamConfig($session, $camera, $reset, $enable, $pause, $motion, $schedule, $ptzcycle, $ptzevents, $alerts, $record);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->CamConfig($session, $camera, $reset, $enable, $pause, $motion, $schedule, $ptzcycle, $ptzevents, $alerts, $record);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->CamConfig($session, $camera, $reset, $enable, $pause, $motion, $schedule, $ptzcycle, $ptzevents, $alerts, $record);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function BlueIris_Log($InstanceID, $session)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->Log($session);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->Log($session);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->Log($session);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->Log($session);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function BlueIris_PTZ($InstanceID, $session, $camera, $button, $updown)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->PTZ($session, $camera, $button, $updown);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->PTZ($session, $camera, $button, $updown);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->PTZ($session, $camera, $button, $updown);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->PTZ($session, $camera, $button, $updown);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function BlueIris_Status($InstanceID, $session, $signal, $profil, $dio, $play)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->Status($session, $signal, $profil, $dio, $play);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->Status($session, $signal, $profil, $dio, $play);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->Status($session, $signal, $profil, $dio, $play);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->Status($session, $signal, $profil, $dio, $play);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2G2413_Measurement($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{3FD4AA09-C291-49D2-8513-4040FC5AA079}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_DS2413\module.php');
		$result = (new IPS2GPIO_DS2413($InstanceID))->Measurement();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function BlueIris_SysConfig($InstanceID, $session)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->SysConfig($session);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->SysConfig($session);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->SysConfig($session);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->SysConfig($session);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function BlueIris_Trigger($InstanceID, $session, $camera)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->Trigger($session, $camera);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->Trigger($session, $camera);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->Trigger($session, $camera);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{7E62F9B0-5474-426F-B91B-E25F4B25A824}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIris\module.php');
		$result = (new BlueIris($InstanceID))->Trigger($session, $camera);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function BlueIrisCam_ClipList($InstanceID, $startdate, $enddate, $tiles)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIrisCam\module.php');
		$result = (new BlueIrisCam($InstanceID))->ClipList($startdate, $enddate, $tiles);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIrisCam\module.php');
		$result = (new BlueIrisCam($InstanceID))->ClipList($startdate, $enddate, $tiles);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIrisCam\module.php');
		$result = (new BlueIrisCam($InstanceID))->ClipList($startdate, $enddate, $tiles);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIrisCam\module.php');
		$result = (new BlueIrisCam($InstanceID))->ClipList($startdate, $enddate, $tiles);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function BlueIrisCam_PTZ($InstanceID, $button, $updown)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIrisCam\module.php');
		$result = (new BlueIrisCam($InstanceID))->PTZ($button, $updown);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIrisCam\module.php');
		$result = (new BlueIrisCam($InstanceID))->PTZ($button, $updown);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIrisCam\module.php');
		$result = (new BlueIrisCam($InstanceID))->PTZ($button, $updown);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5308D185-A3D2-42D0-B6CE-E9D3080CE184}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconBlueIrisCam\module.php');
		$result = (new BlueIrisCam($InstanceID))->PTZ($button, $updown);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function SamsungTizen_WakeUp($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{65BF76B4-042C-4971-A5CC-292FA5E49C86}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSamsungTizen\module.php');
		$result = (new SamsungTizen($InstanceID))->WakeUp();
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{65BF76B4-042C-4971-A5CC-292FA5E49C86}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSamsungTizen\module.php');
		$result = (new SamsungTizen($InstanceID))->WakeUp();
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{65BF76B4-042C-4971-A5CC-292FA5E49C86}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSamsungTizen\module.php');
		$result = (new SamsungTizen($InstanceID))->WakeUp();
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{65BF76B4-042C-4971-A5CC-292FA5E49C86}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSamsungTizen\module.php');
		$result = (new SamsungTizen($InstanceID))->WakeUp();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function SamsungTizen_SendKeys($InstanceID, $keys)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{65BF76B4-042C-4971-A5CC-292FA5E49C86}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSamsungTizen\module.php');
		$result = (new SamsungTizen($InstanceID))->SendKeys($keys);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{65BF76B4-042C-4971-A5CC-292FA5E49C86}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSamsungTizen\module.php');
		$result = (new SamsungTizen($InstanceID))->SendKeys($keys);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{65BF76B4-042C-4971-A5CC-292FA5E49C86}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSamsungTizen\module.php');
		$result = (new SamsungTizen($InstanceID))->SendKeys($keys);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{65BF76B4-042C-4971-A5CC-292FA5E49C86}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSamsungTizen\module.php');
		$result = (new SamsungTizen($InstanceID))->SendKeys($keys);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function SamsungTizen_CheckOnline($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{65BF76B4-042C-4971-A5CC-292FA5E49C86}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSamsungTizen\module.php');
		$result = (new SamsungTizen($InstanceID))->CheckOnline();
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{65BF76B4-042C-4971-A5CC-292FA5E49C86}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSamsungTizen\module.php');
		$result = (new SamsungTizen($InstanceID))->CheckOnline();
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{65BF76B4-042C-4971-A5CC-292FA5E49C86}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSamsungTizen\module.php');
		$result = (new SamsungTizen($InstanceID))->CheckOnline();
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{65BF76B4-042C-4971-A5CC-292FA5E49C86}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSamsungTizen\module.php');
		$result = (new SamsungTizen($InstanceID))->CheckOnline();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function IPSWINSNMP_ReadSNMP($InstanceID, $oid)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->ReadSNMP($oid);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->ReadSNMP($oid);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->ReadSNMP($oid);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->ReadSNMP($oid);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function IPSWINSNMP_WalkSNMP($InstanceID, $oid_st, $oid_end)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->WalkSNMP($oid_st, $oid_end);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->WalkSNMP($oid_st, $oid_end);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->WalkSNMP($oid_st, $oid_end);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->WalkSNMP($oid_st, $oid_end);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function IPSWINSNMP_WriteSNMP($InstanceID, $oid, $value, $type)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->WriteSNMP($oid, $value, $type);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->WriteSNMP($oid, $value, $type);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->WriteSNMP($oid, $value, $type);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->WriteSNMP($oid, $value, $type);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GGeCoSRGBW_ToggleOutputPinStatusEx($InstanceID, $Group, $Channel, $FadeTime)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{3AB26B93-0DD1-4F5C-AFC8-1C3A855F7D14}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_GeCoS_RGBW\module.php');
		$result = (new IPS2GPIO_GeCoS_RGBW($InstanceID))->ToggleOutputPinStatusEx($Group, $Channel, $FadeTime);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function IPSWINSNMP_GetPorts($InstanceID, $status, $util, $utyp)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->GetPorts($status, $util, $utyp);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->GetPorts($status, $util, $utyp);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->GetPorts($status, $util, $utyp);
	}
	elseif(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{1A75660D-48AE-4B89-B351-957CAEBEF22D}") {
		require_once('C:\IP-Symcon\modules\SymconModule\SymconSmnp\module.php');
		$result = (new IPSWINSNMP($InstanceID))->GetPorts($status, $util, $utyp);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2G_PIGPIOD_Restart($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{ED89906D-5B78-4D47-AB62-0BDCEB9AD330}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO\module.php');
		$result = (new IPS2GPIO_IO($InstanceID))->PIGPIOD_Restart();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2G1W_Measurement($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{A66E8B5C-B2DD-4D31-9BC0-08865E714107}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_1Wire\module.php');
		$result = (new IPS2GPIO_1Wire($InstanceID))->Measurement();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GCn_SetPumpState($InstanceID, $State)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{A2FFEE44-7BA0-4EF9-9B96-975C6A55F4EC}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Circulation\module.php');
		$result = (new IPS2GPIO_Circulation($InstanceID))->SetPumpState($State);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GDMR_Get_Status($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{FCE0DF96-16EE-42BF-A102-71C6FBEA658C}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Dimmer\module.php');
		$result = (new IPS2GPIO_Dimmer($InstanceID))->Get_Status();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GDMR_Toggle_Status($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{FCE0DF96-16EE-42BF-A102-71C6FBEA658C}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Dimmer\module.php');
		$result = (new IPS2GPIO_Dimmer($InstanceID))->Toggle_Status();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GDSP_Reset($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{17B9A5B8-A080-43D7-84A7-2FC72D917D64}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Display\module.php');
		$result = (new IPS2GPIO_Display($InstanceID))->Reset();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2G18B20_Measurement($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{DDBD28D4-3E91-4375-9CBB-E887695D5694}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_DS18B20\module.php');
		$result = (new IPS2GPIO_DS18B20($InstanceID))->Measurement();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2G18S20_Measurement($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{EE73E787-B51B-44A8-B474-CF4B8A736AFF}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_DS18S20\module.php');
		$result = (new IPS2GPIO_DS18S20($InstanceID))->Measurement();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GGeCoSPWM16Out_SetOutputPinValue($InstanceID, $Output, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{2ED6393D-E9A6-4C68-824C-90530EDDCE5C}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_GeCoS_PWM16Out\module.php');
		$result = (new IPS2GPIO_GeCoS_PWM16Out($InstanceID))->SetOutputPinValue($Output, $Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GGeCoSPWM16Out_SetOutputPinStatus($InstanceID, $Output, $Status)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{2ED6393D-E9A6-4C68-824C-90530EDDCE5C}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_GeCoS_PWM16Out\module.php');
		$result = (new IPS2GPIO_GeCoS_PWM16Out($InstanceID))->SetOutputPinStatus($Output, $Status);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GGeCoSPWM16Out_ToggleOutputPinStatus($InstanceID, $Output)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{2ED6393D-E9A6-4C68-824C-90530EDDCE5C}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_GeCoS_PWM16Out\module.php');
		$result = (new IPS2GPIO_GeCoS_PWM16Out($InstanceID))->ToggleOutputPinStatus($Output);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GGeCoSRGBW_ToggleOutputPinStatus($InstanceID, $Group, $Channel)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{3AB26B93-0DD1-4F5C-AFC8-1C3A855F7D14}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_GeCoS_RGBW\module.php');
		$result = (new IPS2GPIO_GeCoS_RGBW($InstanceID))->ToggleOutputPinStatus($Group, $Channel);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GGeCoSRGBW_SetOutputPinColor($InstanceID, $Group, $Color)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{3AB26B93-0DD1-4F5C-AFC8-1C3A855F7D14}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_GeCoS_RGBW\module.php');
		$result = (new IPS2GPIO_GeCoS_RGBW($InstanceID))->SetOutputPinColor($Group, $Color);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GMCP23017_SetOutputPin($InstanceID, $Port, $Pin, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{46DD7F84-4844-4F5A-A6AA-7A89C076970B}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_MCP23017\module.php');
		$result = (new IPS2GPIO_MCP23017($InstanceID))->SetOutputPin($Port, $Pin, $Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GPCF8583_Measurement($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{95276FA0-4847-411E-B700-2E5F1866A7F6}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_PCF8583\module.php');
		$result = (new IPS2GPIO_PCF8583($InstanceID))->Measurement();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GAD1_Measurement($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{A2E052CE-055C-4249-A536-7082B233B583}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_PCF8591\module.php');
		$result = (new IPS2GPIO_PCF8591($InstanceID))->Measurement();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GAD1_SetOutput($InstanceID, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{A2E052CE-055C-4249-A536-7082B233B583}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_PCF8591\module.php');
		$result = (new IPS2GPIO_PCF8591($InstanceID))->SetOutput($Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GPTLB10VE_Send($InstanceID, $Message)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{6DF0D014-EE3E-4FB7-AD45-A8579ABDD2FC}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_PTLB10VE\module.php');
		$result = (new IPS2GPIO_PTLB10VE($InstanceID))->Send($Message);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GRGB_Set_StatusEx($InstanceID, $value, $FadeTime)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{98EDCDCC-79D2-4182-9F0B-6E79D475B358}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_RGB\module.php');
		$result = (new IPS2GPIO_RGB($InstanceID))->Set_StatusEx($value, $FadeTime);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GRGB_Toggle_Status($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{98EDCDCC-79D2-4182-9F0B-6E79D475B358}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_RGB\module.php');
		$result = (new IPS2GPIO_RGB($InstanceID))->Toggle_Status();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GRGBW_Set_Status($InstanceID, $value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{E51C7E8D-40B3-45F6-BB91-7E505A614207}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_RGBW\module.php');
		$result = (new IPS2GPIO_RGBW($InstanceID))->Set_Status($value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function WD_Weatheralerts($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{EDF6BF77-0E16-4FCD-90E4-9E5C0F91B81F}") {
		require_once('C:\IP-Symcon\modules\Wunderground\Wunderground\module.php');
		$result = (new WundergroundWetter($InstanceID))->Weatheralerts();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GRGBW_Set_StatusEx($InstanceID, $value, $FadeTime)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{E51C7E8D-40B3-45F6-BB91-7E505A614207}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_RGBW\module.php');
		$result = (new IPS2GPIO_RGBW($InstanceID))->Set_StatusEx($value, $FadeTime);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GRGBW_Toggle_Status($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{E51C7E8D-40B3-45F6-BB91-7E505A614207}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_RGBW\module.php');
		$result = (new IPS2GPIO_RGBW($InstanceID))->Toggle_Status();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GRPi_Measurement($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{2586FE50-71F9-4471-8245-955B5839A0F6}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_RPi\module.php');
		$result = (new IPS2GPIO_RPi($InstanceID))->Measurement();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GRPi_Measurement_1($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{2586FE50-71F9-4471-8245-955B5839A0F6}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_RPi\module.php');
		$result = (new IPS2GPIO_RPi($InstanceID))->Measurement_1();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GRPi_SetDisplayPower($InstanceID, $Value)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{2586FE50-71F9-4471-8245-955B5839A0F6}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_RPi\module.php');
		$result = (new IPS2GPIO_RPi($InstanceID))->SetDisplayPower($Value);
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function I2GVt_Calculate($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{5F168E40-D7FF-42A5-A82D-1CE19BFD95C4}") {
		require_once('C:\IP-Symcon\modules\SymconModules\IPS2GPIO_Vaillant\module.php');
		$result = (new IPS2GPIO_Vaillant($InstanceID))->Calculate();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function WD_UpdateWetterDaten($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{EDF6BF77-0E16-4FCD-90E4-9E5C0F91B81F}") {
		require_once('C:\IP-Symcon\modules\Wunderground\Wunderground\module.php');
		$result = (new WundergroundWetter($InstanceID))->UpdateWetterDaten();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

function WD_Weathernextdays($InstanceID)
{
	$old_handler = set_error_handler('IPSTypeHintHandler::CatchError');
	if(IPS_GetInstance($InstanceID)["ModuleInfo"]["ModuleID"] == "{EDF6BF77-0E16-4FCD-90E4-9E5C0F91B81F}") {
		require_once('C:\IP-Symcon\modules\Wunderground\Wunderground\module.php');
		$result = (new WundergroundWetter($InstanceID))->Weathernextdays();
	}
	else {
		throw new Exception("Instance does not implement this function");
	}
	set_error_handler($old_handler);
	return $result;
}

