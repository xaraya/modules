<?php
/**
 * AuthURL Module Init/Remove Functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AuthURL
 * @link http://xaraya.com/index.php/release/42241.html
 * @author Court Shrock <shrockc@inhs.org>
 */

/**
 * Initialization function
*/
function authurl_init()
{
    # Make sure the CURL PHP extension is available
    if (!extension_loaded('curl')) {
        $msg=xarML('Your PHP configuration does not seem to include the required CURL extension. Please refer to http://www.php.net/manual/en/ref.curl.php on how to install it.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION,'MODULE_DEPENDENCY',
                        new SystemException($msg));
        return;
    }// if

    # Set up module variables
    xarModSetVar('authurl','add_user', 'true');
    xarModSetVar('authurl','auth_url', 'http://someplace.net/login/');
    if (xarFindRole('Users')) {
        xarModSetVar('authurl','default_group', 'Users');
    }// if
    xarModSetVar('authurl','debug_level', 0);

    # Define mask definitions for security checks
    xarRegisterMask('AdminAuthURL','All','authurl','All','All','ACCESS_ADMIN');
    xarRegisterMask('ReadAuthURL','All','authurl','All','All','ACCESS_READ');

    # Do not add authurl to Site.User.AuthenticationModules in xar_config_vars here
/*
    $authModules = array_flip(xarConfigGetVar('Site.User.AuthenticationModules'));

    # insert authurl right before authsystem
    $authModules['authurl'] = $authModules['authsystem']++;
    $authModules = array_flip($authModules);

    ksort($authModules);
    # save the setting
    xarConfigSetVar('Site.User.AuthenticationModules',$authModules);
*/

    # Initialization successful
    return true;
}

/**
 * module upgrade function
 *
 */
function authurl_upgrade($oldVersion)
{
    switch($oldVersion) {
    case '1.0':
        // compatability upgrade, nothing to be done
        break;
    }
    return true;
}

/**
 * module removal function
*/
function authurl_delete()
{
    # Remove module variables
    xarModDelVar('authurl','add_user');
    xarModDelVar('authurl','auth_url');
    xarModDelVar('authurl','default_group');
    xarModDelVar('authurl','debug_level');

    # Remove authurl to Site.User.AuthenticationModules in xar_config_vars
    $authModules = xarConfigGetVar('Site.User.AuthenticationModules');
    $authModulesUpdate = array();

    # Remove masks
    xarRemoveMasks('authurl');

    # Loop through current auth modules and remove 'authurl'
    foreach ($authModules as $authType) {
        if ($authType != 'authurl')
            $authModulesUpdate[] = $authType;
    }// foreach
    xarConfigSetVar('Site.User.AuthenticationModules',$authModulesUpdate);

    # Deletion successful
    return true;
}
?>
