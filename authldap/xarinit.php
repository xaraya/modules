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
    // Make sure that LDAP is available before trying to connect
    if (!extension_loaded('ldap')) {
        $msg=xarML('Your PHP configuration does not seem to include the required LDAP extension. Please refer to http://www.php.net/manual/en/ref.ldap.php on how to install it.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,'MODULE_DEPENDENCY',
                        new SystemException($msg));
        return;
    }

    // Set up module variables
    xarModSetVar('authldap','server', '127.0.0.1');
    xarModSetVar('authldap','port_number', '389');
    xarModSetVar('authldap','bind_dn','o=dept');
    xarModSetVar('authldap','uid_field', 'cn');
    xarModSetVar('authldap','search_user_dn', 'true');
    xarModSetVar('authldap','admin_login', '');
    xarModSetVar('authldap','admin_password', '');
    xarModSetVar('authldap','anonymous_bind', 'true');
    xarModSetVar('authldap','add_user', 'true');
    xarModSetVar('authldap','add_user_uname', 'sn');
    xarModSetVar('authldap','add_user_email', 'mail');

    // Define mask definitions for security checks
    xarRegisterMask('AdminAuthLDAP','All','authldap','All','All','ACCESS_ADMIN');
    xarRegisterMask('ReadAuthLDAP','All','authldap','All','All','ACCESS_READ');

    // Add authldap to Site.User.AuthenticationModules in xar_config_vars
    $authModules = xarConfigGetVar('Site.User.AuthenticationModules');
    $authModules[] = 'authldap';

    // Sort array so authldap is before authsystem
    sort($authModules);

    xarConfigSetVar('Site.User.AuthenticationModules',$authModules);

    // Initialization successful
    return true;
}

/**
 * module removal function
*/
function authldap_delete()
{
    xarModDelVar('authldap','server');
    xarModDelVar('authldap','port_number');
    xarModDelVar('authldap','bind_dn');
    xarModDelVar('authldap','uid_field');
    xarModDelVar('authldap','search_user_dn');
    xarModDelVar('authldap','admin_login');
    xarModDelVar('authldap','admin_password');
    xarModDelVar('authldap','anonymous_bind');
    xarModDelVar('authldap','add_user');
    xarModDelVar('authldap','add_user_uname');
    xarModDelVar('authldap','add_user_email');

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
