<?php

/**
 * Is log currently on? 
 */
function logconfig_adminapi_islogon()
{
    $filename = xarModAPIFunc('logconfig','admin','filename');

    if (file_exists($filename)) {
        return true;
    } //else

    return false;
}

?>