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
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

    // set up the output cache directory
    $systemVarDir = xarCoreGetVarDirPath();
    $varCacheDir = $systemVarDir . '/cache';
    if (is_writable($varCacheDir)) {
         if (!is_dir($varCacheDir.'/output')) {
            $old_umask = umask(0);
            mkdir($varCacheDir.'/output', 0777);
            umask($old_umask);
         } elseif (!is_writable($varCacheDir.'/output')) {
            return false; // todo: give a meaningful error
         }
         // avoid directory browsing
         if (!file_exists($varCacheDir.'/output/index.html')) {
             @touch($varCacheDir.'/output/index.html');
         }
    } else {
        return false;  // todo: give a meaningful error
    }

    // set up the config file.
    // todo: error checking
    $defaultconfigfile = 'modules/xarcachemanager/config.caching.php.dist';
    if (file_exists($defaultconfigfile)) {
        $handle = fopen($defaultconfigfile, "rb");
        $defaultconfig = fread ($handle, filesize ($defaultconfigfile));
        $fp = @fopen($varCacheDir .'/config.caching.php',"wb");
        fwrite($fp,$defaultconfig);
        fclose($fp);
    } else {
        return false;  // todo: give a meaningful error
    }

/*
    if (!xarModRegisterHook('item', 'new', 'GUI',
                           'xarcachemanager', 'admin', 'newhook')) {
        return false;
    }
*/
    if (!xarModRegisterHook('item', 'create', 'API',
                           'xarcachemanager', 'admin', 'createhook')) {
        return false;
    }
/*
    if (!xarModRegisterHook('item', 'modify', 'GUI',
                           'xarcachemanager', 'admin', 'modifyhook')) {
        return false;
    }
*/
    if (!xarModRegisterHook('item', 'update', 'API',
                           'xarcachemanager', 'admin', 'updatehook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'delete', 'API',
                           'xarcachemanager', 'admin', 'deletehook')) {
        return false;
    }
/*
    if (!xarModRegisterHook('module', 'remove', 'API',
                           'xarcachemanager', 'admin', 'removehook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'display', 'GUI',
                           'xarcachemanager', 'user', 'displayhook')) {
        return false;
    }
*/

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
            // Should we need to change anything
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
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();
    
    //if still there, remove the cache.touch file, this turns everything off
    $systemVarDir = xarCoreGetVarDirPath();
    $varCacheDir = $systemVarDir . '/cache';
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

    // Remove module hooks
/*
    if (!xarModUnregisterHook('item', 'new', 'GUI',
                           'xarcachemanager', 'admin', 'newhook')) {
        return false;
    }
*/
    if (!xarModUnregisterHook('item', 'create', 'API',
                           'xarcachemanager', 'admin', 'createhook')) {
        return false;
    }
/*
    if (!xarModUnregisterHook('item', 'modify', 'GUI',
                           'xarcachemanager', 'admin', 'modifyhook')) {
        return false;
    }
*/
    if (!xarModUnregisterHook('item', 'update', 'API',
                           'xarcachemanager', 'admin', 'updatehook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'delete', 'API',
                           'xarcachemanager', 'admin', 'deletehook')) {
        return false;
    }
/*
    // when a whole module is removed, e.g. via the modules admin screen
    // (set object ID to the module name !)
    if (!xarModUnregisterHook('module', 'remove', 'API',
                           'xarcachemanager', 'admin', 'removehook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'display', 'GUI',
                           'xarcachemanager', 'user', 'displayhook')) {
        return false;
    }
*/

    // Remove Masks and Instances
    xarRemoveMasks('xarcachemanager');

    // Deletion successful
    return true;
} 

?>
