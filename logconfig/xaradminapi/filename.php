<?php

/**
 * Returns the filename and path of the config file 
 */
function logconfig_adminapi_filename ()
{
    $logConfigFile = xarCoreGetVarDirPath() . '/cache/config.log.php';
    return $logConfigFile;
}

?>