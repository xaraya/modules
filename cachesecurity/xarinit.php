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

/*****
*
* CREATE TABLE xar_seccache_privileges (
*  xar_realm varchar(32) NOT NULL default '',
*  xar_module varchar(32) NOT NULL default '',
*  xar_component varchar(32) NOT NULL default '',
*  xar_instance1 varchar(32) NOT NULL default '',
*  xar_instance2 varchar(32) NOT NULL default '',
*  xar_instance3 varchar(32) NOT NULL default '',
*  xar_instance4 varchar(32) NOT NULL default '',
*  xar_instance5 varchar(32) NOT NULL default '',
*  xar_instance6 varchar(32) NOT NULL default '',
*  xar_level int(11) NOT NULL default '0',
*  xar_priv_id int(11) NOT NULL default '0',
*  PRIMARY KEY  (xar_priv_id),
*  KEY i_xar_seccache_privlevel (xar_level),
*  KEY i_xar_seccache_privmain (xar_realm,xar_module,xar_component,xar_instance1,xar_instance2,xar_instance3,xar_instance4,xar_instance5,xar_instance6,xar_level,xar_priv_id)
*) TYPE=MyISAM;
******/
/*
    $query = xarDBCreateTable($tables['security_cache_privileges'],
             array('xar_priv_id'  => array('type'       => 'integer',
                                      'null'        => false,
                                      'default'     => '0',
                                      'increment'   => false,
                                      'primary_key' => true),
                   'xar_realm' => array('type'      => 'varchar',
                                      'size'        => 32,
                                      'null'        => false,
                                      'default'     => ''),
                   'xar_module' => array('type'     => 'varchar',
                                      'size'        => 32,
                                      'null'        => false,
                                      'default'     => ''),
                   'xar_component' => array('type'  => 'varchar',
                                      'size'        => 32,
                                      'null'        => false,
                                      'default'     => ''),
                   'xar_instance1' => array('type'   => 'varchar',
                                      'size'        => 32,
                                      'null'        => false,
                                      'default'     => ''),
                   'xar_instance2' => array('type'   => 'varchar',
                                      'size'        => 32,
                                      'null'        => false,
                                      'default'     => ''),
                   'xar_instance3' => array('type'   => 'varchar',
                                      'size'        => 32,
                                      'null'        => false,
                                      'default'     => ''),
                   'xar_instance4' => array('type'   => 'varchar',
                                      'size'        => 32,
                                      'null'        => false,
                                      'default'     => ''),
                   'xar_instance5' => array('type'   => 'varchar',
                                      'size'        => 32,
                                      'null'        => false,
                                      'default'     => ''),
                   'xar_instance6' => array('type'   => 'varchar',
                                      'size'        => 32,
                                      'null'        => false,
                                      'default'     => ''),
                   'xar_level' => array('type'      => 'integer',
                                      'null'        => false,
                                      'default'     => '0'),
                    ));

   if (!$dbconn->Execute($query)) return;

    $index = array('name'      => 'i_'.$sitePrefix.'_seccache_privileges_main',
                   'fields'    => array('xar_realm',
                                                  'xar_module',
                                                  'xar_component',
                                                  'xar_instance1',
                                                  'xar_instance2',
                                                  'xar_instance3',
                                                  'xar_instance4',
                                                  'xar_instance5',
                                                  'xar_instance6',
                                                  'xar_level'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($tables['security_cache_privileges'],$index);
    if (!$dbconn->Execute($query)) return;

    $index = array('name'      => 'i_'.$sitePrefix.'_seccache_privileges_level',
                   'fields'    => array('xar_level'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($tables['security_cache_privileges'],$index);
    if (!$dbconn->Execute($query)) return;

    xarDB_importTables(array('security_cache_privileges' => $tables['security_cache_privileges']));
*/
/*****
*
* CREATE TABLE xar_seccache_masks (
*  xar_realm varchar(32) NOT NULL default '',
*  xar_module varchar(32) NOT NULL default '',
*  xar_component varchar(32) NOT NULL default '',
*  xar_instance1 varchar(32) NOT NULL default '',
*  xar_instance2 varchar(32) NOT NULL default '',
*  xar_instance3 varchar(32) NOT NULL default '',
*  xar_instance4 varchar(32) NOT NULL default '',
*  xar_instance5 varchar(32) NOT NULL default '',
*  xar_instance6 varchar(32) NOT NULL default '',
*  xar_level int(11) NOT NULL default '0',
*  xar_mask_id int(11) NOT NULL default '0',
*  xar_name varchar(32) NOT NULL default '',
*  PRIMARY KEY  (xar_mask_id),
*  KEY i_xar_security_masks_name (xar_name)
*) TYPE=MyISAM;
******/

