<?php
/**
 * File: $Id$
 *
 * Trackback Initialialization File
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage trackback
 * @author Gregor J. Rothfuss
 */

/**
 * Initialise the trackback module
 *
 * @return bool
 */
function trackback_init()
{
    // Get database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Create tables
    $query = xarDBCreateTable($xartable['trackback'],
                             array('xar_trackbackid' => array('type'        => 'integer',
                                                            'null'        => false,
                                                            'default'     => '0',
                                                            'increment'   => true,
                                                            'primary_key' => true),
                                   'xar_moduleid'   => array('type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0'),
                                   'xar_itemid'     => array('type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0'),
                                   'xar_url'       => array('type'        => 'varchar',
                                                            'null'        => false,
                                                            'size'        => 255,
                                                            'default'     => ''),
                                   'xar_blog_name'       => array('type'        => 'varchar',
                                                            'null'        => false,
                                                            'size'        => 255,
                                                            'default'     => ''),
                                   'xar_title'       => array('type'        => 'varchar',
                                                            'null'        => false,
                                                            'size'        => 255,
                                                            'default'     => ''),
                                   'xar_excerpt'       => array('type'        => 'text')));

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBCreateIndex($xartable['trackback'],
                             array('name'   => 'i_'.xarDBGetSiteTablePrefix().'_trackback_moduleid',
                                   'fields' => array('xar_moduleid'),
                                   'unique' => false));

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBCreateIndex($xartable['trackback'],
                             array('name'   => 'i_'.xarDBGetSiteTablePrefix().'_trackback_itemid',
                                   'fields' => array('xar_itemid'),
                                   'unique' => false));

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBCreateIndex($xartable['trackback'],
                             array('name'   => 'i_'.xarDBGetSiteTablePrefix().'_trackback_url',
                                   'fields' => array('xar_url'),
                                   'unique' => false));

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Set up module hooks

    // when a module item is displayed
    // (use xarVarSetCached('Hooks.trackback','save', 1) to tell trackback *not*
    // to display the hit count, but to save it in 'Hooks.trackback', 'value')
    if (!xarModRegisterHook('item', 'display', 'GUI',
                           'trackback', 'user', 'display')) {
        return false;
    }
    // when a module item is created (set extrainfo to the module name ?)
    if (!xarModRegisterHook('item', 'create', 'API',
                           'trackback', 'admin', 'create')) {
        return false;
    }
    // when a module item is deleted (set extrainfo to the module name ?)
    if (!xarModRegisterHook('item', 'delete', 'API',
                           'trackback', 'admin', 'delete')) {
        return false;
    }
    // when a whole module is removed, e.g. via the modules admin screen
    // (set object ID to the module name !)
    if (!xarModRegisterHook('module', 'remove', 'API',
                           'trackback', 'admin', 'deleteall')) {
        return false;
    }

    // Initialisation successful
    return true;
}

/**
 * Upgrade the trackback module from an old version
 *
 * @param string oldVersion
 * @return bool
 */
function trackback_upgrade($oldversion)
{
    //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Upgrade dependent on old version number
    switch($oldversion) {
        case 1.0:
            // Code to upgrade from version 1.0 goes here
            break;
        case 1.1:
            // Code to upgrade from version 1.1 goes here
            break;
    }

    return true;
}

/**
 * Delete the trackback module
 *
 * @return bool
 */
function trackback_delete()
{
    // Remove module hooks
    if (!xarModUnregisterHook('item', 'display', 'GUI',
                             'trackback', 'user', 'display')) {
        xarSessionSetVar('errormsg', xarML('Could not unregister hook'));
    }
    if (!xarModUnregisterHook('item', 'create', 'API',
                             'trackback', 'admin', 'create')) {
        xarSessionSetVar('errormsg', xarML('Could not unregister hook'));
    }
    if (!xarModUnregisterHook('item', 'delete', 'API',
                             'trackback', 'admin', 'delete')) {
        xarSessionSetVar('errormsg', xarML('Could not unregister hook'));
    }
    if (!xarModUnregisterHook('module', 'remove', 'API',
                             'trackback', 'admin', 'deleteall')) {
        xarSessionSetVar('errormsg', xarML('Could not unregister hook'));
    }

    // Get database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Delete tables
    $query = xarDBDropTable($xartable['trackback']);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Deletion successful
    return true;
}

?>