<?php
/**
 * File: $Id$
 *
 * AuthLDAP Administrative Display Functions
 * 
 * @package authentication
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authldap
 * @author Chris Dudley <miko@xaraya.com> | Richard Cave <rcave@xaraya.com>
*/

/**
 * Update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function authldap_admin_updateconfig()
{
    // Get parameters
    
    list($adduser,
         $adduseruname,
         $adduseremail,
         $defaultgroup ) = xarVarCleanFromInput('adduser', 
                                                'adduseruname', 
                                                'adduseremail', 
                                                'defaultgroup');

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    if(!$adduser){
        xarModSetVar('authldap', 'add_user', 'false');
    } else {
        xarModSetVar('authldap', 'add_user', 'true');
    }
    xarModSetVar('authldap', 'add_user_uname', $adduseruname);
    xarModSetVar('authldap', 'add_user_email', $adduseremail);

    // Get default users group
    if (!isset($defaultgroup)) {
        // See if Users role exists
        if( xarFindRole("Users"))
            $defaultgroup = 'Users';
    } 
    xarModSetVar('authldap', 'defaultgroup', $defaultgroup);

    // lets update status and display updated configuration
    xarResponseRedirect(xarModURL('authldap', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>
