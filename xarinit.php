<?php
/**
 * AuthSQL Initialisation
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AuthSQL Module
 * @link http://xaraya.com/index.php/release/10512.html
 * @author Roger Keays and James Cooper
*/

/**
 * Initialisation function
*/
function authsql_init()
{
    // Set up module variables
    xarModSetVar('authsql', 'sqlhost', 'localhost');

    xarModSetVar('authsql', 'sqldbport', '');
    xarModSetVar('authsql', 'sqldbtype', '');
    xarModSetVar('authsql', 'sqldbname', '');
    xarModSetVar('authsql', 'sqldbuser', '');
    xarModSetVar('authsql', 'sqldbpass', '');
    xarModSetVar('authsql', 'sqldbpasswordtablename', '');
    xarModSetVar('authsql', 'sqldbusernamefield', '');
    xarModSetVar('authsql', 'sqldbpasswordfield', '');
    xarModSetVar('authsql', 'sqldbpasswordencryptionmethod', '');

    xarModSetVar('authsql','add_user', 'true');
    xarModSetVar('authsql','store_user_password', 'true');

    // Define mask definitions for security checks
    xarRegisterMask('AdminAuthSQL','All','authsql','All','All','ACCESS_ADMIN');
    xarRegisterMask('ReadAuthSQL','All','authsql','All','All','ACCESS_READ');

    // Do not add authsql to Site.User.AuthenticationModules in xar_config_vars here
/*
    $authModules = xarConfigGetVar('Site.User.AuthenticationModules');
    $authModules[] = 'authsql';

    // Sort array so authsql is before authsystem
    sort($authModules);

    xarConfigSetVar('Site.User.AuthenticationModules',$authModules);
*/

    /* call upgrade */
    return upgrade(1.0);
}


/**
 * upgrade module
 */
function upgrade($oldver) 
{
    switch ($oldver) {
        case 1.0:

            /* added 'where' field */
            xarModSetVar('authsql', 'sqlwhere', '');
        default:
            break;
    }
    return true;
} /* upgrade */


/**
 * module removal function
 */
function authsql_delete()
{
    // Remove module variables
    xarModDelAllVars('authsql');

    // Remove authsql to Site.User.AuthenticationModules in xar_config_vars
    $authModules = xarConfigGetVar('Site.User.AuthenticationModules');
    $authModulesUpdate = array();

    // Loop through current auth modules and remove 'authsql'
    foreach ($authModules as $authType) 
    {
        if ($authType != 'authsql') 
        {
            $authModulesUpdate[] = $authType;
        }
    }
    xarConfigSetVar('Site.User.AuthenticationModules',$authModulesUpdate);

    // Deletion successful
    return true;
}
?>