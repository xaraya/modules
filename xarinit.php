<?php
/**
 * File: $Id$
 *
 * Invision Power Board Authentication
 *
 * @package authentication
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage authinvision2
 * @author Brian McCloskey <brian@nexusden.com>
*/

/**
 * Initialisation function
*/
function authinvision2_init()
{
    // Set up module variables
    xarModSetVar('authinvision2','server', 'localhost');
    xarModSetVar('authinvision2','database', '');
    xarModSetVar('authinvision2','username','');
    xarModSetVar('authinvision2','password', '');
    xarModSetVar('authinvision2','prefix','ibf');
    xarModSetVar('authinvision2','defaultgroup','Users');
    xarModSetVar('authinvision2','forumroot','forum');
    xarModSetVar('authinvision2','onlyauth','0');

    // Register blocks
    if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
            array('modName' => 'authinvision2', 'blockType' => 'login'))) return;

    // Define mask definitions for security checks
    xarRegisterMask('Adminauthinvision2','All','authinvision2','All','All','ACCESS_ADMIN');
    xarRegisterMask('Readauthinvision2','All','authinvision2','All','All','ACCESS_READ');

    // Add authinvision2 to Site.User.AuthenticationModules in xar_config_vars
    $authModules = xarConfigGetVar('Site.User.AuthenticationModules');
    $authModules[] = 'authinvision2';

    // Sort array so authinvision2 is before authsystem
    sort($authModules);

    xarConfigSetVar('Site.User.AuthenticationModules',$authModules);

    // Initialization successful
    return true;
}

/**
 * Module upgrade function
 *
 */
function authinvision2_upgrade($oldVersion)
{
    switch($oldVersion) {
    case '1.0':
        // compatability upgrade, nothing to be done
        break;
    case '1.1':
        // No upgrading required
        break;
    case '1.2':
        // No upgrading required
        break;
    case '1.3':
        // No upgrading required
        break;
    case '1.3.1':
        // No upgrading required
        break;
    }
    return true;
}
/**
 * module removal function
*/
function authinvision2_delete()
{
    xarModDelVar('authinvision2','server');
    xarModDelVar('authinvision2','database');
    xarModDelVar('authinvision2','username');
    xarModDelVar('authinvision2','password');
    xarModDelVar('authinvision2','prefix');
    xarModDelVar('authinvision2','defaultgroup');
    xarModDelVar('authinvision2','forumroot');
    xarModDelVar('authinvision2','onlyauth');

    // Remove authinvision2 to Site.User.AuthenticationModules in xar_config_vars
    $authModules = xarConfigGetVar('Site.User.AuthenticationModules');
    $authModulesUpdate = array();

    // Loop through current auth modules and remove 'authinvision2'
    foreach ($authModules as $authType) {
        if ($authType != 'authinvision2')
            $authModulesUpdate[] = $authType;
    }

    if (empty($authModulesUpdate)) {
        $authModulesUpdate[] = 'authsystem';
    }
    xarConfigSetVar('Site.User.AuthenticationModules',$authModulesUpdate);
    // Unregister blocks
    if (!xarModAPIFunc('blocks', 'admin', 'unregister_block_type',
            array('modName' => 'authinvision2', 'blockType' => 'login'))) return;

    // Deletion successful
    return true;
}
?>
