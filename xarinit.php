<?php
/**
 * Hitcount
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Hitcount Module
 * @link http://xaraya.com/index.php/release/177.html
 * @author Hitcount Module Development Team
 */

/**
 * initialise the hitcount module
 * Initialisation functions for hitcount
 *
 * @Author Original author: Jim McDonald
 */
function hitcount_init()
{
    // Set ModVar
    xarModVars::set('hitcount', 'countadmin', 0);

    // Get database information
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    //Load Table Maintenance API
    sys::import('xaraya.tableddl');

    // Create tables
    $query = xarDBCreateTable($xartable['hitcount'],
                             array('id'         => array('type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'increment'   => true,
                                                            'primary_key' => true),
// TODO: replace with unique id
                                   'module_id'  => array('type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0'),
                                   'itemtype'   => array('type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0'),
                                   'itemid'     => array('type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0'),
                                   'hits'       => array('type'        => 'integer',
                                                            'null'        => false,
                                                            'size'        => 'big',
                                                            'default'     => '0'),
                                   'lasthit'    => array('type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0')));

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBCreateIndex($xartable['hitcount'],
                             array('name'   => 'i_' . xarDB::getPrefix() . '_hitcombo',
                                   'fields' => array('module_id','itemtype', 'itemid'),
                                   'unique' => false));

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBCreateIndex($xartable['hitcount'],
                             array('name'   => 'i_' . xarDB::getPrefix() . '_hititem',
                                   'fields' => array('itemid'),
                                   'unique' => false));

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBCreateIndex($xartable['hitcount'],
                             array('name'   => 'i_' . xarDB::getPrefix() . '_hits',
                                   'fields' => array('hits'),
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

    $query1 = "SELECT DISTINCT $xartable[modules].name FROM $xartable[hitcount] LEFT JOIN $xartable[modules] ON $xartable[hitcount].module_id = $xartable[modules].regid";
    $query2 = "SELECT DISTINCT itemtype FROM $xartable[hitcount]";
    $query3 = "SELECT DISTINCT itemid FROM $xartable[hitcount]";
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

    xarRegisterPrivilege('ViewHitcount','All','hitcount','All','All','ACCESS_OVERVIEW');
    xarRegisterPrivilege('ReadHitcount','All','hitcount','All','All','ACCESS_READ');
    xarRegisterPrivilege('CommmentHitcount','All','hitcount','All','All','ACCESS_COMMENT');
    xarRegisterPrivilege('ModerateHitcount','All','hitcount','All','All','ACCESS_MODERATE');
    xarRegisterPrivilege('EditHitcount','All','hitcount','All','All','ACCESS_EDIT');
    xarRegisterPrivilege('AddHitcount','All','hitcount','All','All','ACCESS_ADD');
    xarRegisterPrivilege('ManageHitcount','All','hitcount','All','All:All','ACCESS_DELETE');
    xarRegisterPrivilege('AdminHitcount','All','hitcount','All','All','ACCESS_ADMIN');

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
        case '2.0.0':
            // intermediate versions from repository in jamaica 2.0.0-b2 may have wrong module id's
            // stored in xar_hitcount

        case '2.0.1':

            break;
    }

    return true;
}

/**
 * delete the hitcount module
 */
function hitcount_delete()
{

    xarModVars::delete('hitcount', 'countadmin');
    // Remove module hooks
    if (!xarModUnregisterHook('item', 'display', 'GUI',
                             'hitcount', 'user', 'display')) {
        xarSession::setVar('errormsg', xarML('Could not unregister hook'));
    }
    if (!xarModUnregisterHook('item', 'create', 'API',
                             'hitcount', 'admin', 'create')) {
        xarSession::setVar('errormsg', xarML('Could not unregister hook'));
    }
    if (!xarModUnregisterHook('item', 'delete', 'API',
                             'hitcount', 'admin', 'delete')) {
        xarSession::setVar('errormsg', xarML('Could not unregister hook'));
    }
    if (!xarModUnregisterHook('module', 'remove', 'API',
                             'hitcount', 'admin', 'deleteall')) {
        xarSession::setVar('errormsg', xarML('Could not unregister hook'));
    }

    // Get database information
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    //Load Table Maintenance API
    sys::import('xaraya.tableddl');

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
