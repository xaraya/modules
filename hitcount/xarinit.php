<?php
// File: $Id: s.xarinit.php 1.12 03/01/22 15:54:10+00:00 mikespub@sasquatch.pulpcontent.com $
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Purpose of file:  Initialisation functions for hitcount
// ----------------------------------------------------------------------

/**
 * initialise the hitcount module
 */
function hitcount_init()
{
    // Set ModVar
    xarModSetVar('hitcount', 'countadmin', 0);

    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Create tables
    $query = xarDBCreateTable($xartable['hitcount'],
                             array('xar_hitcountid' => array('type'        => 'integer',
                                                            'null'        => false,
                                                            'default'     => '0',
                                                            'increment'   => true,
                                                            'primary_key' => true),
// TODO: replace with unique id
                                   'xar_moduleid'   => array('type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0'),
                                   'xar_itemtype'   => array('type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0'),
                                   'xar_itemid'     => array('type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0'),
                                   'xar_hits'       => array('type'        => 'integer',
                                                            'null'        => false,
                                                            'size'        => 'big',
                                                            'default'     => '0')));

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBCreateIndex($xartable['hitcount'],
                             array('name'   => 'i_' . xarDBGetSiteTablePrefix() . '_hitcombo',
                                   'fields' => array('xar_moduleid','xar_itemtype'),
                                   'unique' => false));

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBCreateIndex($xartable['hitcount'],
                             array('name'   => 'i_' . xarDBGetSiteTablePrefix() . '_hititem',
                                   'fields' => array('xar_itemid'),
                                   'unique' => false));

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBCreateIndex($xartable['hitcount'],
                             array('name'   => 'i_' . xarDBGetSiteTablePrefix() . '_hits',
                                   'fields' => array('xar_hits'),
                                   'unique' => false));

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Set up module hooks

    // when a module item is displayed
    // (use xarVarSetCached('Hooks.hitcount','save', 1) to tell hitcount *not*
    // to display the hit count, but to save it in 'Hooks.hitcount', 'value')
    if (!xarModRegisterHook('item', 'display', 'GUI',
                           'hitcount', 'user', 'display')) {
        return false;
    }
    // when a module item is created (set extrainfo to the module name ?)
    if (!xarModRegisterHook('item', 'create', 'API',
                           'hitcount', 'admin', 'create')) {
        return false;
    }
    // when a module item is deleted (set extrainfo to the module name ?)
    if (!xarModRegisterHook('item', 'delete', 'API',
                           'hitcount', 'admin', 'delete')) {
        return false;
    }
    // when a whole module is removed, e.g. via the modules admin screen
    // (set object ID to the module name !)
    if (!xarModRegisterHook('module', 'remove', 'API',
                           'hitcount', 'admin', 'deleteall')) {
        return false;
    }

    /*********************************************************************
    * Define instances for this module
    * Format is
    * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
    *********************************************************************/

    $query1 = "SELECT DISTINCT $xartable[modules].xar_name FROM $xartable[hitcount] LEFT JOIN $xartable[modules] ON $xartable[hitcount].xar_moduleid = $xartable[modules].xar_regid";
    $query2 = "SELECT DISTINCT xar_itemtype FROM $xartable[hitcount]";
    $query3 = "SELECT DISTINCT xar_itemid FROM $xartable[hitcount]";
    $instances = array(
                        array('header' => 'Module Name:',
                                'query' => $query1,
                                'limit' => 20
                            ),
                        array('header' => 'Item Type:',
                                'query' => $query2,
                                'limit' => 20
                            ),
                        array('header' => 'Item ID:',
                                'query' => $query3,
                                'limit' => 20
                            )
                    );
    xarDefineInstance('hitcount','Item',$instances);

    /*********************************************************************
    * Register the module components that are privileges objects
    * Format is
    * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
    *********************************************************************/


    xarRegisterMask('ViewHitcountItems','All','hitcount','Item','All:All:All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadHitcountItem','All','hitcount','Item','All:All:All','ACCESS_READ');
    xarRegisterMask('DeleteHitcountItem','All','hitcount','Item','All:All:All','ACCESS_DELETE');
    xarRegisterMask('AdminHitcount','All','hitcount','All','All','ACCESS_ADMIN');


    // Initialisation successful
    return true;
}

/**
 * upgrade the hitcount module from an old version
 */
function hitcount_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case 1.0:
            // Code to upgrade from version 1.0 goes here

            // Get database information
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();

            //Load Table Maintenance API
            xarDBLoadTableMaintenanceAPI();

            $query = xarDBAlterTable($xartable['hitcount'],
                                     array('command'  => 'add',
                                           'field'    => 'xar_itemtype',
                                           'type'     => 'integer',
                                           'unsigned' => true,
                                           'null'     => false,
                                           'default'  => '0'));

            $result =& $dbconn->Execute($query);
            if (!$result) return;

            break;
        case 1.1:
            xarModSetVar('hitcount', 'countadmin', 0);
            xarRegisterMask('AdminHitcount','All','hitcount','All','All','ACCESS_ADMIN');
            $modversion['admin']          = 1;
            // Code to upgrade from version 1.1 goes here
            break;
    }

    return true;
}

/**
 * delete the hitcount module
 */
function hitcount_delete()
{
    
    xarModDelVar('hitcount', 'countadmin');
    // Remove module hooks
    if (!xarModUnregisterHook('item', 'display', 'GUI',
                             'hitcount', 'user', 'display')) {
        xarSessionSetVar('errormsg', xarML('Could not unregister hook'));
    }
    if (!xarModUnregisterHook('item', 'create', 'API',
                             'hitcount', 'admin', 'create')) {
        xarSessionSetVar('errormsg', xarML('Could not unregister hook'));
    }
    if (!xarModUnregisterHook('item', 'delete', 'API',
                             'hitcount', 'admin', 'delete')) {
        xarSessionSetVar('errormsg', xarML('Could not unregister hook'));
    }
    if (!xarModUnregisterHook('module', 'remove', 'API',
                             'hitcount', 'admin', 'deleteall')) {
        xarSessionSetVar('errormsg', xarML('Could not unregister hook'));
    }

    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Delete tables
    $query = xarDBDropTable($xartable['hitcount']);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Remove Masks and Instances
    xarRemoveMasks('hitcount');
    xarRemoveInstances('hitcount');

    // Deletion successful
    return true;
}

?>
