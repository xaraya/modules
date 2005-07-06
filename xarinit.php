<?php

/**
 * Initialize the module
 */
function security_init()
{
    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $dict =& xarDBNewDataDict($dbconn);
    $pre = xarDBGetSiteTablePrefix();

    $table = "
        xar_modid I NOTNULL,
        xar_itemtype I,
        xar_itemid I,
        xar_userlevel I,
        xar_grouplevel I,
        xar_worldlevel I
    ";
    $result = $dict->createTable($xartable['security'], $table);
    if( !$result ) return false;
    
    $table = "
        xar_modid I NOTNULL,
        xar_itemtype I,
        xar_itemid I,
        xar_gid I,
        xar_level I
    ";
    $result = $dict->createTable($xartable['security_group_levels'], $table);
    if( !$result ) return false;
    
    // Set up module hooks
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

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
    */
    xarRegisterMask('UseSecurity', 'All', 'security', 'All', 'All', 'ACCESS_READ');
    xarRegisterMask('AdminSecurity', 'All', 'security', 'All', 'All', 'ACCESS_ADMIN');
    //xarRegisterMask('ChangeOwner', 'All', 'owner', 'All', 'All', 'ACCESS_ADMIN');
    
    // Initialisation successful
    return true;
}


/**
 * Upgrade the module from an old version
 */
function security_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '0.1.0':
            // fall through to the next upgrade
            // Get database information
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $dict =& xarDBNewDataDict($dbconn);
            $pre = xarDBGetSiteTablePrefix();
            
            $table = "
                xar_modid I NOTNULL,
                xar_itemtype I,
                xar_itemid I,
                xar_gid I,
                xar_level I
            ";
            $result = $dict->createTable($xartable['security_group_levels'], $table);
            if( !$result ) return false;

        case '1.1.0':
            // Code to upgrade from version 1.1.0 goes here
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
function security_delete()
{
    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $dict =& xarDBNewDataDict($dbconn);
    $pre = xarDBGetSiteTablePrefix();

    $dict->dropTable($xartable['security']);
    
    //
    xarModDelAllVars('security');

    // Deletion successful
    return true;
}

?>
