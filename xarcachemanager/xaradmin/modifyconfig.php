<?php

/**
 * Update the configuration parameters of the module based on data from the modification form
 * 
 * @author jsb | mikespub
 * @access public 
 * @param no $ parameters
 * @return true on success or void on failure
 * @throws MODULE_FILE_NOT_EXIST
 * @todo nothing
 */
function xarcachemanager_admin_modifyconfig()
{ 
    // Security Check
    if (!xarSecurityCheck('AdminXarCache')) return;

    $data = array();
    
    $systemVarDir = xarCoreGetVarDirPath();
    
    if (file_exists($systemVarDir . '/cache/output/cache.touch')) {
        $data['CachingEnabled'] = 1;
    } else {
        $data['CachingEnabled'] = 0;
    }

    $configFile = $systemVarDir . '/cache/config.caching.php';
    if (!file_exists($configFile)) {
        // if the file doesn't exist, we could recreate it
        /*$defaultconfigfile = 'modules/xarcachemanager/config.caching.php.dist';
        $handle = fopen($defaultconfigfile, "r");
        $defaultconfig = fread ($handle, filesize ($defaultconfigfile));
        $fp = @fopen($configFile,"w");
        fwrite($fp,$defaultconfig);
        fclose($fp);
        echo 'WARNING: var/cache/config.caching.php file was missing. New config file was created';*/
        // but I figure it is better to let the admin know something that should not have
        // been messed up, is.
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'MODULE_FILE_NOT_EXIST', $configFile);
        return;
    }
    include $configFile;

    $keyslist = str_replace( '.', '', array_keys($cachingConfiguration));
    $valueslist = array_values($cachingConfiguration);
    $data['settings'] = array();
    
    $arraysize = sizeof($keyslist);
    for ($i=0;$i<$arraysize;$i++) {
        $data['settings'][$keyslist[$i]] = $valueslist[$i];
    }

    if(!isset($data['settings']['PageDisplayView'])) {
        $data['settings']['PageDisplayView'] = 0;
    }
    if(!isset($data['settings']['PageViewTime'])) {
        $data['settings']['PageViewTime'] = 0;
    }
    if(!isset($data['settings']['OutputSizeLimit'])) {
        $data['settings']['OutputSizeLimit'] = 0.2;
    }

    $filter['Class'] = 2;
    $data['themes'] = xarModAPIFunc('themes',
        'admin',
        'getlist', $filter);

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>
