<?php

/**
 * Is security caching currently on? 
 */
function cachesecurity_adminapi_ison()
{
    $filename = xarModAPIFunc('cachesecurity','admin','filename', array('part'=>'on'));

    if (file_exists($filename)) {
        return true;
    } //else

    return false;
}

?>