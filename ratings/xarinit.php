<?php
/**
 * File: $Id: s.xaradmin.php 1.28 03/02/08 17:38:40-05:00 John.Cox@mcnabb. $
 *
 * Ratings System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * @subpackage ratings module
 * @author Jim McDonald
 */

/**
 * initialise the ratings module
 */
function ratings_init()
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
        'xar_numratings' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '1')
        );
    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($xartable['ratings'], $fields);
    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    // TODO: compare with having 2 indexes (cfr. hitcount)
    $query = xarDBCreateIndex($xartable['ratings'],
        array('name' => 'i_' . xarDBGetSiteTablePrefix() . '_ratingcombo',
            'fields' => array('xar_moduleid', 'xar_itemtype', 'xar_itemid'),
            'unique' => true));

    $result = &$dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBCreateIndex($xartable['ratings'],
        array('name' => 'i_' . xarDBGetSiteTablePrefix() . '_rating',
            'fields' => array('xar_rating'),
            'unique' => false));

    $result = &$dbconn->Execute($query);
    if (!$result) return;
    // Set up module variables
    xarModSetVar('ratings', 'defaultstyle', 'outoffivestars');
    xarModSetVar('ratings', 'seclevel', 'medium');
    // Set up module hooks
    if (!xarModRegisterHook('item',
            'display',
            'GUI',
            'ratings',
            'user',
            'display')) {
        return false;
    }

    // when a whole module is removed, e.g. via the modules admin screen
    // (set object ID to the module name !)
    if (!xarModRegisterHook('module', 'remove', 'API',
                           'ratings', 'admin', 'deleteall')) {
        return false;
    }

    /**
     * Define instances for this module
     * Format is
     * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
     */

    $query1 = "SELECT DISTINCT $xartable[modules].xar_name FROM $xartable[ratings] LEFT JOIN $xartable[modules] ON $xartable[ratings].xar_moduleid = $xartable[modules].xar_regid";
    $query2 = "SELECT DISTINCT xar_itemtype FROM $xartable[ratings]";
    $query3 = "SELECT DISTINCT xar_itemid FROM $xartable[ratings]";
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
    xarDefineInstance('ratings', 'Item', $instances);
    $ratingstable=$xartable['ratings'];

    $query1 = "SELECT DISTINCT $xartable[modules].xar_name FROM $xartable[ratings] LEFT JOIN $xartable[modules] ON $xartable[ratings].xar_moduleid = $xartable[modules].xar_regid";
    $query2 = "SELECT DISTINCT xar_itemtype FROM $xartable[ratings]";
    $query3 = "SELECT DISTINCT xar_itemid FROM $xartable[ratings]";
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
    xarDefineInstance('ratings', 'Template', $instances);

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */

    xarRegisterMask('OverviewRatings', 'All', 'ratings', 'All', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadRatings', 'All', 'ratings', 'All', 'All', 'ACCESS_READ');
    xarRegisterMask('AddRatings', 'All', 'ratings', 'All', 'All', 'ACCESS_ADD');
    xarRegisterMask('DeleteRatings', 'All', 'ratings', 'All', 'All', 'ACCESS_DELETE');
    xarRegisterMask('AdminRatings', 'All', 'ratings', 'All', 'All', 'ACCESS_ADMIN');

    xarRegisterMask('CommentRatings', 'All', 'ratings', 'Item', 'All:All:All', 'ACCESS_COMMENT');
    xarRegisterMask('EditRatingsTemplate', 'All', 'ratings', 'Template', 'All:All:All', 'ACCESS_ADMIN');
    // Initialisation successful
    return true;
}

/**
 * upgrade the ratings module from an old version
 */
function ratings_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case 1.0:
            // Code to upgrade from version 1.0 goes here
            break;
        case 1.1:
            // Code to upgrade from version 1.1 goes here
            // delete/initialize the whole thing again
            ratings_delete();
            ratings_init();

            break;
        case '1.2':
    }

    return true;
}

/**
 * delete the ratings module
 */
function ratings_delete()
{
    // Remove module hooks
    if (!xarModUnregisterHook('item',
            'display',
            'GUI',
            'ratings',
            'user',
            'display')) return;

    if (!xarModUnregisterHook('module', 'remove', 'API',
                             'ratings', 'admin', 'deleteall')) {
        return;
    }

    // Remove Masks and Instances
    xarRemoveMasks('ratings');
    xarRemoveInstances('ratings');

    // Delete module variables
    xarModDelVar('ratings', 'defaultstyle');
    xarModDelVar('ratings', 'seclevel');

    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();
    // Delete tables
    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['ratings']);
    if (empty($query)) return; // throw back
    // Drop the table and send exception if returns false.
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    // Remove Masks and Instances
    xarRemoveMasks('ratings');
    xarRemoveInstances('ratings');
    // Deletion successful
    return true;
}

?>
