<?php
/**
 * File: $Id$
 *
 * AuthSSO Initialisation
 *
 * @package authentication
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage authsso
 * @author Jonn Beames <jsb@xaraya.com> | Richard Cave <rcave@xaraya.com>
*/

/**
 * Initialisation function
*/
function authsso_init()
{
    // ToDo: sanity check

    // ToDo: Set up module variables
    xarModSetVar('authsso', 'add_user', 'true');
    xarModSetVar('authsso', 'add_user_maildomain', 'example.com');
    xarModSetVar('authsso', 'defaultgroup', 'Users');
    xarModSetVar('authsso', 'getfromldap', 'false');
    xarModSetVar('authsso', 'ldapdisplayname', 'displayname');
    xarModSetVar('authsso', 'ldapmail', 'mail');

    // Define mask definitions for security checks
    xarRegisterMask('AdminAuthSSO', 'All', 'authsso', 'All', 'All', 'ACCESS_ADMIN');
    xarRegisterMask('ReadAuthSSO', 'All', 'authsso', 'All', 'All', 'ACCESS_READ');

    // Add authsso to Site.User.AuthenticationModules in xar_config_vars
    $authModules = xarConfigGetVar('Site.User.AuthenticationModules');
    $authModules[] = 'authsso';

    // Sort array so authsso is before authsystem
    sort($authModules);

    xarConfigSetVar('Site.User.AuthenticationModules',$authModules);

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
