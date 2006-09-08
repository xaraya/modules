<?php

function xproject_admin_newcombo()
{
    $data = xarModAPIFunc('xproject','admin','menu');

    if (!xarSecurityCheck('AddXProject')) {
        return;
    }

    return $data;
}

?>
