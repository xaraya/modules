<?php
/**
 * File: $Id$
 *
 * AuthSSO Administrative Display Functions
 *
 * @package authentication
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authsso
 * @author Jonn Beames <jsb@xaraya.com> | Richard Cave <rcave@xaraya.com>
*/

/**
 * Update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function authsso_admin_updateconfig()
{
    // Get parameters

    list(
         $adduser,
         $addusermaildomain,
         $useldap,
         $ldapnamevalue,
         $ldapmailvalue,
         $defaultgroup
        ) = xarVarCleanFromInput(
                                 'adduser',
                                 'addusermaildomain',
                                 'useldap',
                                 'ldapnamevalue',
                                 'ldapmailvalue',
                                 'defaultgroup'
                                );

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // update the data
    if(!$adduser) {
        xarModSetVar('authsso', 'add_user', 'false');
    } else {
        xarModSetVar('authsso', 'add_user', 'true');
    }
    if(!$useldap) {
        xarModSetVar('authsso', 'getfromldap', 'false');
    } else {
        xarModSetVar('authsso', 'getfromldap', 'true');
    }
    if(isset($addusermaildomain)) {
        xarModSetVar('authsso', 'add_user_maildomain', $addusermaildomain);
    }
    if(isset($ldapnamevalue)) {
        xarModSetVar('authsso', 'ldapdisplayname', $ldapnamevalue);
    }
    if(isset($ldapmailvalue)) {
        xarModSetVar('authsso', 'ldapmail', $ldapmailvalue);
    }

    // Get default users group
    if (!isset($defaultgroup)) {
        // See if Users role exists
        if( xarFindRole("Users"))
            $defaultgroup = 'Users';
    }
    // update config
    xarModSetVar('authsso', 'defaultgroup', $defaultgroup);

    // and refresh display
    xarResponseRedirect(xarModURL('authsso', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>
