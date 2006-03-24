<?php
/*
 * Restore caching config
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
/**
 * Restore the caching configuration file
 * 
 * @author jsb <jsb@xaraya.com>
 * @access public 
 * @throws FUNCTION_FAILED
 * @returns boolean
 */
function xarcachemanager_adminapi_restore_cachingconfig()
{
    $varCacheDir = xarCoreGetVarDirPath() . '/cache';
    $defaultConfigFile = 'modules/xarcachemanager/config.caching.php.dist';
    $cachingConfigFile = $varCacheDir . '/config.caching.php';

    $configSettings = xarModAPIFunc('xarcachemanager',
                                    'admin',
                                    'get_cachingconfig',
                                    array('from' => 'db',
                                          'cachingConfigFile' => $cachingConfigFile));                                         
    
    // Confirm the cache directory is writable
    if (!is_writable($varCacheDir)) {
        $msg=xarML('The #(1) directory is not writable by the web 
                   web server. The #(1) directory must be writable by the web 
                   server process owner for output caching to work. 
                   Please change the permission on the #(1) directory
                   so that the web server can write to it.', $varCacheDir);
        xarErrorSet(XAR_SYSTEM_EXCEPTION,'FUNCTION_FAILED',
                        new SystemException($msg));
        return false;
    }
    
    // Confirm the config file is writable
    if (file_exists($cachingConfigFile) && !is_writable($cachingConfigFile)) {
        $msg=xarML('The #(1) file is not writable by the web 
                   web server. The #(1) file must be writable by the web 
                   server process owner for output caching to be configured 
                   via the xarCacheManager module. 
                   Please change the permission on the #(1) file
                   so that the web server can write to it.', $cachingConfigFile);
        xarErrorSet(XAR_SYSTEM_EXCEPTION,'FUNCTION_FAILED',
                        new SystemException($msg));
        return false;
    }
    
    if (file_exists($cachingConfigFile)) {
        @unlink($cachingConfigFile);
    }
    copy($defaultConfigFile, $cachingConfigFile); 
    xarModAPIFunc('xarcachemanager', 'admin', 'save_cachingconfig', 
        array('configSettings' => $configSettings));                

    return true;
}

?>
