<?php
/**
 * File: $Id: s.xaradmin.php 1.28 03/02/08 17:38:40-05:00 John.Cox@mcnabb. $
 * 
 * Userpoints System
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * @subpackage userpoints module
 * @author Vassilis Stratigakis 
 */

/**
 * initialise the userpoints module
 */
function userpoints_init()
{
    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // Load Table Maintainance API
    xarDBLoadTableMaintenanceAPI();
    // Create table
    $fields = array('xar_rid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_moduleid' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'default' => '0'),
        'xar_itemtype' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'default' => '0'),
        'xar_itemid' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'default' => '0'),
        'xar_rating' => array('type' => 'float', 'size' => 'double', 'width' => 3, 'decimals' => 5, 'null' => false, 'default' => '0'),
        'xar_numuserpoints' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '1')
        );
    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($xartable['userpoints'], $fields);
    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    // TODO: compare with having 2 indexes (cfr. hitcount)
    $query = xarDBCreateIndex($xartable['userpoints'],
        array('name' => 'i_' . xarDBGetSiteTablePrefix() . '_ratingcombo',
            'fields' => array('xar_moduleid', 'xar_itemtype', 'xar_itemid'),
            'unique' => true));

    $result = &$dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBCreateIndex($xartable['userpoints'],
        array('name' => 'i_' . xarDBGetSiteTablePrefix() . '_rating',
            'fields' => array('xar_rating'),
            'unique' => false));

    $result = &$dbconn->Execute($query);
    if (!$result) return;
    // Set up module variables
    xarModSetVar('userpoints', 'defaultscore', 10);
    // Set up module hooks
    if (!xarModRegisterHook('item',
                            'display',
                            'GUI',
                            'userpoints',
                            'user',
                            'display')
	    ) 
		                       {
                               return false;
                               }

    // when a whole module is removed, e.g. via the modules admin screen
    // (set object ID to the module name !)
    if (!xarModRegisterHook('module', 'remove', 'API',
                           'userpoints', 'admin', 'deleteall')) {
        return false;
    }

    /**
     * Define instances for this module
     * Format is
     * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
     */

    $query1 = "SELECT DISTINCT $xartable[modules].xar_name FROM $xartable[userpoints] LEFT JOIN $xartable[modules] ON $xartable[userpoints].xar_moduleid = $xartable[modules].xar_regid";
    $query2 = "SELECT DISTINCT xar_itemtype FROM $xartable[userpoints]";
    $query3 = "SELECT DISTINCT xar_itemid FROM $xartable[userpoints]";
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
    xarDefineInstance('userpoints', 'Item', $instances);
    $userpointstable=$xartable['userpoints'];

    $query1 = "SELECT DISTINCT $xartable[modules].xar_name FROM $xartable[userpoints] LEFT JOIN $xartable[modules] ON $xartable[userpoints].xar_moduleid = $xartable[modules].xar_regid";
    $query2 = "SELECT DISTINCT xar_itemtype FROM $xartable[userpoints]";
    $query3 = "SELECT DISTINCT xar_itemid FROM $xartable[userpoints]";
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
    // FIXME: this seems to be some left-over from the old template module
    xarDefineInstance('userpoints', 'Template', $instances);

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */

    xarRegisterMask('OverviewUserpoints', 'All', 'userpoints', 'All', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadUserpoints', 'All', 'userpoints', 'All', 'All', 'ACCESS_READ');
    xarRegisterMask('AddUserpoints', 'All', 'userpoints', 'All', 'All', 'ACCESS_ADD');
    xarRegisterMask('DeleteUserpoints', 'All', 'userpoints', 'All', 'All', 'ACCESS_DELETE');
    xarRegisterMask('AdminUserpoints', 'All', 'userpoints', 'All', 'All', 'ACCESS_ADMIN');

    xarRegisterMask('CommentUserpoints', 'All', 'userpoints', 'Item', 'All:All:All', 'ACCESS_COMMENT');
    xarRegisterMask('EditUserpointsTemplate', 'All', 'userpoints', 'Template', 'All:All:All', 'ACCESS_ADMIN');
    // Initialisation successful
    return true;
}

/**
 * upgrade the userpoints module from an old version
 */
function userpoints_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case 1.0:
        case '1.0':
            // Code to upgrade from version 1.0 goes here
            break;
        case '1.1.0':
            // Code to upgrade from version 1.1 goes here
            // delete/initialize the whole thing again
            userpoints_delete();
            userpoints_init();

            break;
    }

    return true;
}

/**
 * delete the userpoints module
 */
function userpoints_delete()
{
    // Remove module hooks
    if (!xarModUnregisterHook('item',
            'display',
            'GUI',
            'userpoints',
            'user',
            'display')) return;

    if (!xarModUnregisterHook('module', 'remove', 'API',
                             'userpoints', 'admin', 'deleteall')) {
        return;
    }

    // Remove Masks and Instances
    xarRemoveMasks('userpoints');
    xarRemoveInstances('userpoints');

    // Delete module variables
    xarModDelVar('userpoints', 'defaultscore');

    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();
    // Delete tables
    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['userpoints']);
    if (empty($query)) return; // throw back
    // Drop the table and send exception if returns false.
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    // Remove Masks and Instances
    xarRemoveMasks('userpoints');
    xarRemoveInstances('userpoints');
    // Deletion successful
    return true;
}

?>