/*
    $query = xarDBCreateTable($tables['security_cache_masks'],
             array('xar_mask_id'  => array('type'       => 'integer',
                                      'null'        => false,
                                      'default'     => '0',
                                      'increment'   => false,
                                      'primary_key' => true),
                   'xar_name'  => array('type'       => 'varchar',
                                      'size'        => 32,
                                      'null'        => false,
                                      'default'     => ''),                   
                   'xar_realm' => array('type'      => 'varchar',
                                      'size'        => 32,
                                      'null'        => false,
                                      'default'     => ''),
                   'xar_module' => array('type'     => 'varchar',
                                      'size'        => 32,
                                      'null'        => false,
                                      'default'     => ''),
                   'xar_component' => array('type'  => 'varchar',
                                      'size'        => 32,
                                      'null'        => false,
                                      'default'     => ''),
                   'xar_instance1' => array('type'   => 'varchar',
                                      'size'        => 32,
                                      'null'        => false,
                                      'default'     => ''),
                   'xar_instance2' => array('type'   => 'varchar',
                                      'size'        => 32,
                                      'null'        => false,
                                      'default'     => ''),
                   'xar_instance3' => array('type'   => 'varchar',
                                      'size'        => 32,
                                      'null'        => false,
                                      'default'     => ''),
                   'xar_instance4' => array('type'   => 'varchar',
                                      'size'        => 32,
                                      'null'        => false,
                                      'default'     => ''),
                   'xar_instance5' => array('type'   => 'varchar',
                                      'size'        => 32,
                                      'null'        => false,
                                      'default'     => ''),
                   'xar_instance6' => array('type'   => 'varchar',
                                      'size'        => 32,
                                      'null'        => false,
                                      'default'     => ''),
                   'xar_level' => array('type'      => 'integer',
                                      'null'        => false,
                                      'default'     => '0'),
                    ));

   if (!$dbconn->Execute($query)) return;

    $index = array('name'      => 'i_'.$sitePrefix.'_seccache_masks_main',
                   'fields'    => array('xar_name',
                                                  'xar_realm',
                                                  'xar_module',
                                                  'xar_component',
                                                  'xar_instance1',
                                                  'xar_instance2',
                                                  'xar_instance3',
                                                  'xar_instance4',
                                                  'xar_instance5',
                                                  'xar_instance6',
                                                  'xar_level'),
                   'unique'    => true);
    $query = xarDBCreateIndex($tables['security_cache_masks'],$index);
    if (!$dbconn->Execute($query)) return;

    $index = array('name'      => 'i_'.$sitePrefix.'_seccache_masks_name',
                              'fields'       => array('xar_name'),
                              'unique'    => FALSE);
    $query = xarDBCreateIndex($tables['security_cache_masks'],$index);
    if (!$dbconn->Execute($query)) return;

    xarDB_importTables(array('security_cache_masks' => $tables['security_cache_masks']));
*/
/*****
*
CREATE TABLE xar_seccache_privsgraph (
  xar_priv_id int(11) NOT NULL default '0',
  xar_priv_sibbling_id int(11) NOT NULL default '0',
  UNIQUE KEY xar_seccache_sibbling_id_key (xar_priv_sibbling_id,xar_priv_id)
) TYPE=MyISAM;
*
******/

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


    //Creates indexes in the main privileges/masks tables
    $index = array('name'      => 'i_'.$sitePrefix.'_seccache_privileges',
                   'fields'    => array('xar_realm (20)',
                                                  'xar_module (20)',
                                                  'xar_component (20)',
                                                  'xar_pid',
                                                  'xar_instance',
                                                  'xar_level'),
                   'unique'    => false); //Table doesnt support unique subkeys
    $query = xarDBCreateIndex($tables['privileges'],$index);
    if (!$dbconn->Execute($query)) return;
    

    //Creates indexes in the main privileges/masks tables
    $index = array('name'      => 'i_'.$sitePrefix.'_seccache_masks',
                   'fields'    => array('xar_name (20)',
                                                  'xar_realm (20)',
                                                  'xar_module (20)',
                                                  'xar_component (20)',
                                                  'xar_instance',
                                                  'xar_level'),
                   'unique'    => false); //This should be true, but we have
                   //incongruent data in the database by the default install
    $query = xarDBCreateIndex($tables['security_masks'],$index);
    if (!$dbconn->Execute($query)) return;

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
            break;
        case '0.2.0':
            // Code to upgrade from the 0.2 version
            $cacheSecurityDir = xarCoreGetVarDirPath() . '/security';
            mkdir($cacheSecurityDir.'/masks', 0777);
            break;
        case '0.4.0':
            // Code to upgrade from the 0.4 version (base module level caching)
            break;
        case '1.0.0':
            // Code to upgrade from version 1.0 goes here
            break;
        case '2.0.0':
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
function cachesecurity_delete()
{
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
    $query = xarDBDropTable($tables['security_cache_rolesgraph']);
    if (empty($query)) return; // throw back
    if (!$dbconn->Execute($query)) return;

    $query = xarDBDropTable($tables['security_cache_privsgraph']);
    if (empty($query)) return; // throw back
    if (!$dbconn->Execute($query)) return;

    $query = xarDBDropIndex($tables['security_masks'],
                            array('name' => 'i_'.$sitePrefix.'_seccache_masks'));
    if (empty($query)) return; // throw back
    if (!$dbconn->Execute($query)) return;

    $query = xarDBDropIndex($tables['privileges'],
                            array('name' => 'i_'.$sitePrefix.'_seccache_privileges'));
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
