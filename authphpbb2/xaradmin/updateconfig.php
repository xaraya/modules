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
         $prefix
//         $forumroot
        ) = xarVarCleanFromInput(
                                 'adduser',
                                 'defaultgroup',
                                 'server',
                                 'dbtype',
                                 'database', 
                                 'username', 
                                 'password', 
                                 'prefix'
//                                 'forumroot'
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
//    xarModSetVar('authphpbb2', 'forumroot', $forumroot);

    // and refresh display
    xarResponseRedirect(xarModURL('authphpbb2', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>