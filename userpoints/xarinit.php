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

    // Create table to store the user's score per item and stats

	$fields = array(
        'xar_upid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_moduleid'=>array('null'=>FALSE, 'type'=>'integer','size'=>'big', 'default'=>'0'),
        'xar_itemtype'=>array('null'=>FALSE, 'type'=>'integer','size'=>'big', 'default'=>'0'),
        'xar_objectid'=>array('type'=>'integer','size'=>'small','null'=>FALSE,'default'=>'1'),
        'xar_status'=>array('type'=>'integer','size'=>'tiny','null'=>FALSE,'default'=>'0'),
        'xar_authorid'=>array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        'xar_pubdate'=>array('type'=>'integer','unsigned'=>TRUE,'null'=>FALSE,'default'=>'0'),
        'xar_cpoints'=>array('null'=>FALSE, 'type'=>'integer','size'=>'big', 'default'=>'0'),
        'xar_rpoints'=>array('null'=>FALSE, 'type'=>'integer','size'=>'big', 'default'=>'0'),
        'xar_upoints'=>array('null'=>FALSE, 'type'=>'integer','size'=>'big', 'default'=>'0'),
        'xar_fpoints'=>array('null'=>FALSE, 'type'=>'integer','size'=>'big', 'default'=>'0')

    );

    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($xartable['userpoints'], $fields);
    if (empty($query)) return; // throw back
    
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // Create table to store the user's score per item and stats

	$fields = array(
        'xar_upid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_moduleid'=>array('null'=>FALSE, 'type'=>'integer','size'=>'big', 'default'=>'0'),
        'xar_itemtype'=>array('null'=>FALSE, 'type'=>'integer','size'=>'big', 'default'=>'0'),
        'xar_objectid'=>array('type'=>'integer','size'=>'small','null'=>FALSE,'default'=>'1'),
        'xar_status'=>array('type'=>'integer','size'=>'tiny','null'=>FALSE,'default'=>'0'),
        'xar_authorid'=>array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        'xar_pubdate'=>array('type'=>'integer','unsigned'=>TRUE,'null'=>FALSE,'default'=>'0'),
        'xar_dpoints'=>array('null'=>FALSE, 'type'=>'integer','size'=>'big', 'default'=>'0')
    );

    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($xartable['userpoints_display'], $fields);
    if (empty($query)) return; // throw back
    
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // Create table to hold rank information

	$fields = array('xar_id' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
                    'xar_rankname' => array('type' => 'varchar', 'size' => 32, 'null' => false),
                    'xar_rankminscore' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0')
        );

    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($xartable['userpoints_ranks'], $fields);
    if (empty($query)) return; // throw back
    
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // Create table to hold the user's score

	$fields = array('xar_id' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
                    'xar_authorid'=>array('null'=>FALSE, 'type'=>'integer','size'=>'big', 'default'=>'0'),
                    'xar_totalscore'=>array('null'=>FALSE, 'type'=>'integer','size'=>'big', 'default'=>'0')
        );

    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($xartable['userpoints_score'], $fields);
    if (empty($query)) return; // throw back
    
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    
    xarModSetVar('userpoints', 'ranksperpage', 10);
    xarModSetVar('userpoints', 'showadminscore', 1);
    xarModSetVar('userpoints', 'scowanonscore', 0);
    xarModSetVar('userpoints', 'SupportShortURLs', 0);
    xarModSetVar('userpoints', 'defaultcreate', 10);
	xarModSetVar('userpoints', 'defaultdelete', 10);
    xarModSetVar('userpoints', 'defaultdisplay', 0.01);
    xarModSetVar('userpoints', 'defaultupdate', 0.05);
    xarModSetVar('userpoints', 'defaultfrontpage', 0);


    //when user performs any of the CRUD actions, have the hook fire.
    if (!xarModRegisterHook('module', 'remove', 'API',
                           'userpoints', 'admin', 'deleteall')) {
        return false;
    }   
    if (!xarModRegisterHook('item', 'create', 'API',
                           'userpoints', 'admin', 'createhook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'remove', 'API',
                           'userpoints', 'admin', 'removehook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'update', 'API',
                           'userpoints', 'admin', 'updatehook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'display', 'GUI',
                           'userpoints', 'user', 'display')) {
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
    //xarDefineInstance('userpoints', 'Template', $instances);

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

    xarRegisterMask('ReadRank', 'All', 'userpoints', 'All', 'All', 'ACCESS_READ');
    xarRegisterMask('AddRank', 'All', 'userpoints', 'All', 'All', 'ACCESS_ADD');
    xarRegisterMask('DeleteRank', 'All', 'userpoints', 'All', 'All', 'ACCESS_DELETE');
    xarRegisterMask('AdminRank', 'All', 'userpoints', 'All', 'All', 'ACCESS_ADMIN');
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
    if (!xarModUnregisterHook('module', 'remove', 'API',
                           'userpoints', 'admin', 'deleteall')) {
        return false;
    }   
    if (!xarModUnregisterHook('item', 'create', 'API',
                           'userpoints', 'adminapi', 'createhook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'remove', 'API',
                           'userpoints', 'adminapi', 'removehook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'update', 'API',
                           'userpoints', 'adminapi', 'updatehook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'display', 'GUI',
                           'userpoints', 'user', 'display')) {
        return false;
    }

    // Delete module variables
    xarModDelVar('userpoints', 'ranksperpage');
    xarModDelVar('userpoints', 'showadminscore');
    xarModDelVar('userpoints', 'showanonscore');
    xarModDelVar('userpoints', 'SupportShortURLs');
    xarModDelVar('userpoints', 'defaultcreate');
    xarModDelVar('userpoints', 'defaultdelete');
    xarModDelVar('userpoints', 'defaultdisplay');
    xarModDelVar('userpoints', 'defaultupdate');
    xarModDelVar('userpoints', 'defaultfrontpage');

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
    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['userpoints_display']);
    if (empty($query)) return; // throw back
    // Drop the table and send exception if returns false.
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['userpoints_ranks']);
    if (empty($query)) return; // throw back
    // Drop the table and send exception if returns false.
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['userpoints_score']);
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
