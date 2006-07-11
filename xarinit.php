<?php
/**
 * AuthphpBB2 Initialisation
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authldap
 * @link http://xaraya.com/index.php/release/77102.html
 * @author Alexander GQ Gerasiov <gq@gq.pp.ru>
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
    xarModSetVar('authphpbb2', 'dbtype', 'mysql');

    xarModSetVar('authphpbb2', 'forumurl', 'http://my.domain/forum');

    // Register blocks
    if (!xarModAPIFunc('blocks', 'admin', 'register_block_type', array('modName' => 'authphpbb2', 'blockType' => 'phpbb2login')))
        return;

    // Define mask definitions for security checks
    xarRegisterMask('AdminAuthphpBB2', 'All', 'authphpbb2', 'All', 'All', 'ACCESS_ADMIN');
    xarRegisterMask('ReadAuthphpBB2', 'All', 'authphpbb2', 'All', 'All', 'ACCESS_READ');

    // Do not add authphpbb2 to Site.User.AuthenticationModules in xar_config_vars here
/*
    $authModules = xarConfigGetVar('Site.User.AuthenticationModules');
    $authModules[] = 'authphpbb2';

    // Sort array so authphpbb2 is before authsystem
    sort($authModules);

    xarConfigSetVar('Site.User.AuthenticationModules',$authModules);
*/

    // Initialization successful
    return true;
}

/**
 * Upgrade the users module from an old version
 */
function authphpbb2_upgrade($oldVersion)
{
    // Upgrade dependent on old version number
    switch ($oldVersion) {
        case '0.0.1':

            // Register blocks
            xarModAPIFunc('blocks', 'admin', 'register_block_type', array('modName' => 'authphpbb2', 'blockType' => 'phpbb2login'));
            xarModSetVar('authphpbb2', 'forumurl', 'http://my.domain/forum');
        
            break;
        case '0.0.2':
            break;
        case '0.0.3': //current version
            //version upgrade to signify changed code but no db changes
            break;
        case 2.0:
            // Code to upgrade from version 2.0 goes here
            break;
    }
    // Update successful
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
    xarModDelVar('authphpbb2', 'forumurl');

    // Register blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'authphpbb2',
                             'blockType'=> 'phpbb2login'))) return;

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