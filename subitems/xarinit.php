<?php
/**
 * File: $Id: s.xarinit.php 1.17 03/03/18 02:35:04-05:00 johnny@falling.local.lan $
 *
 * subitems initialization functions
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage subitems
 * @author subitems module development team
 */

/**
 * Upgraded to the new security schema by Vassilis Stratigakis
 * http://www.tequilastarrise.net
 */

/**
 * initialise the subitems module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function subitems_init()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($xartable['subitems_ddobjects'], array(
        'xar_objectid' => array('type' => 'integer', 'null' => false, 'unsigned' => true, 'default' => '0','primary_key' => true),
        'xar_module' => array('type' => 'varchar', 'null' => false, 'size' => 255, 'default' => ''),
        'xar_itemtype' => array('type' => 'integer', 'null' => false, 'unsigned' => true, 'default' => '0'),
        'xar_template' => array('type' => 'varchar','null' => false,'size' => 255,'default' => '')
        ));
    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBCreateTable($xartable['subitems_ddids'], array(
        'xar_itemid' => array('type' => 'integer', 'null' => false, 'unsigned' => true, 'default' => '0'),
        'xar_ddid' => array('type' => 'integer', 'null' => false,'unsigned' => true, 'default' => '0'),
        'xar_objectid' => array('type' => 'integer', 'null' => false,'unsigned' => true, 'default' => '0')
        ));
    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // If your module supports short URLs, the website administrator should
    // be able to turn it on or off in your module administration
    xarModSetVar('subitems', 'SupportShortURLs', 0);

    if (!xarModRegisterHook('item', 'create', 'API',
            'subitems', 'user', 'hook_item_create')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'delete', 'API',
            'subitems', 'user', 'hook_item_delete')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'update', 'API',
            'subitems', 'user', 'hook_item_update')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'modify', 'GUI',
            'subitems', 'user', 'hook_item_modify')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'new', 'GUI',
            'subitems', 'user', 'hook_item_new')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'display', 'GUI',
            'subitems', 'user', 'hook_item_display')) {
        return false;
    }

// The following masks deal with the link definitions and the subitems module itself
// TODO: extend with other relevant masks and instances (e.g. objectid/moduleid/itemtype/itemid/ddid)
    xarRegisterMask('AdminSubitems', 'All', 'subitems', 'All', 'All', 'ACCESS_ADMIN');

// Note : access to add/edit/delete individual subitem entries is managed via DD masks

    // Initialisation successful
    return true;
}

/**
 * upgrade the subitems module from an old version
 * This function can be called multiple times
 */
function subitems_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '1.0.0':
            // Code to upgrade from version 1.0.0 goes here
            xarRemoveMasks('subitems');
            xarRegisterMask('AdminSubitems', 'All', 'subitems', 'All', 'All', 'ACCESS_ADMIN');

        case '1.0.1':
            // Code to upgrade from version 1.0.1 goes here

        case '2.0.0':
            // Code to upgrade from version 2.0.0 goes here
            break;
    }
    // Update successful
    return true;
}

/**
 * delete the subitems module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function subitems_delete()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['subitems_ddids']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBDropTable($xartable['subitems_ddobjects']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // Delete any module variables
    xarModDelVar('subitems', 'SupportShortURLs');

    // Remove module hooks
    if (!xarModUnregisterHook('item', 'create', 'API',
            'subitems', 'user', 'hook_item_create')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'delete', 'API',
            'subitems', 'user', 'hook_item_delete')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'update', 'API',
            'subitems', 'user', 'hook_item_update')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'modify', 'GUI',
            'subitems', 'user', 'hook_item_modify')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'new', 'GUI',
            'subitems', 'user', 'hook_item_new')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'display', 'GUI',
            'subitems', 'user', 'hook_item_display')) {
        return false;
    }

    // Remove Masks and Instances
    xarRemoveMasks('subitems');
    xarRemoveInstances('subitems');

    // Deletion successful
    return true;
}

?>
