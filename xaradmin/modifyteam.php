<?php

function xproject_admin_modifyteam($args)
{
    extract($args);

    if (!xarVarFetch('projectid',     'id',     $projectid,     $projectid,     XARVAR_NOT_REQUIRED)) return;

    if(!xarModLoad('addressbook', 'user')) return;

    $projectinfo = xarModAPIFunc('xproject',
                             'user',
                             'get',
                             array('projectid' => $projectid));
    
    if (!isset($projectinfo) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('EditXProject', 1, 'Item', "$projectinfo[project_name]:All:$projectid")) {
        return;
    }

    $teamlist = xarModAPIFunc('xproject',
                            'team',
                            'getall',
                            array('projectid' => $projectid));
    $valuelist = array();
    foreach($teamlist as $teaminfo) {
        $valuelist[] = $teaminfo['memberid'];
    }
    $valueexplodelist = implode(";", $valuelist);

    $data = array();

    $data['projectid'] = $projectinfo['projectid'];

    $data['teamlist'] = $teamlist;

    $data['valueexplodelist'] = $valueexplodelist;
    
    $data['authid'] = xarSecGenAuthKey();

    $data['item'] = $projectinfo;
    
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update'));

    $data['returnurl'] = xarServerGetVar('HTTP_REFERER');

    return $data;
}

?>