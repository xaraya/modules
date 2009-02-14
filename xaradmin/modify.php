<?php

function accessmethods_admin_modify($args)
{
	extract($args);
    
    if (!xarVarFetch('siteid',     'id',     $siteid,     $siteid,     XARVAR_NOT_REQUIRED)) return;
	
    if (!empty($objectid)) {
        $projectid = $objectid;
    }
	$item = xarModAPIFunc('accessmethods',
                         'user',
                         'get',
                         array('siteid' => $siteid));
	
	if (!isset($project) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('EditAccessMethods', 1, 'All', "$item[webmasterid]")) {
        return;
    }
    
    $data = xarModAPIFunc('accessmethods','admin','menu');
    
	$data['siteid'] = $item['siteid'];
	
    $data['authid'] = xarSecGenAuthKey();

	$item['module'] = 'accessmethods';

	$data['item'] = $item;

    $logs = xarModAPIFunc('accessmethods',
                          'log',
                          'getall',
                          array('siteid' => $siteid));
                          
    if($logs === false) return;
    
    $data['logs'] = $logs;

    $hooks = xarModCallHooks('item','modify',$siteid,$item);

    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    
    return $data;
}

?>
