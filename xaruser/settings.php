<?php

function xtasks_user_settings()
{
    $data = xarModAPIFunc('xtasks','admin','menu');
    
    if (!xarVarFetch('returnurl',     'str::',     $returnurl,     '',     XARVAR_NOT_REQUIRED)) return;

    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('My Settings')));
    xarModSetVar('xtasks', 'emailtaskupdates', false);
    xarModSetVar('xtasks', 'show_owner', false);
    xarModSetVar('xtasks', 'show_project', false);
    xarModSetVar('xtasks', 'show_client', false);
    xarModSetVar('xtasks', 'show_importance', false);
    xarModSetVar('xtasks', 'show_priority', false);
    xarModSetVar('xtasks', 'show_age', false);
    xarModSetVar('xtasks', 'show_pctcomplete', false);
    xarModSetVar('xtasks', 'show_planned_dates', false);
    xarModSetVar('xtasks', 'show_actual_dates', false);
    xarModSetVar('xtasks', 'show_hours', false);
    xarModSetVar('xtasks', 'verbose', false);

    $data['submitlabel'] = xarML('Submit');
    $data['uid'] = xarUserGetVar('uid');
    $data['returnurl'] = $returnurl;
    return $data;
}

?>