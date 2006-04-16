<?php
/**
 * Owner - Tracks who creates xaraya based items.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Owner Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */
/**
 * Initialize the module
 */
function owner_init()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();


    $prefix   =  xarDBGetSiteTablePrefix();

    /* Get a data dictionary object with all the item create methods in it */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    $sec_fields = "
		xar_modid      I NotNull DEFAULT 0,
		xar_itemtype   I NotNull DEFAULT 0,
		xar_itemid     I NotNull DEFAULT 0,
		xar_uid        I NotNull DEFAULT 0
    ";
    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($xartable['owner'], $sec_fields);
    if (!$result) {return;}

    $result = $datadict->createIndex(
        "i_{$prefix}_owner_combo",
        $xartable['owner'],
        array('xar_modid', 'xar_itemtype', 'xar_itemid')
    );
    if (!$result) {return;}

    // Set up module hooks
    if (!xarModRegisterHook('item', 'display', 'GUI',
            'owner', 'admin', 'changeowner')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'modify', 'GUI',
            'owner', 'admin', 'changeowner')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'create', 'API',
            'owner', 'admin', 'createhook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'update', 'API',
            'owner', 'admin', 'updatehook')) {
        return false;
    }

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
    */
    xarRegisterMask('ViewOwner', 'All', 'owner', 'All', 'All', 'ACCESS_READ');
    xarRegisterMask('ChangeOwner', 'All', 'owner', 'All', 'All', 'ACCESS_ADMIN');

    // Initialisation successful
    return true;
}


/**
 * Upgrade the module from an old version
 */
function owner_upgrade($oldversion)
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Upgrade dependent on old version number
    switch($oldversion)
    {
        case '0.1.0':
            // fall through to the next upgrade

        case '0.5.0':
        case '0.5.1':
        case '0.5.2':
        case '0.5.3':
        case '0.5.4':
        case '0.5.5':
        case '0.6.0':

            break;

        default:
            // Couldn't find a previous version to upgrade
            return;
    }

    // Update successful
    return true;
}


/**
 * Delete the module
 */
function owner_delete()
{
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    /* Get a data dictionary object with item create and delete methods */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    /* Drop the security tables */
    $result = $datadict->dropTable($xartable['owner']);
    if( !$result ){ return false; }

    //
    xarModDelAllVars('owner');

    if (!xarModUnregisterHook('item', 'display', 'GUI',
            'owner', 'admin', 'changeowner')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'modify', 'GUI',
            'owner', 'admin', 'changeowner')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'create', 'API',
            'owner', 'admin', 'createhook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'update', 'API',
            'owner', 'admin', 'updatehook')) {
        return false;
    }

    // Removes and privileges that may have been created
    xarRemoveMasks('owner');
    xarRemoveInstances('owner');

    // Deletion successful
    return true;
}

?>
