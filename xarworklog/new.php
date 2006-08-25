<?php

function xtasks_worklog_new()
{
    if (!xarVarFetch('projectid',     'id',     $projectid,     $projectid,     XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('inline', 'int', $inline, $inline, XARVAR_NOT_REQUIRED)) return;

    if (!xarModAPILoad('xtasks', 'user')) return;
    
    $data = xarModAPIFunc('xtasks','admin','menu');

    if (!xarSecurityCheck('AddXProject')) {
        return;
    }

    $projectinfo = xarModAPIFunc('xtasks',
                          'user',
                          'get',
                          array('projectid' => $projectid));

    $data['authid'] = xarSecGenAuthKey();
    $data['projectid'] = $projectid;
    $data['inline'] = $inline;
    $data['projectinfo'] = $projectinfo;

    $data['addbutton'] = xarVarPrepForDisplay(xarML('Create Feature'));

    return $data;
}

?>