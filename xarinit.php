<?php

require( "modules/base/xarclass/adodb-xmlschema.inc.php" );

/**
 * Initialize the module
 */
function security_init()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $schemaFile = 'modules/security/xardata/tables.xml';
    $schema = new adoSchema( $dbconn );
    $schema->setPrefix( xarDBGetSiteTablePrefix() . '_' );
    $sql = $schema->ParseSchema( $schemaFile );
    $result = $schema->ExecuteSchema();  
    xarErrorFree();

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
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $schemaFile = 'modules/security/xardata/tables.xml';
    
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '0.1.0':
        case '0.1.1':
            // Code to upgrade from version 1.1.0 goes here
            $schema = new adoSchema( $dbconn );
            $schema->setPrefix( xarDBGetSiteTablePrefix() . '_' );
            $sql = $schema->ParseSchema( $schemaFile );
            $result = $schema->ExecuteSchema();  
            xarErrorFree();

            
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
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $schemaFile = 'modules/security/xardata/tables.xml';
    $schema = new adoSchema( $dbconn );
    $sql = $schema->RemoveSchema( $schemaFile );
    $result = $schema->ExecuteSchema();  
    
    //
    xarModDelAllVars('security');

    // Deletion successful
    return true;
}

?>
