<?php

/**
 * Synchronize the configuration cache file in PHP with the DB configuration 
 */
function logconfig_adminapi_sync()
{
    if (!xarModAPIFunc('logconfig','admin','islogon')) {
        //do nothing
        return true;
    }
    //else
    
    if (!xarModAPIFunc('logconfig','admin','saveconfig')) return;

    return true;
}

?>
