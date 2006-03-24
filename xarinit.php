<?php
/**
 * xarCacheManager initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 * @author jsb | mikespub
 */

/**
 * initialise the xarcachemanager module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function xarcachemanager_init()
{
    // set up the config.caching file and output cache directory structure
    $varCacheDir = xarCoreGetVarDirPath() . '/cache';
    if (!xarcachemanager_fs_setup(array('varCacheDir' => $varCacheDir))) {
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
    if (!xarModRegisterHook('item', 'modify', 'GUI',
                            'xarcachemanager', 'admin', 'modifyhook')) {
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

    if (xarCore_getSystemVar('DB.UseADODBCache')){
        // Enable query caching for categories getcat
        if (xarModIsAvailable('categories')) {
            xarModSetVar('categories','cache.userapi.getcat',60);
        }
        // Enable query caching for comments get_author_count
        if (xarModIsAvailable('comments')) {
            xarModSetVar('comments','cache.userapi.get_author_count',60);
        }
    }
    
    // add the database storage table
    xarcachemanager_create_cache_data();

    // Initialisation successful
    return true;
}

/**
 * upgrade the xarcachemanager module from an old version
 * This function can be called multiple times
 */
function xarcachemanager_upgrade($oldversion)
{
    $varCacheDir = xarCoreGetVarDirPath() . '/cache';
    $defaultConfigFile = 'modules/xarcachemanager/config.caching.php.dist';
    $cachingConfigFile = $varCacheDir . '/config.caching.php';
    
    // check to see if we've got the necessary permissions to upgrade
    if ((!file_exists($cachingConfigFile) && !is_writable($varCacheDir)) || 
        (file_exists($cachingConfigFile) && !is_writable($cachingConfigFile))) {
        $msg=xarML('The xarCacheManager module upgrade has failed.  
                   Please make #(1) writable by the web server process 
                   owner to complete the upgrade.  If #(1) does not exist, 
                   please make #(2) writable by the web server process and 
                   #(1) will be created for you.', $cachingConfigFile, $varCacheDir);
        xarErrorSet(XAR_SYSTEM_EXCEPTION,'FUNCTION_FAILED',
                        new SystemException($msg));
        return false;
    }
    
    // parse the current distribution config file so we have default values
    include_once($defaultConfigFile);

    // Upgrade dependent on old version number
    switch ($oldversion) {
        case 0.1:
            // Code to upgrade from the 0.1 version (base page level caching)
            // Do conversion of MB to bytes in config file
            include($cachingConfigFile);
            $cachingConfiguration['Output.SizeLimit'] = $cachingConfiguration['Output.SizeLimit'] * 1048576;
            xarModAPIFunc('xarcachemanager', 'admin', 'save_cachingconfig', 
                array('configSettings' => $cachingConfiguration,
                      'cachingConfigFile' => $cachingConfigFile));
        case 0.2:
        case '0.2.0':
            // Code to upgrade from the 0.2 version (cleaned-up page level caching)
            // Bring the config file up to current version
            if (file_exists($cachingConfigFile)) {
                $configSettings = xarModAPIFunc('xarcachemanager',
                                                'admin',
                                                'get_cachingconfig',
                                                array('from' => 'file',
                                                      'cachingConfigFile' => $cachingConfigFile));
                if(isset($configSettings['Page.DefaultTheme'])) {
                    $configSettings['Output.DefaultTheme'] = $configSettings['Page.DefaultTheme'];
                }
                @unlink($cachingConfigFile);
                copy($defaultConfigFile, $cachingConfigFile); 
                xarModAPIFunc('xarcachemanager', 'admin', 'save_cachingconfig', 
                    array('configSettings' => $configSettings,
                          'cachingConfigFile' => $cachingConfigFile));
            } else {
                copy($defaultConfigFile, $cachingConfigFile);
            }
            // Register new Admin Modify GUI Hook
            if (!xarModRegisterHook('item', 'modify', 'GUI',
                                    'xarcachemanager', 'admin', 'modifyhook')) {
                return false;
            }
        case '0.3.0':
            // Code to upgrade from the 0.3.0
            // Bring the config file up to current version
            if (file_exists($cachingConfigFile)) {
                $configSettings = xarModAPIFunc('xarcachemanager',
                                                'admin',
                                                'get_cachingconfig',
                                                array('from' => 'file',
                                                      'cachingConfigFile' => $cachingConfigFile));
                @unlink($cachingConfigFile);
                copy($defaultConfigFile, $cachingConfigFile); 
                xarModAPIFunc('xarcachemanager', 'admin', 'save_cachingconfig', 
                    array('configSettings' => $configSettings,
                          'cachingConfigFile' => $cachingConfigFile));                
            } else {
                copy($defaultConfigFile, $cachingConfigFile);
            }
            // switch to the file based block caching enabler
            if (xarModGetVar('xarcachemanager', 'CacheBlockOutput')) {
                $outputCacheDir = $varCacheDir . '/output/';
                if(!file_exists($outputCacheDir . 'cache.blocklevel')) {
                    touch($outputCacheDir . 'cache.blocklevel');
                }
                xarModDelVar('xarcachemanager', 'CacheBlockOutput');
            }
        case '0.3.1':
            // Code to upgrade from the 0.3.1 version (base block level caching)
            // Bring the config file up to current version
            if (file_exists($cachingConfigFile)) {
                $configSettings = xarModAPIFunc('xarcachemanager',
                                                'admin',
                                                'get_cachingconfig',
                                                array('from' => 'file',
                                                      'cachingConfigFile' => $cachingConfigFile));
                @unlink($cachingConfigFile);
                copy($defaultConfigFile, $cachingConfigFile); 
                xarModAPIFunc('xarcachemanager', 'admin', 'save_cachingconfig', 
                    array('configSettings' => $configSettings,
                          'cachingConfigFile' => $cachingConfigFile));                
            } else {
                copy($defaultConfigFile, $cachingConfigFile);
            }

            // set up the new output sub-directorys
            if (!xarcachemanager_fs_setup(array('varCacheDir' => $varCacheDir))) {
                return false;
            }
            
            // since we've moved around where output will be cached, flush everything out
            if (!function_exists('xarOutputFlushCached')) {
                include_once('includes/xarCache.php');
                xarCache_init(array('cacheDir' => $varCacheDir . '/output'));
            }
            xarOutputFlushCached('');
            break;
        case '0.3.2':
            // Code to upgrade from the 0.3.2 version (base block level caching)
            // Double check the file system setup
            if (!xarcachemanager_fs_setup(array('varCacheDir' => $varCacheDir))) {
                return false;
            }
            // Bring the config file up to current version
            if (file_exists($cachingConfigFile)) {
                $configSettings = xarModAPIFunc('xarcachemanager',
                                                'admin',
                                                'get_cachingconfig',
                                                array('from' => 'file',
                                                      'cachingConfigFile' => $cachingConfigFile));
                @unlink($cachingConfigFile);
                copy($defaultConfigFile, $cachingConfigFile); 
                xarModAPIFunc('xarcachemanager', 'admin', 'save_cachingconfig', 
                    array('configSettings' => $configSettings,
                          'cachingConfigFile' => $cachingConfigFile));                
            } else {
                copy($defaultConfigFile, $cachingConfigFile);
            }

        case '0.3.3':
            // Code to upgrade from the 0.3.3 version (use xar_cache_data as optional replacement for filesystem)
            xarcachemanager_create_cache_data();

            // Bring the config file up to current version
            if (file_exists($cachingConfigFile)) {
                $configSettings = xarModAPIFunc('xarcachemanager',
                                                'admin',
                                                'get_cachingconfig',
                                                array('from' => 'file',
                                                      'cachingConfigFile' => $cachingConfigFile));
                @unlink($cachingConfigFile);
                copy($defaultConfigFile, $cachingConfigFile); 
                xarModAPIFunc('xarcachemanager', 'admin', 'save_cachingconfig', 
                    array('configSettings' => $configSettings,
                          'cachingConfigFile' => $cachingConfigFile));                
            } else {
                // as of version 0.3.3 we can restore the config from modvars
                $configSettings = xarModAPIFunc('xarcachemanager',
                                                'admin',
                                                'get_cachingconfig',
                                                array('from' => 'db',
                                                      'cachingConfigFile' => $cachingConfigFile));
                copy($defaultConfigFile, $cachingConfigFile); 
                xarModAPIFunc('xarcachemanager', 'admin', 'save_cachingconfig', 
                    array('configSettings' => $configSettings,
                          'cachingConfigFile' => $cachingConfigFile));
            }

        case '0.4.0':
            // Code to upgrade from the 0.4.0 version (base module level caching)
            break;
        case '1.0.0':
            // Code to upgrade from version 1.0.0 goes here
            break;
        case '2.0.0':
            // Code to upgrade from version 2.0.0 goes here
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
    $varCacheDir = xarCoreGetVarDirPath() . '/cache';
    $cacheOutputDir = $varCacheDir . '/output';
    if (is_dir($cacheOutputDir)) {
        //if still there, remove the cache.touch file, this turns everything off
        if (file_exists($cacheOutputDir . '/cache.touch')) {
            @unlink($cacheOutputDir . '/cache.touch');
        }

        // clear out the cache
        @xarcachemanager_rmdirr($cacheOutputDir);
    }

    // remove the caching config file
    if (file_exists($varCacheDir . '/config.caching.php')) {
        @unlink($varCacheDir . '/config.caching.php');
    }

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
    if (!xarModUnregisterHook('item', 'modify', 'GUI',
                              'xarcachemanager', 'admin', 'modifyhook')) {
        return false;
    }
    if (!xarModUnregisterHook('module', 'updateconfig', 'API',
                              'xarcachemanager', 'admin', 'updateconfighook')) {
        return false;
    }

    // Remove module variables
    xarModDelAllVars('xarcachemanager');

    // Remove Masks and Instances
    xarRemoveMasks('xarcachemanager');

    // Deletion successful
    return true;
} 

/**
 * Setup the config.caching file and the output directories
 *
 * @param string $args['varCacheDir']
 * @return bool Returns true on success, false on failure
 * @throws FUNCTION_FAILED
 * @todo special handling for "repair" during upgrades
 */
function xarcachemanager_fs_setup($args)
{
    extract($args);
    
    // default var cache directory
    if (!isset($varCacheDir)) { 
        $varCacheDir = xarCoreGetVarDirPath() . '/cache';
    }
    
    // output cache directory
    $cacheOutputDir = $varCacheDir . '/output';
    
    // caching config files
    $defaultConfigFile = 'modules/xarcachemanager/config.caching.php.dist';
    $cachingConfigFile = $varCacheDir .'/config.caching.php';
    
    // confirm that the things are ready to be set up
    if (is_writable($varCacheDir)) {
        if (!file_exists($cachingConfigFile)) {
            copy($defaultConfigFile, $cachingConfigFile);
        }
    } else {
        if (!is_dir($cacheOutputDir) || !file_exists($cachingConfigFile)) {
            // tell them that cache needs to be writable or manually create output dir
            $msg=xarML('The #(1) directory must be writable by the web server 
                       for the install script to set up output caching for you. 
                       The xarCacheManager module has not been installed, 
                       please make the #(1) directory writable by the web server
                       before re-trying to install this module.  
                       Alternatively, you can manually create the #(2) directory
                       and copy the #(3) file to #(4) - the #(2) directory and 
                       the #(4) file must be writable by the web server for 
                       output caching to work.',
                       $varCacheDir,
                       $cacheOutputDir,
                       $defaultConfigFile,
                       $cachingConfigFile);
            xarErrorSet(XAR_SYSTEM_EXCEPTION,'FUNCTION_FAILED',
                            new SystemException($msg));
            return false;
        }           
    }
    
    // confirm the caching config file is good to go
    if (!is_writable($cachingConfigFile)) {
        $msg=xarML('The #(1) file must be writable by the web server for 
                   output caching to work.', $cachingConfigFile);
        xarErrorSet(XAR_SYSTEM_EXCEPTION,'FUNCTION_FAILED',
                        new SystemException($msg));
        return false;
    }
    
    // set up the directories
    $outputCacheDirs = array($cacheOutputDir);
    $additionalDirs = array('page', 'mod', 'block');
    foreach ($additionalDirs as $addDir) {
        $outputCacheDirs[] = $cacheOutputDir . '/' . $addDir;
    }
    
    foreach ($outputCacheDirs as $setupDir) {
        // check if the directory already exists
        if (is_dir($setupDir)) {
            if (!is_writable($setupDir)) {
                $msg=xarML('The #(1) directory is not writable by the web 
                           web server. The #(1) directory must be writable by the web 
                           server process owner for output caching to work. 
                           Please change the permission on the #(1) directory
                           so that the web server can write to it.', $setupDir);
                xarErrorSet(XAR_SYSTEM_EXCEPTION,'FUNCTION_FAILED',
                                new SystemException($msg));
                return false;
            }
        } else {
            $old_umask = umask(0);
            mkdir($setupDir, 0777);
            umask($old_umask);
            if (!file_exists($setupDir.'/index.html')) {
                @touch($setupDir.'/index.html');
            }
        }
    }
    return true;
}

/**
 * Delete a file, or a folder and its contents
 *
 * @author    Aidan Lister <aidan@php.net>
 * @version   1.0
 * @param     string   $dirname   The directory to delete
 * @return    bool     Returns true on success, false on failure
 */
function xarcachemanager_rmdirr($dirname)
{
    // delete a file
    if (is_file($dirname)) {
        return unlink($dirname);
    }
    
    // loop through the folder
    $dir = dir($dirname);
    while (false !== $entry = $dir->read()) {
        // skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }
        
        // Deep delete directories
        if (is_dir("$dirname/$entry")) {
            xarcachemanager_rmdirr("$dirname/$entry");
        } else {
            unlink("$dirname/$entry");
        }
    }
    
    // clean up
    $dir->close();
    return rmdir($dirname);
}

// TODO: if we want to re-use this for compiled templates someday,
//       this will need to move to the core somewhere...
function xarcachemanager_create_cache_data()
{
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // optional database storage for cached data (instead of filesystem)
    $cachedatatable = $xartable['cache_data'];

    $xartables = $dbconn->MetaTables();
    if (!in_array($cachedatatable, $xartables)) {
        // Load Table Maintenance API (still some issues with xarDataDict)
        xarDBLoadTableMaintenanceAPI();

        $query = xarDBCreateTable($cachedatatable,
                                  array('xar_id'   => array('type'        => 'integer',
                                                            'null'        => false,
                                                            'default'     => '0',
                                                            'increment'   => true,
                                                            'primary_key' => true),
                                        // cache type : page, block, template, module, ...
                                        'xar_type' => array('type'        => 'varchar',
                                                            'size'        => 20,
                                                            'null'        => false,
                                                            'default'     => ''),
                                        // cache key
                                        'xar_key'  => array('type'        => 'varchar',
                                                            'size'        => 127,
                                                            'null'        => false,
                                                            'default'     => ''),
                                        // cache code
                                        'xar_code' => array('type'        => 'varchar',
                                                            'size'        => 32,
                                                            'null'        => false,
                                                            'default'     => ''),
                                        // last modified time
                                        'xar_time' => array('type'        => 'integer',
                                                            'null'        => false,
                                                            'default'     => '0'),
                                        // size of the cached data (e.g. for clean-up or gzip)
                                        'xar_size' => array('type'        => 'integer',
                                                            'null'        => false,
                                                            'default'     => '0'),
                                        // check for the cached data (e.g. crc for gzip, or md5 for ...)
                                        'xar_check' => array('type'        => 'varchar',
                                                             'size'        => 32,
                                                             'null'        => false,
                                                             'default'     => ''),
                                        // the actual cached data
                                        'xar_data'  => array('type'        => 'text',
                                                             'size'        => 'medium', // 16 MB
                                                             'null'        => false)));
        if (empty($query)) return; // throw back
        $result =& $dbconn->Execute($query);
        if (!$result) return;

    // TODO: verify if separate indexes work better here or not (varchar)
        $query = xarDBCreateIndex($cachedatatable,
                                  array('name'   => 'i_' . xarDBGetSiteTablePrefix() . '_cachedata_combo',
                                        'fields' => array('xar_type',
                                                          'xar_key',
                                                          'xar_code')));
    // TODO: verify if we can make this index unique despite concurrent saves
    //                                    'unique' => 'true'));
        if (empty($query)) return; // throw back
        $result = $dbconn->Execute($query);
        if (!isset($result)) return;
    }

}

?>
