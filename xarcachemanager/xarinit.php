<?php
/**
 * File: $Id$
 *
 * xarCacheManager initialization functions
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @author jsb | mikespub
 */

/**
 * initialise the xarcachemanager module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function xarcachemanager_init()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

    // set up the output cache directory
    $varCacheDir = xarCoreGetVarDirPath() . '/cache';

    if (is_writable($varCacheDir) || is_dir($varCacheDir.'/output')) {
        if (!is_dir($varCacheDir.'/output')) {
            // set up the output directory
            $old_umask = umask(0);
            mkdir($varCacheDir.'/output', 0777);
            umask($old_umask);
        }
        if (!is_writable($varCacheDir.'/output')) {
            // tell them output dir needs to be writable
            $msg=xarML('The var/cache/output directory must be writable 
                       by the web server for output caching to work.  
                       The xarCacheManager module has not been installed, 
                       please make the var/cache/output directory 
                       writable by the web server before re-trying to 
                       install this module.');
            xarExceptionSet(XAR_SYSTEM_EXCEPTION,'FUNCTION_FAILED',
                            new SystemException($msg));
            return false;
        }
    } else {
        // tell them that cache needs to be writable or manually create output dir
        $msg=xarML('The var/cache directory must be writable 
                   by the web server for the install script to 
                   set up output caching for you.
                   The xarCacheManager module has not been installed, 
                   please make the var/cache directory 
                   writable by the web server before re-trying to 
                   install this module.  
                   Alternatively, you can manually create the 
                   var/cache/output directory and copy the 
                   xarcachemanager/config.caching.php.dist 
                   file to var/cache/config.caching.php - the output 
                   directory and the config.caching.php file must be 
                   writable by the web server for output caching to work.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,'FUNCTION_FAILED',
                        new SystemException($msg));
        return false;
    }
    
    // avoid directory browsing
    if (!file_exists($varCacheDir.'/output/index.html')) {
        @touch($varCacheDir.'/output/index.html');
    }

    // set up the config file.
    $defaultConfigFile = 'modules/xarcachemanager/config.caching.php.dist';
    $cachingConfigFile = $varCacheDir .'/config.caching.php';
    if (!file_exists($defaultConfigFile)) {
        $msg=xarML('That is strange.  The default, distributed configuration 
                   file, normally #(1), seems to be 
                   missing.', $defaultConfigFile);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,'MODULE_FILE_NOT_EXIST',
                        new SystemException($msg));
        
        return false;
    }
    if (is_writable($varCacheDir) || is_writable($cachingConfigFile)) {
        $handle = fopen($defaultConfigFile, "rb");
        $defaultConfig = fread ($handle, filesize ($defaultConfigFile));
        $fp = @fopen($cachingConfigFile,"wb");
        fwrite($fp, $defaultConfig);
        fclose($fp);
    } else {
        // tell them that cache needs to be writable or manually create config file
        $msg=xarML('The var/cache directory must be writable 
                   by the web server for the install script to 
                   set up output caching for you.
                   The xarCacheManager module has not been installed, 
                   please make the var/cache directory 
                   writable by the web server before re-trying to 
                   install this module.  
                   Alternatively, you can manually copy the 
                   xarcachemanager/config.caching.php.dist 
                   file to var/cache/config.caching.php - the 
                   config.caching.php file must be writable by the
                   web server for output caching to be managed with
                   the xarcachemanager module.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,'FUNCTION_FAILED',
                        new SystemException($msg));
        return false;
    }
    
    // Set up module variables
    xarModSetVar('xarcachemanager','FlushOnNewComment', 0);
    xarModSetVar('xarcachemanager','FlushOnNewRating', 0);
    xarModSetVar('xarcachemanager','FlushOnNewPollvote', 0);

    if (!xarModRegisterHook('item', 'create', 'API',
                            'xarcachemanager', 'admin', 'createhook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'update', 'API',
                            'xarcachemanager', 'admin', 'updatehook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'delete', 'API',
                            'xarcachemanager', 'admin', 'deletehook')) {
        return false;
    }
    if (!xarModRegisterHook('module', 'updateconfig', 'API',
                            'xarcachemanager', 'admin', 'updateconfighook')) {
        return false;
    }

    // Enable xarcachemanager hooks for articles
    if (xarModIsAvailable('articles')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'articles', 'hookModName' => 'xarcachemanager'));
    }
    // Enable xarcachemanager hooks for base
    if (xarModIsAvailable('base')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'base', 'hookModName' => 'xarcachemanager'));
    }
    // Enable xarcachemanager hooks for blocks
    if (xarModIsAvailable('blocks')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'blocks', 'hookModName' => 'xarcachemanager'));
    }
    // Enable xarcachemanager hooks for categories
    if (xarModIsAvailable('categories')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'categories', 'hookModName' => 'xarcachemanager'));
    }
    // Enable xarcachemanager hooks for roles
    if (xarModIsAvailable('roles')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'roles', 'hookModName' => 'xarcachemanager'));
    }
    // Enable xarcachemanager hooks for privileges
    if (xarModIsAvailable('privileges')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'privileges', 'hookModName' => 'xarcachemanager'));
    }

    // set up permissions masks.
    xarRegisterMask('ReadXarCache', 'All', 'xarcachemanager', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('AdminXarCache', 'All', 'xarcachemanager', 'Item', 'All:All:All', 'ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

/**
 * upgrade the xarcachemanager module from an old version
 * This function can be called multiple times
 */
