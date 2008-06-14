<?php
/**
 * Ratings Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ratings Module
 * @link http://xaraya.com/index.php/release/41.html
 * @author Jim McDonald
 */
/**
 * initialise the ratings module
 * @return bool true for the successfull install
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
        'xar_rating' => array('type' => 'float', 'size' => 'double', 'width' => 8, 'decimals' => 5, 'null' => false, 'default' => '0'),
        'xar_numratings' => array('type' => 'integer', 'size' => 'medium', 'null' => false, 'default' => '1')
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
 * @param string oldversion
 * @return bool true on success of upgrade
 */
function ratings_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '1.0':
            // Code to upgrade from version 1.0 goes here
        case '1.1':
            // Code to upgrade from version 1.1 goes here
            // delete/initialize the whole thing again
            ratings_delete();
            ratings_init();
        case '1.2.0':
            // clean up double hook registrations
            xarModUnregisterHook('module', 'remove', 'API', 'ratings', 'admin', 'deleteall');
            xarModRegisterHook('module', 'remove', 'API', 'ratings', 'admin', 'deleteall');
            $hookedmodules = xarModAPIFunc('modules', 'admin', 'gethookedmodules',
                                           array('hookModName' => 'ratings'));
            if (isset($hookedmodules) && is_array($hookedmodules)) {
                foreach ($hookedmodules as $modname => $value) {
                    foreach ($value as $itemtype => $val) {
                        xarModAPIFunc('modules','admin','enablehooks',
                                      array('hookModName' => 'ratings',
                                            'callerModName' => $modname,
                                            'callerItemType' => $itemtype));
                    }
                }
            }

        case '1.2.1':
            // Set up shownum modvar, including for existing hooked modules
            xarModSetVar('ratings', 'shownum', 1);
            $hookedmodules = xarModAPIFunc('modules', 'admin', 'gethookedmodules',
                                   array('hookModName' => 'ratings'));
            if (isset($hookedmodules) && is_array($hookedmodules)) {
                foreach ($hookedmodules as $modname => $value) {
                    // we have hooks for individual item types here
                    if (!isset($value[0])) {
                        // Get the list of all item types for this module (if any)
                        $mytypes = xarModAPIFunc($modname,'user','getitemtypes',
                                                 // don't throw an exception if this function doesn't exist
                                                 array(), 0);
                        foreach ($value as $itemtype => $val) {
                            xarModSetVar('ratings',"shownum.$modname.$itemtype", 1);
                        }
                    } else {
                        xarModSetVar('ratings', 'shownum.' . $modname, 1);
                    }
                }
            }

            // modify field xar_ratings.xar_rating
            // Get database information
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $query= "ALTER TABLE " . $xartable['ratings'] . "
                           MODIFY COLUMN xar_rating double(8,5) NOT NULL default '0.00000'";
            $result =& $dbconn->Execute($query);
            if (!$result) return;


    }
    return true;
}

/**
 * delete the ratings module
 * @return bool true on successfull deletion
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

    // Delete module variables
    xarModDelAllVars('ratings');

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
