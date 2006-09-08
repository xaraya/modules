<?php

function xproject_admin_new($args)
{
    extract($args);
    
    if (!xarVarFetch('clientid', 'id', $clientid, 0, XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('xproject','admin','menu');

    $data['projects_objectid'] = xarModGetVar('xproject', 'projects_objectid');

    if (!xarSecurityCheck('AddXProject')) {
        return;
    }
    
    $data['clientid'] = $clientid;

    $data['addbutton'] = xarVarPrepForDisplay(xarML('Create Project'));

    $item = array();
    $item['module'] = 'xproject';
    $hooks = xarModCallHooks('item','new','',$item);
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
