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

    $varCacheDir = xarCoreGetVarDirPath() . '/cache';
    
    if (file_exists($varCacheDir . '/output/cache.touch')) {
        $data['CachingEnabled'] = 1;
    } else {
        $data['CachingEnabled'] = 0;
    }

    if (file_exists($varCacheDir . '/output/cache.pagelevel')) {
        $data['pageCachingEnabled'] = 1;
    } else {
        $data['pageCachingEnabled'] = 0;
    }

    $cachingConfigFile = $varCacheDir . '/config.caching.php';

    if (!file_exists($cachingConfigFile)) {
        $msg=xarML('That is strange.  The #(1) file seems to be 
                    missing.', $cachingConfigFile);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,'MODULE_FILE_NOT_EXIST',
                        new SystemException($msg));
            
        return false;
    }

    include $cachingConfigFile;

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
        $data['settings']['OutputSizeLimit'] = 262144;
    }

    $data['settings']['OutputSizeLimit'] /= 1048576;

    // reformat seconds as hh:mm:ss
    $data['settings']['PageTimeExpiration'] = xarModAPIFunc( 'xarcachemanager', 'admin', 'convertseconds',
                                                             array('starttime' => $data['settings']['PageTimeExpiration'],
                                                                   'direction' => 'from'));
    $data['settings']['BlockTimeExpiration'] = xarModAPIFunc( 'xarcachemanager', 'admin', 'convertseconds',
                                                             array('starttime' => $data['settings']['BlockTimeExpiration'],
                                                                   'direction' => 'from'));

    $filter['Class'] = 2;
    $data['themes'] = xarModAPIFunc('themes',
        'admin',
        'getlist', $filter);

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>
