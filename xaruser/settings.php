<?php

function dossier_user_settings()
{
    $data = xarModAPIFunc('dossier','user','menu');

    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('My Settings')));

    $data['submitlabel'] = xarML('Submit');
    $data['uid'] = xarUserGetVar('uid');
    return $data;
}

?>
