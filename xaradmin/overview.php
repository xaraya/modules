<?php

function helpdesk_admin_overview($args)
{
    extract($args);

    if (!xarSecurityCheck('adminhelpdesk')) { return; }

    $data = array();

    return $data;
}
?>