<?php

/**
 * Unsets the touch file which means the given system part is not synchronized 
 */
function cachesecurity_adminapi_unsetsynchronized($args)
{
    $filename = xarModAPIFunc('cachesecurity','admin','filename', array('part'=>$args['part']));
    if (file_exists($filename) && !unlink($filename)) return false;

    return true;
}

?>