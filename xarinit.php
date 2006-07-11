<?php
/**
 * AuthSSO Initialisation
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AuthSSO
 * @link http://xaraya.com/index.php/release/51.html
 * @author Jonn Beames <jsb@xaraya.com> | Richard Cave <rcave@xaraya.com>
*/

/**
 * Initialisation function
*/
function authsso_init()
{
    // ToDo: sanity check

    // ToDo: Set up module variables
    xarModSetVar('authsso', 'add_user', '1');
    xarModSetVar('authsso', 'add_user_maildomain', 'example.com');
    xarModSetVar('authsso', 'defaultgroup', 'Users');
    xarModSetVar('authsso', 'getfromldap', '0');
    xarModSetVar('authsso', 'ldapdisplayname', 'displayname');
    xarModSetVar('authsso', 'ldapmail', 'mail');
    if (xarFindRole("Users")) {
        xarModSetVar('authsso', 'defaultgroup', 'Users');
    }

    // Define mask definitions for security checks
    xarRegisterMask('AdminAuthSSO', 'All', 'authsso', 'All', 'All', 'ACCESS_ADMIN');
    xarRegisterMask('ReadAuthSSO', 'All', 'authsso', 'All', 'All', 'ACCESS_READ');

    // Do not add authsso to Site.User.AuthenticationModules in xar_config_vars here
/*
    $authModules = xarConfigGetVar('Site.User.AuthenticationModules');
    $authModules[] = 'authsso';

    // Sort array so authsso is before authsystem
    sort($authModules);

    xarConfigSetVar('Site.User.AuthenticationModules',$authModules);
*/

    // Initialization successful
    return true;
}

/**
 * module upgrade function
 *
 */
function authsso_upgrade($oldVersion)
{
    switch($oldVersion) {
    case '0.2':
        // compatability upgrade, nothing to be done
        break;
    }
    return true;
}

/**
 * module removal function
*/
function authsso_delete()
{
    // ToDo: remove config vars
    xarModDelVar('authsso', 'add_user');
    xarModDelVar('authsso', 'add_user_maildomain');
    xarModDelVar('authsso', 'defaultgroup');
    xarModDelVar('authsso', 'getfromldap');
    xarModDelVar('authsso', 'ldapdisplayname');
    xarModDelVar('authsso', 'ldapmail');

    // Remove authsso to Site.User.AuthenticationModules in xar_config_vars
    $authModules = xarConfigGetVar('Site.User.AuthenticationModules');
    $authModulesUpdate = array();

    // Loop through current auth modules and remove 'authsso'
    foreach ($authModules as $authType) {
        if ($authType != 'authsso')
            $authModulesUpdate[] = $authType;
    }
    xarConfigSetVar('Site.User.AuthenticationModules',$authModulesUpdate);

    // Deletion successful
    return true;
}
?>