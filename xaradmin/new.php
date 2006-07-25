<?php

function xproject_admin_new()
{
    $data = xarModAPIFunc('xproject','admin','menu');

    $data['projects_objectid'] = xarModGetVar('xproject', 'projects_objectid');

    if (!xarSecurityCheck('AddXProject')) {
        return;
    }

    $data['authid'] = xarSecGenAuthKey();
    $data['clientid'] = xarSessionGetVar('uid');

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