function xarcachemanager_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case 0.1:
            // Code to upgrade from the 0.1 version (base page level caching)
            // todo: do conversion of MB to bytes in config file
            $cachingConfigFile = xarCoreGetVarDirPath() . '/cache/config.caching.php';
            include($cachingConfigFile);
            $cachesizelimit = $cachingConfiguration['Output.SizeLimit'] * 1048576;
            $cachingConfig = join('', file($cachingConfigFile));
            $cachingConfig = preg_replace('/\[\'Output.SizeLimit\'\]\s*=\s*(|\")(.*)\\1;/', "['Output.SizeLimit'] = $cachesizelimit;", $cachingConfig);
            $fp = fopen ($cachingConfigFile, 'wb');
            fwrite ($fp, $cachingConfig);
            fclose ($fp);
            break;
        case 0.2:
        case '0.2.0':
            // Code to upgrade from the 0.2 version (cleaned-up page level caching)
            break;
        case 0.3:
            // Code to upgrade from the 0.3 version (base block level caching)
            break;
        case 0.4:
            // Code to upgrade from the 0.4 version (base module level caching)
            break;
        case 1.0:
            // Code to upgrade from version 1.0 goes here
            break;
        case 2.0:
            // Code to upgrade from version 2.0 goes here
            break;
    }
    // Update successful
    return true;
}

/**
 * delete the xarcachemanager module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function xarcachemanager_delete()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();
    
    //if still there, remove the cache.touch file, this turns everything off
    $varCacheDir = xarCoreGetVarDirPath() . '/cache';
    if (file_exists($varCacheDir . '/output') && is_dir($varCacheDir . '/output')) {
        if (file_exists($varCacheDir . '/output/cache.touch')) {
            @unlink($varCacheDir . '/output/cache.touch');
        }

        // clear out the cache
        if ($handle = @opendir($varCacheDir . '/output')) {
            while (($file = readdir($handle)) !== false) {
                $cache_file = $varCacheDir . '/output/' . $file;
                if (is_file($cache_file)) {
                    @unlink($cache_file);
                }
            }
            closedir($handle);
        }

        // remove the output cache directory
        @rmdir($varCacheDir . '/output');
    }

    // remove the caching config file
    if (file_exists($varCacheDir . '/config.caching.php')) {
        @unlink($varCacheDir . '/config.caching.php');
    }

    // Remove module variables
    xarModDelVar('xarcachemanager','FlushOnNewComment');
    xarModDelVar('xarcachemanager','FlushOnNewRating');
    xarModDelVar('xarcachemanager','FlushOnNewPollvote');

    // Remove module hooks
    if (!xarModUnregisterHook('item', 'create', 'API',
                              'xarcachemanager', 'admin', 'createhook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'update', 'API',
                              'xarcachemanager', 'admin', 'updatehook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'delete', 'API',
                              'xarcachemanager', 'admin', 'deletehook')) {
        return false;
    }
    if (!xarModUnregisterHook('module', 'updateconfig', 'API',
                            'xarcachemanager', 'admin', 'updateconfighook')) {
        return false;
    }

    // Remove Masks and Instances
    xarRemoveMasks('xarcachemanager');

    // Deletion successful
    return true;
} 

?>
