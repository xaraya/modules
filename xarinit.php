<?php
/**
    Security - Provides unix style privileges to xaraya items.
 
    @copyright (C) 2003-2005 by Envision Net, Inc.
    @license GPL (http://www.gnu.org/licenses/gpl.html)
    @link http://www.envisionnet.net/
    @author Brian McGilligan <brian@envisionnet.net>
 
    @package Xaraya eXtensible Management System
    @subpackage Security module
*/

/*
    NOTE: We are using the adodb XML Schema package to manage the
    db tables. It makes it a bit easier to maintain and upgrades
    become a snap. But the bundled version that comes with adodb 
    does not work, there is a bug of some sorts in it. So I've patched
    it and put it in the base module for now. I really need to sent this
    upstream.
*/
//require_once( "xaradodb/adodb-xmlschema.inc.php" );
require_once( "modules/base/xarclass/adodb-xmlschema.inc.php" );

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
    /*
        When adodb XMLschema tries to detects the current schema and nothing exists
        xaraya exception are set. So we just want to get rid of them for now, till
        I can figure out a better solution like not having the exceptions set in the 
        first place
    */
    xarErrorFree();

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
    $schemaFile = 'modules/security/xardata/tables.xml';
    
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '0.1.0':
        case '0.1.1':
        case '0.5.0':
            $schema = new adoSchema( $dbconn );
            $schema->setPrefix( xarDBGetSiteTablePrefix() . '_' );
            $sql = $schema->ParseSchema( $schemaFile );
            $result = $schema->ExecuteSchema();  
            xarErrorFree();

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
    $schemaFile = 'modules/security/xardata/tables.xml';
    $schema = new adoSchema( $dbconn );
    $sql = $schema->RemoveSchema( $schemaFile );
    $result = $schema->ExecuteSchema();  
    
    // cleans up the module vars
    xarModDelAllVars('security');

    // Deletion successful
    return true;
}

?>
