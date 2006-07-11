<?php
/**
 * Update Configuration
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
 * Update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function authphpbb2_admin_updateconfig()
{
    // Get parameters
    if (!xarVarFetch('adduser',       'checkbox', $adduser,       false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaultgroup',  'str:1:',   $defaultgroup,  '',    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('activate',      'checkbox', $activate,      false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('server',        'str:1:',   $server,        '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dbtype',        'str:1:',   $dbtype,        '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('database',      'str:1:',   $database,      '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('username',      'str:1:',   $username,      '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('password',      'str:1:',   $password,      '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('prefix',        'str:1:',   $prefix,        '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('forumurl',      'str:1:',   $forumurl,      '', XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // update the data
    if(!$adduser) {
        xarModSetVar('authphpbb2', 'add_user', 'false');
    } else {
        xarModSetVar('authphpbb2', 'add_user', 'true');
    }

    // Get default users group
    if (!isset($defaultgroup)) {
        // See if Users role exists
        if( xarFindRole("Users"))
            $defaultgroup = 'Users';
    }
    // update config
    xarModSetVar('authphpbb2', 'defaultgroup', $defaultgroup);
    if ($dbtype == '')
        $dbtype = 'mysql';

    xarModSetVar('authphpbb2', 'server', $server);
    xarModSetVar('authphpbb2', 'dbtype', $dbtype);
    xarModSetVar('authphpbb2', 'database', $database);
    xarModSetVar('authphpbb2', 'username', $username);
    xarModSetVar('authphpbb2', 'password', $password);
    xarModSetVar('authphpbb2', 'prefix', $prefix);
    xarModSetVar('authphpbb2', 'forumurl', $forumurl);

    $authmodules = xarConfigGetVar('Site.User.AuthenticationModules');
    if (empty($activate) && in_array('authphpbb2', $authmodules)) {
        $newauth = array();
        foreach ($authmodules as $module) {
            if ($module != 'authphpbb2') {
                $newauth[] = $module;
            }
        }
        xarConfigSetVar('Site.User.AuthenticationModules', $newauth);
    } elseif (!empty($activate) && !in_array('authphpbb2', $authmodules)) {
        $newauth = array();
        foreach ($authmodules as $module) {
            if ($module == 'authsystem') {
                $newauth[] = 'authphpbb2';
            }
            $newauth[] = $module;
        }
        xarConfigSetVar('Site.User.AuthenticationModules', $newauth);
    }

    // and refresh display
    xarResponseRedirect(xarModURL('authphpbb2', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>