<?php

/**
 * the main administration function
 * @return array
 */
function filemanager_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('EditFileManager')) return;

    return array();
}

?>