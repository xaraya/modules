<?php

function dyn_example_user_settings()
{
    $data = xarModAPIFunc('dyn_example','user','menu');

    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('My Settings')));

    $data['submitlabel'] = xarML('Submit');
    $data['uid'] = xarUserGetVar('uid');
    return $data;
}

?>