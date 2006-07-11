<?php
/**
 * Modify Configuration
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
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function authphpbb2_admin_modifyconfig()
{
    // Security check
    if(!xarSecurityCheck('AdminAuthphpBB2')) return;

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    
    // prepare labels and values for display by the template
    $data['server'] = xarVarPrepForDisplay(xarModGetVar('authphpbb2','server'));
    $data['dbtype'] = xarVarPrepForDisplay(xarModGetVar('authphpbb2','dbtype'));
    if ($data['dbtype'] == '')
        $data['dbtype'] = 'mysql';
    $data['database'] = xarVarPrepForDisplay(xarModGetVar('authphpbb2','database'));
    $data['username'] = xarVarPrepForDisplay(xarModGetVar('authphpbb2','username'));
    $data['password'] = xarVarPrepForDisplay(xarModGetVar('authphpbb2','password'));
    $data['prefix'] = xarVarPrepForDisplay(xarModGetVar('authphpbb2','prefix'));
//    $data['forumroot'] = xarVarPrepForDisplay(xarModGetVar('authphpbb2','forumroot'));
    
    // prepare labels and values for display by the template
    $data['title'] = xarVarPrepForDisplay(xarML('Administration'));
    $data['configadmin'] = xarVarPrepForDisplay(xarML('Configure AuthphpBB2'));
    $data['adduser'] = xarVarPrepForDisplay(xarML('Add phpBB2 User to Xaraya Database on Login'));
    $data['adduserhelp'] = xarVarPrepForDisplay(xarML('If a user has been authenticated by the phpBB2 but does not have a login account to Xaraya, the user will not be able to login. By selecting this option, a user that has been authenticated by the phpBB2 will be automatically added to the Xaraya database and allowed to login.'));
    if (xarModGetVar('authphpbb2', 'add_user') == 'true') {
        $data['adduservalue'] = 'checked';
    } else {
        $data['adduservalue'] = '';
    }
    $data['submitbutton'] = xarVarPrepForDisplay(xarML('Update AuthphpBB2 Configuration'));

    // Get groups
    $data['defaultgrouplabel'] = xarVarPrepForDisplay(xarML('Default Group'));
    $data['defaultgroup'] = xarModGetVar('authphpbb2', 'defaultgroup');

    // Get default users group
    if (!isset($data['defaultgroup'])) {
        // See if Users role exists
        if ( xarFindRole("Users"))
            $data['defaultgroup'] = 'Users';
    }

    // Get the list of groups
    if (!$groupRoles = xarGetGroups()) return; // throw back

    $i=0;
    while (list($key,$group) = each($groupRoles)) {
        $groups[$i]['name'] = xarVarPrepForDisplay($group['name']);
        $i++;
    }
    $data['groups'] = $groups;

    $data['forumurl'] = xarVarPrepForDisplay(xarModGetVar('authphpbb2','forumurl'));

    // Send the values to the template
    return $data;
}

?>