<?php
function opentracker_user_exit() {
	require_once 'modules/opentracker/xarOpenTracker.php';
	
	if (!xarVarFetch('url', 'str:1:', $exitURL)) return; 
	if (!xarVarFetch('proto', 'str', $protocol, 'aHR0cDovLw==', XARVAR_NOT_REQUIRED)) return; # the hardcoded string is base64 for 'http://'
	
  $protocol = base64_decode($protocol);	
	$exitURL = str_replace('&amp;', '&', base64_decode($exitURL));
	
	$config    = &phpOpenTracker_Config::singleton();
	$db        = &phpOpenTracker_DB::singleton();
	
	$container = &phpOpenTracker_Container::singleton(
		array(
			'initNoSetup' => true
		)
	);
	
	$db->query(
		sprintf(
			'UPDATE %s
			  SET exit_target_id = %d
			WHERE accesslog_id   = %d
			  AND document_id    = %d
			  AND timestamp      = %d',
			
			$config['accesslog_table'],
			$db->storeIntoDataTable($config['exit_targets_table'], $exitURL),
			$container['accesslog_id'],
			$container['document_id'],
			$container['timestamp']
		)
	);
	xarResponseRedirect($protocol . $exitURL);
	return '';
}
?>