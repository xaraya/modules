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
    $data['users'] = xarModAPIFunc('roles','user','countitems')  // all roles
                   - xarModAPIFunc('roles','user','countgroups') // all groups
                   - 1;                                          // anonymous user  
    $data['sysversion'] = xarConfigGetVar('System.Core.VersionNum');

	//TODO:
	// articles
	// comments
	// waiting content
	// categories

	return $data;
}

?>