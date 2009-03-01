<?php

function xtasks_user_overview()
{
    if (!xarSecurityCheck('ViewXProject')){
        return;
    }

    $data = xarModAPIFunc('xtasks','admin','menu');
    $data['welcome'] = xarML('Welcome to the xTasks module. ');
    return $data;
}
?>