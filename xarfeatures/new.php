<?php

function xproject_features_new($args)
{

    extract($args);
    if (!xarVarFetch('projectid', 'id', $projectid, $projectid, XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('inline', 'int', $inline, $inline, XARVAR_NOT_REQUIRED)) return;

    if (!xarModAPILoad('xproject', 'user')) return;

    $data = xarModAPIFunc('xproject', 'admin', 'menu');

    if (!xarSecurityCheck('AddXProject')) {
        return;
    }

    $projectinfo = xarModAPIFunc('xproject',
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
