<?php
/**
 * File: $Id: xarinit.php,v 1.3 2003/12/17 05:38:34 roger Exp $
 *
 * AuthSQL Initialisation
 *
 * @copyright (C) 2003 ninthave
 * @author James Cooper jbt_cooper@bigpond.com
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

    // Add authsql to Site.User.AuthenticationModules in xar_config_vars
    $authModules = xarConfigGetVar('Site.User.AuthenticationModules');
    $authModules[] = 'authsql';

    // Sort array so authsql is before authsystem
    sort($authModules);

    xarConfigSetVar('Site.User.AuthenticationModules',$authModules);

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
