<?php

function xarpages_admin_main()
{
    // Need admin priv to view the info page.
    if (!xarSecurityCheck('AdminPage')) {
        return;
    }

    return array();
}

?>