<?php

function xproject_pages_new()
{
    if (!xarVarFetch('projectid',     'id',     $projectid,     $projectid,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('parentid',     'id',     $parentid,     $parentid,     XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('inline', 'int', $inline, $inline, XARVAR_NOT_REQUIRED)) return;

    if (!xarModAPILoad('xproject', 'user')) return;

    $data = xarModAPIFunc('xproject','admin','menu');

    if (!xarSecurityCheck('AddXProject')) {
        return;
    }

    $projectinfo = xarModAPIFunc('xproject',
                          'user',
                          'get',
                          array('projectid' => $projectid));

    $pagelist = xarModAPIFunc('xproject',
                         'pages',
                         'getall',
                         array('projectid' => $projectid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $data['authid'] = xarSecGenAuthKey();
    $data['projectid'] = $projectid;
    $data['parentid'] = $parentid;
    $data['inline'] = $inline;
    $data['projectinfo'] = $projectinfo;
    $data['pagelist'] = $pagelist;

    $data['addbutton'] = xarVarPrepForDisplay(xarML('Create Feature'));

    return $data;
}

?>