<?php

/**
 * Get misc stats from different core modules
 *
 * @param   none
 * @return  array - misc data
 */
function stats_userapi_getmisc()
{
	// core
	$countArgs = array('include_anonymous' => false,
					   'include_myself'    => false);
	$data['users'] = xarModAPIFunc('roles','user','countall',$countArgs);
    $data['sysversion'] = xarConfigGetVar('System.Core.VersionNum');

	//TODO:
	// articles
	// comments
	// waiting content
	// categories
	unset($countArgs);
	
	return $data;
}

?>