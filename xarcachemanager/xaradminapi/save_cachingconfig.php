<?php

/**
 * Save configuration settings in the config file and modVars
 * 
 * @author jsb <jsb@xaraya.com>
 * @access public 
 * @param $args['config'] array of config labels and values
 * @throws FUNCTION_FAILED
 */
function xarcachemanager_adminapi_save_cachingconfig($args)
{
    extract($args);

    if (!xarSecurityCheck('AdminXarCache')) { return; }
    if (empty($configSettings) || !is_array($configSettings)) { return FALSE; }
    
    if (!isset($cachingConfigFile)) {
         $cachingConfigFile = xarCoreGetVarDirPath() . '/cache/config.caching.php';
    }

    if (!is_writable($cachingConfigFile)) {
        $msg=xarML('The caching configuration file is not writable by the web server.  
                   #(1) must be writable by the web server for 
                   the output caching to be managed by xarCacheManager.', $cachingConfigFile);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,'FUNCTION_FAILED',
                        new SystemException($msg));
        return false;
    }

    $cachingConfig = join('', file($cachingConfigFile));
    
    foreach ($configSettings as $configKey => $configValue) {
        if (is_numeric($configValue)) {
            $cachingConfig = preg_replace('/\[\'' . $configKey . '\'\]\s*=\s*(|\")(.*)\\1;/', "['$configKey'] = $configValue;", $cachingConfig);
        } elseif (is_array($configValue)) {
            $configValue = "'" . join("','",$configValue) . "'";
            $cachingConfig = preg_replace('/\[\'' . $configKey . '\'\]\s*=\s*array\s*\((.*)\)\s*;/i', "['$configKey'] = array($configValue);", $cachingConfig);
        } else {
            $cachingConfig = preg_replace('/\[\'' . $configKey . '\'\]\s*=\s*(\'|\")(.*)\\1;/', "['$configKey'] = '$configValue';", $cachingConfig);
        }
        // place the config setting in a modvar for save keeping
        xarModSetVar('xarcachemanager', $configKey, $configValue);
    }
    
    $fp = fopen ($cachingConfigFile, 'wb');
    fwrite ($fp, $cachingConfig);
    fclose ($fp);

}

?>
