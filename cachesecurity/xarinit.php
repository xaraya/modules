<?php
/**
 * File: $Id$
 *
 * Cache Security initialization functions
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @author Flavio Botelho <nuncanada@xaraya.com>
 */

/**
 * initialise the xarcachemanager module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function cachesecurity_init()
{
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $tables =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();
    $sitePrefix = xarDBGetSiteTablePrefix();

    $query = xarDBCreateTable($tables['security_cache_privsgraph'],
             array('xar_priv_id'  => array('type'       => 'integer',
                                      'null'        => false,
                                      'default'     => '0',
                                      'increment'   => false,
                                      'primary_key' => false),
                       'xar_priv_sibbling_id'  => array('type'       => 'integer',
                                      'null'        => false,
                                      'default'     => '0',
                                      'increment'   => false,
                                      'primary_key' => false),
                    ));

   if (!$dbconn->Execute($query)) return;

    $index = array('name'      => 'i_'.$sitePrefix.'_seccache_privsgraph_main',
                   'fields'    => array('xar_priv_id',
                                                  'xar_priv_sibbling_id'),
                   'unique'    => true);
    $query = xarDBCreateIndex($tables['security_cache_privsgraph'],$index);
    if (!$dbconn->Execute($query)) return;

    xarDB_importTables(array('security_cache_privsgraph' => $tables['security_cache_privsgraph']));

/*****
*
CREATE TABLE xar_seccache_rolesgraph (
  xar_role_id int(11) NOT NULL default '0',
  xar_role_sibbling_id int(11) NOT NULL default '0',
  xar_role_distance int(11) NOT NULL default '0',
  UNIQUE KEY i_xar_seccache_rolesgraph_id (xar_role_id,xar_role_sibbling_id,xar_role_distance)
) TYPE=MyISAM;
*
******/

    $query = xarDBCreateTable($tables['security_cache_rolesgraph'],
             array('xar_role_id'  => array('type'       => 'integer',
                                      'null'        => false,
                                      'default'     => '0'),
                       'xar_role_sibbling_id'  => array('type'       => 'integer',
                                      'null'        => false,
                                      'default'     => '0'),
                       'xar_role_distance'  => array('type'       => 'integer',
                                      'null'        => false,
                                      'default'     => '0'),
                    ));

   if (!$dbconn->Execute($query)) return;

    $index = array('name'      => 'i_'.$sitePrefix.'_seccache_rolesgraph_main',
                   'fields'    => array('xar_role_id',
                                                  'xar_role_sibbling_id',
                                                  'xar_role_distance'),
                   'unique'    => true);
    $query = xarDBCreateIndex($tables['security_cache_rolesgraph'], $index);
    if (!$dbconn->Execute($query)) return;

    xarDB_importTables(array('security_cache_rolesgraph' => $tables['security_cache_rolesgraph']));

    // Set up database tables
    $query = xarDBCreateTable($tables['security_cache_privsmasks'],
        array(
            'xar_priv_id'  => array(
                'type'       => 'integer',
                'null'        => false,
                'default'     => '0',
                'increment'   => false,
                'primary_key' => false),
            'xar_mask_id'  => array(
                'type'       => 'integer',
                'null'        => false,
                'default'     => '0',
                'increment'   => false,
                'primary_key' => false),
        ));

    if (!$dbconn->Execute($query)) return;

    $index = array(
        'name'      => 'i_'.$sitePrefix.'_seccache_privsmasks_main',
        'fields'    => array('xar_priv_id', 'xar_mask_id'),
        'unique'    => true);
    $query = xarDBCreateIndex($tables['security_cache_privsmasks'],$index);
    if (!$dbconn->Execute($query)) return;

    xarDB_importTables(array('security_cache_privsmasks' => $tables['security_cache_privsmasks']));

    //set up configvars
    xarConfigSetVar('CacheSecurity.on', false);
    xarConfigSetVar('CacheSecurity.rolesgraph', false);
    xarConfigSetVar('CacheSecurity.privsgraph', false);
    xarConfigSetVar('CacheSecurity.privsmasks', false);

    // set up permissions masks.
    xarRegisterMask('AdminCacheSecurity', 'All', 'cachesecurity', 'Item', 'All', 'ACCESS_ADMIN');

    // Register search hook
    xarModRegisterHook('item','delete','API','cachesecurity','admin','hook');
    xarModRegisterHook('item','create','API','cachesecurity','admin','hook');
    xarModRegisterHook('item','new','API','cachesecurity','admin','hook');
    xarModRegisterHook('item','update','API','cachesecurity','admin','hook');
    xarModRegisterHook('item','link','API','cachesecurity','admin','hook');
    xarModRegisterHook('item','unlink','API','cachesecurity','admin','hook');

    //Link the hooks to the privileges/roles module
    xarModAPIFunc('modules','admin','enablehooks',
        array('callerModName' => 'roles', 'hookModName' => 'cachesecurity'));
    xarModAPIFunc('modules','admin','enablehooks',
        array('callerModName' => 'privileges', 'hookModName' => 'cachesecurity'));

    // Initialisation successful
    return true;
}

/**
 * activates the cache security module
 * Synchronizes the cache and turns on the module
 */
