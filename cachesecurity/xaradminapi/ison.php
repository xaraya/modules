<?php

/**
 * Is security caching currently on? 
 */
function cachesecurity_adminapi_ison()
{
    $filename = xarModAPIFunc('logconfig','admin','filename', array('part'=>'general'));

    if (file_exists($filename)) {
        return true;
    } //else

    return false;
}

?>