<?php

/**
 * If log is on, turn it off. If it is off, turn it on. 
 */
function logconfig_adminapi_switchonoff ()
{
    $filename = xarModAPIFunc('logconfig','admin','filename');
    
    if (file_exists($filename)) {
        //Turn off

        //In a busy site, this might be dificult to delete.
        $time = time();
        while (!unlink($filename) && ( ($time-time()) < 3) ) {}

         if (file_exists($filename)) {
            $msg = xarML('Unable to delete file (#(1))', $filename);
            xarExceptionSet(XAR_SYSTEM_MESSAGE, 'UNABLE_DELETE_FILE', $msg);
            return;
         }
    } else {
        //Turn on!
        if (!xarModAPIFunc('logconfig','admin','saveconfig')) return;
    }
    
    return true;
}

?>