function cachesecurity_activate()
{
/*
    //Using APIFunc during activate causes recursive errors
    if (!xarModAPIFunc('cachesecurity','admin','syncall')) return;
    if (!xarModAPIFunc('cachesecurity','admin','turnon')) return;
   */
    
    return true;
}

/**
 * deactivates the cache security module
 * Synchronizes the cache and turns on the module
 */
function cachesecurity_deactivate()
{
    if (!xarModAPIFunc('cachesecurity','admin','turnoff')) return;
    
    return true;
}

/**
 * upgrade the xarcachemanager module from an old version
 * This function can be called multiple times
 */
function cachesecurity_upgrade($oldversion)
{
    $dbconn =& xarDBGetConn();
    $tables =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();
    $sitePrefix = xarDBGetSiteTablePrefix();

    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '0.1.0':
            // Code to upgrade from the 0.1 version (base block level caching)
            // Register search hook
            xarModRegisterHook('item','delete','API','cachesecurity','admin','hook');
            xarModRegisterHook('item','create','API','cachesecurity','admin','hook');
            xarModRegisterHook('item','new','API','cachesecurity','admin','hook');
            xarModRegisterHook('item','update','API','cachesecurity','admin','hook');
            xarModRegisterHook('item','link','API','cachesecurity','admin','hook');
            xarModRegisterHook('item','unlink','API','cachesecurity','admin','hook');

            //Link the hooks to the privileges/roles module
            xarModAPIFunc('modules','admin','enablehooks',
                array('callerModName' => 'roles', 'hookModName' => 'cachesecurity'));
            xarModAPIFunc('modules','admin','enablehooks',
                array('callerModName' => 'privileges', 'hookModName' => 'cachesecurity'));

        case '0.3.0':
            xarConfigSetVar('CacheSecurity.on', false);

            // Set up database tables
            $query = xarDBCreateTable($tables['security_cache_privsmasks'],
                 array('xar_priv_id'  => array('type'       => 'integer',
                                          'null'        => false,
                                          'default'     => '0',
                                          'increment'   => false,
                                          'primary_key' => false),
                           'xar_mask_id'  => array('type'       => 'integer',
                                          'null'        => false,
                                          'default'     => '0',
                                          'increment'   => false,
                                          'primary_key' => false),
                        ));

            if (!$dbconn->Execute($query)) return;

            $index = array(
                'name'      => 'i_'.$sitePrefix.'_seccache_privsmasks_main',
                'fields'    => array('xar_priv_id', 'xar_mask_id'),
                'unique'    => true);
            $query = xarDBCreateIndex($tables['security_cache_privsmasks'],$index);
            if (!$dbconn->Execute($query)) return;

            xarDB_importTables(array('security_cache_privsmasks' => $tables['security_cache_privsmasks']));

        case '0.8.0':
            $query = xarDBDropIndex($tables['security_masks'],
                                array('name' => 'i_'.$sitePrefix.'_seccache_masks'));
            if (empty($query)) return; // throw back
            if (!$dbconn->Execute($query)) return;

            $query = xarDBDropIndex($tables['privileges'],
                                    array('name' => 'i_'.$sitePrefix.'_seccache_privileges'));
            if (empty($query)) return; // throw back
            if (!$dbconn->Execute($query)) return;

        case '2.0.0':
            // Code to upgrade from version 2.0 goes here
    }
    // Update successful
    return true;
}

/**
 * delete the xarcachemanager module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function cachesecurity_delete()
{
    //set up configvars
    //FIXME: We need someway to delete configuration variables!!!
    xarConfigSetVar('CacheSecurity.on', false);
    xarConfigSetVar('CacheSecurity.rolesgraph', false);
    xarConfigSetVar('CacheSecurity.privsgraph', false);
    xarConfigSetVar('CacheSecurity.privsmasks', false);

    // Drop the tables
    $dbconn =& xarDBGetConn();
    $tables =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();
    $sitePrefix = xarDBGetSiteTablePrefix();

/*
    $query = xarDBDropTable($tables['security_cache_privileges']);
    if (empty($query)) return; // throw back
    if (!$dbconn->Execute($query)) return;

    $query = xarDBDropTable($tables['security_cache_masks']);
    if (empty($query)) return; // throw back
    if (!$dbconn->Execute($query)) return;
*/
    $query = xarDBDropTable($tables['security_cache_privsmasks']);
    if (empty($query)) return; // throw back
    if (!$dbconn->Execute($query)) return;

    $query = xarDBDropTable($tables['security_cache_rolesgraph']);
    if (empty($query)) return; // throw back
    if (!$dbconn->Execute($query)) return;

    $query = xarDBDropTable($tables['security_cache_privsgraph']);
    if (empty($query)) return; // throw back
    if (!$dbconn->Execute($query)) return;

    // Remove Masks and Instances
    xarRemoveMasks('AdminCacheSecurity');

    //Unregister the hooks
    xarModUnRegisterHook('item','delete','API','cachesecurity','admin','hook');
    xarModUnRegisterHook('item','create','API','cachesecurity','admin','hook');
    xarModUnRegisterHook('item','new','API','cachesecurity','admin','hook');
    xarModUnRegisterHook('item','update','API','cachesecurity','admin','hook');
    xarModUnRegisterHook('item','link','API','cachesecurity','admin','hook');
    xarModUnRegisterHook('item','unlink','API','cachesecurity','admin','hook');

    // Deletion successful
    return true;
} 
?>
