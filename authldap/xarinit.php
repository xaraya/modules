<?php
/**
 * File: $Id$
 *
 * AuthLDAP Initialisation
 *
 * @package authentication
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage authldap
 * @author Chris Dudley <miko@xaraya.com> | Richard Cave <rcave@xaraya.com>
*/

/**
 * Initialisation function
*/
function authldap_init()
{
    // Make sure the LDAP PHP extension is available
    if (!extension_loaded('ldap')) {
        $msg=xarML('Your PHP configuration does not seem to include the required LDAP extension. Please refer to http://www.php.net/manual/en/ref.ldap.php on how to install it.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,'MODULE_DEPENDENCY',
                        new SystemException($msg));
        return;
    }

    // Set up module variables
    xarModSetVar('authldap','add_user', 'true');
    xarModSetVar('authldap','add_user_uname', 'sn');
    xarModSetVar('authldap','add_user_email', 'mail');
    xarModSetVar('authldap','store_user_password', 'true');
    xarModSetVar('authldap','failover', 'true');

    // Define mask definitions for security checks
    xarRegisterMask('AdminAuthLDAP','All','authldap','All','All','ACCESS_ADMIN');
    xarRegisterMask('ReadAuthLDAP','All','authldap','All','All','ACCESS_READ');

    // Add authldap to Site.User.AuthenticationModules in xar_config_vars
    $authModules = xarConfigGetVar('Site.User.AuthenticationModules');
    $authModulesUpdate = array();

    // insert authldap right before authsystem
    foreach ($authModules as $authType) {
        if ($authType == 'authsystem') {
            $authModulesUpdate[] = 'authldap';
        }// if
        $authModulesUpdate[] = $authType;
    }// foreach

    // save the setting
    xarConfigSetVar('Site.User.AuthenticationModules',$authModulesUpdate);

    // Initialization successful
    return true;
}

/**
 * Module upgrade function
 *
 *
 */
function authldap_upgrade($oldVersion)
{
    switch($oldVersion) {
    case '1.0':
        // compatability upgrade
        break;
    }
    return true;
}

/**
 * module removal function
*/
function authldap_delete()
{
    // Remove module variables
    xarModDelVar('authldap','add_user');
    xarModDelVar('authldap','add_user_uname');
    xarModDelVar('authldap','add_user_email');
    xarModDelVar('authldap','store_user_password');
    xarModDelVar('authldap','failover');

    // Remove authldap to Site.User.AuthenticationModules in xar_config_vars
    $authModules = xarConfigGetVar('Site.User.AuthenticationModules');
    $authModulesUpdate = array();

    // Loop through current auth modules and remove 'authldap'
    foreach ($authModules as $authType) {
        if ($authType != 'authldap')
            $authModulesUpdate[] = $authType;
    }
    xarConfigSetVar('Site.User.AuthenticationModules',$authModulesUpdate);

    // Deletion successful
    return true;
}
?>
