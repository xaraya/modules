<?php

/**
 * Sets the touch file that means the given system part is synchronized 
 */
function cachesecurity_adminapi_setsynchronized($args)
{
    $filename = xarModAPIFunc('cachesecurity','admin','filename', array('part'=>$args['part']));

    if (!touch($filename)) return false;
     if (!file_exists($filename)) return false;

    return true;
}

?>