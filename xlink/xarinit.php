<?php
/**
 * File: $Id$
 *
 * CrossLink initialization functions
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage xlink
 * @author mikespub
 */

/**
 * initialise the xlink module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function xlink_init()
{
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xlinktable = $xartable['xlink'];

    xarDBLoadTableMaintenanceAPI();
    $query = xarDBCreateTable($xartable['xlink'],
                             array('xar_id'         => array('type'        => 'integer',
                                                            'null'       => false,
                                                            'increment'  => true,
                                                            'primary_key' => true),
                                   'xar_basename'   => array('type'        => 'varchar',
                                                            'size'        => 40,
                                                            'null'        => false,
                                                            'default'     => ''),
                                   'xar_refid'      => array('type'        => 'varchar',
                                                            'size'        => 254,
                                                            'null'        => false,
                                                            'default'     => ''),
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
                                  ));

    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // allow only one entry for the same base + reference id
    $index = array(
        'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_xlink_combo1',
        'fields'    => array('xar_basename','xar_refid'),
        'unique'    => true
    );
    $query = xarDBCreateIndex($xlinktable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // allow several references to the same module item (for now)
    $index = array(
        'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_xlink_combo2',
        'fields'    => array('xar_moduleid','xar_itemtype','xar_itemid'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($xlinktable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    xarModSetVar('xlink', 'SupportShortURLs', 1);

// base names
    xarModSetVar('xlink', 'default', ',kbase,products,wiki');

    if (!xarModRegisterHook('item', 'new', 'GUI',
                           'xlink', 'admin', 'newhook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'create', 'API',
                           'xlink', 'admin', 'createhook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'modify', 'GUI',
                           'xlink', 'admin', 'modifyhook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'update', 'API',
                           'xlink', 'admin', 'updatehook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'delete', 'API',
                           'xlink', 'admin', 'deletehook')) {
        return false;
    }
    if (!xarModRegisterHook('module', 'remove', 'API',
                           'xlink', 'admin', 'removehook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'display', 'GUI',
                           'xlink', 'user', 'displayhook')) {
        return false;
    }

/* // TODO: show items you created/edited someday ?
    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
            'xlink', 'user', 'usermenu')) {
        return false;
    }
*/

    $instances = array(
                       array('header' => 'external', // this keyword indicates an external "wizard"
                             'query'  => xarModURL('xlink', 'admin', 'privileges'),
                             'limit'  => 0
                            )
                    );
    xarDefineInstance('xlink', 'Item', $instances);

// TODO: tweak this - allow viewing xlink of "your own items" someday ?
    xarRegisterMask('ReadXLink', 'All', 'xlink', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('AdminXLink', 'All', 'xlink', 'Item', 'All:All:All', 'ACCESS_ADMIN');

    // create the dynamic object that will represent our items
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/xlink/xlink.xml'));
    if (empty($objectid)) return;
    // save the object id for later
    xarModSetVar('xlink','objectid',$objectid);

    // Initialisation successful
    return true;
}

/**
 * upgrade the xlink module from an old version
 * This function can be called multiple times
 */
function xlink_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case 1.0:
            // Code to upgrade from version 1.0 goes here
// Warning: this deletes all previous settings & entries !
            if (!xlink_delete()) return;
            if (!xlink_init()) return;
            xarModSetVar('xlink', 'SupportShortURLs', 1);
            break;
        case '1.1':

        case 2.0:
            // Code to upgrade from version 2.0 goes here
            break;
    }
    // Update successful
    return true;
}

/**
 * delete the xlink module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function xlink_delete()
{
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['xlink']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // Delete any module variables
    xarModDelVar('xlink', 'SupportShortURLs'); 

    // Remove module hooks
    if (!xarModUnregisterHook('item', 'new', 'GUI',
                           'xlink', 'admin', 'newhook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'create', 'API',
                           'xlink', 'admin', 'createhook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'modify', 'GUI',
                           'xlink', 'admin', 'modifyhook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'update', 'API',
                           'xlink', 'admin', 'updatehook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'delete', 'API',
                           'xlink', 'admin', 'deletehook')) {
        return false;
    }
    // when a whole module is removed, e.g. via the modules admin screen
    // (set object ID to the module name !)
    if (!xarModUnregisterHook('module', 'remove', 'API',
                           'xlink', 'admin', 'removehook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'display', 'GUI',
                           'xlink', 'user', 'displayhook')) {
        return false;
    }
/* // TODO: show items you created/edited someday ?
    if (!xarModUnregisterHook('item', 'usermenu', 'GUI',
            'xlink', 'user', 'usermenu')) {
        return false;
    } 
*/

    // Remove Masks and Instances
    xarRemoveMasks('xlink');
    xarRemoveInstances('xlink'); 

    // delete the dynamic object and its properties
    $objectid = xarModGetVar('xlink','objectid');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',
                      array('objectid' => $objectid));
        xarModDelVar('xlink','objectid');
    }

    // Deletion successful
    return true;
} 

?>
