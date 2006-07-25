<?php

function accessmethods_admin_modify($args)
{
    xarModLoad('addressbook', 'user');

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

    if (!xarSecurityCheck('EditAccessMethods', 1, 'Item', "$item[site_name]:All:$siteid")) {
        return;
    }
    
    $data = xarModAPIFunc('accessmethods','admin','menu');

    $data['accessmethods_objectid'] = xarModGetVar('accessmethods', 'accessmethods_objectid');
    
	$data['siteid'] = $item['siteid'];
	
    $data['authid'] = xarSecGenAuthKey();

	$item['module'] = 'accessmethods';

	$data['item'] = $item;

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
