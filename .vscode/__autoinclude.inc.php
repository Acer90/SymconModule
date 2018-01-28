<?

//Currently we do not support injection of arrays into PHP
//Unwrap colon seperated values for backwards compatibility
if(isset($_IPS['SENDER']) && ($_IPS['SENDER'] === "HeatingControl")) {
	$_IPS['INSTANCES'] = explode(",", $_IPS['INSTANCES']);
	foreach($_IPS['INSTANCES'] as $key => $value)
		$_IPS['INSTANCES'][$key] = intval($value);
	$_IPS['INVERTS'] = explode(",", $_IPS['INVERTS']);
	foreach($_IPS['INVERTS'] as $key => $value)
		$_IPS['INVERTS'][$key] = ($value == "true");
}

//Working directory for events with scriptText is not set internally
//We need to work around the isse that PHP does not expose any internal API for changing this
if($_IPS['SELF'] == 0 && !isset($_SERVER['PHP_SELF']))
	chdir(IPS_GetKernelDirEx()."scripts");

if(file_exists(IPS_GetKernelDirEx() . "/scripts/__rpc.inc.php")) 
	require_once(IPS_GetKernelDirEx() . "/scripts/__rpc.inc.php"); 

if(IPS_GetOption("CompatibilityRequired") != 0)
	if(file_exists(IPS_GetKernelDirEx() . "/scripts/__compatibility.inc.php")) 
		require_once(IPS_GetKernelDirEx() . "/scripts/__compatibility.inc.php"); 

if(file_exists(IPS_GetKernelDir() . "/scripts/__generated.inc.php"))
	require_once(IPS_GetKernelDir() . "/scripts/__generated.inc.php");

if(file_exists(IPS_GetKernelDir() . "/scripts/__autoload.php"))
	require_once(IPS_GetKernelDir() . "/scripts/__autoload.php"); 

?>
