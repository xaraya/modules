<?php
/**
 * File: $Id$
 *
 * AuthphpBB2 Administrative Display Functions
 *
 */

/**
 * Update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function authphpbb2_admin_updateconfig()
{
    // Get parameters

    list(
         $adduser,
         $defaultgroup,
         $server,
         $dbtype,
         $database,
         $username,
         $password,
         $prefix,
         $forumurl,
         $activate
        ) = xarVarCleanFromInput(
                                 'adduser',
                                 'defaultgroup',
                                 'server',
                                 'dbtype',
                                 'database', 
                                 'username', 
                                 'password', 
                                 'prefix',
                                 'forumurl',
                                 'activate'
                                );

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
    } elseif (!in_array('authphpbb2', $authmodules)) {
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
