<?php
/**
 * Security - Provides unix style privileges to xaraya items.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Security Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */
/**
    Initialize the module
*/
function security_init()
{
    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $prefix   =  xarDBGetSiteTablePrefix();

    /* Get a data dictionary object with all the item create methods in it */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    $sec_fields = "
		xar_modid      I NotNull DEFAULT 0,
		xar_itemtype   I NotNull DEFAULT 0,
		xar_itemid     I NotNull DEFAULT 0,
		xar_userlevel  I NotNull DEFAULT 0,
		xar_grouplevel I NotNull DEFAULT 0,
		xar_worldlevel I NotNull DEFAULT 0
    ";
    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($xartable['security'], $sec_fields);
    if (!$result) {return;}

    $result = $datadict->createIndex(
        "i_{$prefix}_security_combo",
        $xartable['security'],
        array('xar_modid', 'xar_itemtype', 'xar_itemid')
    );
    if (!$result) {return;}

	$sec_group_fields = "
        xar_modid    I NotNull DEFAULT 0,
        xar_itemtype I NotNull DEFAULT 0,
        xar_itemid   I NotNull DEFAULT 0,
        xar_gid      I NotNull DEFAULT 0,
        xar_level    I NotNull DEFAULT 0
    ";
    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($xartable['security_group_levels'], $sec_group_fields);
    if (!$result) {return;}

    $result = $datadict->createIndex(
        "i_{$prefix}_security_group_levels_combo",
        $xartable['security_group_levels'],
        array('xar_modid', 'xar_itemtype', 'xar_itemid')
    );
    if (!$result) {return;}

    /*
        Register all the modules hooks
    */
    if (!xarModRegisterHook('item', 'display', 'GUI',
            'security', 'admin', 'changesecurity')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'modify', 'GUI',
            'security', 'admin', 'changesecurity')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'create', 'API',
            'security', 'admin', 'createhook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'update', 'API',
            'security', 'admin', 'updatehook')) {
        return false;
    }

    /*
      Register the module components that are privileges objects Format is
      xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
    */
    xarRegisterMask('UseSecurity', 'All', 'security', 'All', 'All', 'ACCESS_READ');
    xarRegisterMask('AdminSecurity', 'All', 'security', 'All', 'All', 'ACCESS_ADMIN');

    // Initialisation successful
    return true;
}


/**
 * Upgrade the module from an old version
 */
function security_upgrade($oldversion)
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Upgrade dependent on old version number
    switch($oldversion) {
        case '0.1.0':
        case '0.1.1':
        case '0.5.0':
        case '0.8.0':
        case '0.8.1':

            break;

        default:
            // Couldn't find a previous version to upgrade
            return false;
    }

    // Update successful
    return true;
}


/**
 * Delete the module
 */
function security_delete()
{
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    /* Get a data dictionary object with item create and delete methods */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    /* Drop the security tables */
    $result = $datadict->dropTable($xartable['security']);
    if( !$result ){ return false; }
    $result = $datadict->dropTable($xartable['security_group_levels']);
    if( !$result ){ return false; }

    // cleans up the module vars
    xarModDelAllVars('security');

    /* Unregister each of the hooks that have been created */
    $result = xarModUnregisterHook('item', 'display', 'GUI', 'security', 'admin', 'changesecurity');
    if( !$result ){ return false; }

    $result = xarModUnregisterHook('item', 'modify', 'GUI', 'security', 'admin', 'changesecurity');
    if( !$result ){ return false; }

    $result = xarModUnregisterHook('item', 'create', 'API', 'security', 'admin', 'createhook');
    if( !$result ){ return false; }

    $result = xarModUnregisterHook('item', 'update', 'API', 'security', 'admin', 'updatehook');
    if( !$result ){ return false; }

    // Removes and privileges that may have been created
    xarRemoveMasks('security');
    xarRemoveInstances('security');

    // Deletion successful
    return true;
}

?>
