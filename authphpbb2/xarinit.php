<?php
/**
 * File: $Id$
 *
 * AuthphpBB2 Initialisation
 *
 */

/**
 * Initialisation function
*/
function authphpbb2_init()
{
    // ToDo: sanity check

    // ToDo: Set up module variables
    xarModSetVar('authphpbb2', 'add_user', 'true');
    xarModSetVar('authphpbb2', 'defaultgroup', 'Users');
    xarModSetVar('authphpbb2', 'server', 'localhost');
    xarModSetVar('authphpbb2', 'database', 'phpbb2');
    xarModSetVar('authphpbb2', 'username', 'root');
    xarModSetVar('authphpbb2', 'password', '');
    xarModSetVar('authphpbb2', 'prefix', 'phpbb_');
    xarModSetVar('authphpbb2','dbtype', 'mysql');
//    xarModSetVar('authphpbb2', 'forumroot', 'forum');

    // Define mask definitions for security checks
    xarRegisterMask('AdminAuthphpBB2', 'All', 'authphpbb2', 'All', 'All', 'ACCESS_ADMIN');
    xarRegisterMask('ReadAuthphpBB2', 'All', 'authphpbb2', 'All', 'All', 'ACCESS_READ');

    // Add authphpbb2 to Site.User.AuthenticationModules in xar_config_vars
    $authModules = xarConfigGetVar('Site.User.AuthenticationModules');
    $authModules[] = 'authphpbb2';

    // Sort array so authphpbb2 is before authsystem
    sort($authModules);

    xarConfigSetVar('Site.User.AuthenticationModules',$authModules);

    // Initialization successful
    return true;
}

/**
 * module removal function
*/
function authphpbb2_delete()
{
    // ToDo: remove config vars
    xarModDelVar('authphpbb2', 'add_user');
    xarModDelVar('authphpbb2', 'defaultgroup');
    xarModDelVar('authphpbb2', 'dbtype');
    xarModDelVar('authphpbb2', 'server');
    xarModDelVar('authphpbb2', 'database');
    xarModDelVar('authphpbb2', 'username');
    xarModDelVar('authphpbb2', 'password');
    xarModDelVar('authphpbb2', 'prefix');
//    xarModDelVar('authphpbb2', 'forumroot');

    // Remove authphpbb2 to Site.User.AuthenticationModules in xar_config_vars
    $authModules = xarConfigGetVar('Site.User.AuthenticationModules');
    $authModulesUpdate = array();

    // Loop through current auth modules and remove 'authphpbb2'
    foreach ($authModules as $authType) {
        if ($authType != 'authphpbb2')
            $authModulesUpdate[] = $authType;
    }
    xarConfigSetVar('Site.User.AuthenticationModules',$authModulesUpdate);

    // Deletion successful
    return true;
}
?>