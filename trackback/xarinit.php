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
    $tables = xarDBGetTables();

    //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();

    // $query = "CREATE TABLE $trackBackTable (
    // trackbackid int(11) NOT NULL auto_increment,
    // moduleid int(11) NOT NULL default 0,
    // itemid int(11) NOT NULL default 0,
    // url varchar(255) NOT NULL default '',
    // blog_name varchar(255) NOT NULL default '',
    // title varchar(255) NOT NULL default '',
    // excerpt text,
    // PRIMARY KEY(trackbackid))";
    $query = xarDBCreateTable($tables['trackback'],
                             array('trackbackid' => array('type'        => 'integer',
                                                          'null'        => false,
                                                          'default'     => '0',
                                                          'increment'   => true,
                                                          'primary_key' => true),
                                   'moduleid'    => array('type'        => 'integer',
                                                          'unsigned'    => true,
                                                          'null'        => false,
                                                          'default'     => '0'),
                                   'itemid'      => array('type'        => 'integer',
                                                          'unsigned'    => true,
                                                          'null'        => false,
                                                          'default'     => '0'),
                                   'url'         => array('type'        => 'varchar',
                                                          'null'        => false,
                                                          'size'        => 255,
                                                          'default'     => ''),
                                   'blog_name'   => array('type'        => 'varchar',
                                                          'null'        => false,
                                                          'size'        => 255,
                                                          'default'     => ''),
                                   'title'       => array('type'        => 'varchar',
                                                          'null'        => false,
                                                          'size'        => 255,
                                                          'default'     => ''),
                                   'excerpt'     => array('type'        => 'text')));

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBCreateIndex($tables['trackback'],
                             array('name'   => 'i_'.xarDBGetSiteTablePrefix().'_trackback_moduleid',
                                   'fields' => array('moduleid'),
                                   'unique' => false));

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBCreateIndex($tables['trackback'],
                             array('name'   => 'i_'.xarDBGetSiteTablePrefix().'_trackback_itemid',
                                   'fields' => array('itemid'),
                                   'unique' => false));

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBCreateIndex($tables['trackback'],
                             array('name'   => 'i_'.xarDBGetSiteTablePrefix().'_trackback_url',
                                   'fields' => array('url'),
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

    // Define Privilege Masks
    xarRegisterMask('ViewTrackBack', 'All', 'trackback', 'TrackBack', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('AddTrackBack','All','trackback','TrackBack','All:All:All','ACCESS_ADD');
    xarRegisterMask('DeleteTrackBack','All','trackback','TrackBack','All:All:All','ACCESS_DELETE');



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
        case '1.0':
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
    $tables = xarDBGetTables();

    //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Delete tables
    $query = xarDBDropTable($tables['trackback']);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Remove Privilege Masks and Instances
    xarRemoveMasks('trackback');
    xarRemoveInstances('trackback');

    // Deletion successful
    return true;
}

?>