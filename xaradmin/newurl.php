<?php
function window_admin_newurl($args)
{
    extract($args);
    if (!xarModAPIFunc('window',
            'admin',
            'addurl')) return;

    return true;
}
?>