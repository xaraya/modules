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
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function authldap_admin_modifyconfig()
{
    // Security check
    if(!xarSecurityCheck('AdminAuthLDAP')) return;
    
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    
    // prepare labels and values for display by the template
    $data['title'] = xarVarPrepForDisplay(xarML('Administration'));
    $data['configadmin'] = xarVarPrepForDisplay(xarML('Configure AuthLDAP'));

    $data['adduser'] = xarVarPrepForDisplay(xarML('Add LDAP User to Xaraya Database on Login'));
    if (xarModGetVar('authldap','add_user') == 'true') {    
        $data['adduservalue'] = xarVarPrepForDisplay("checked");
    } else {
        $data['adduservalue'] = "";
    }
    $data['adduseruname'] = xarVarPrepForDisplay(xarML('LDAP Username Attribute Name'));
    $data['adduserunamevalue'] = xarVarPrepForDisplay(xarModGetVar('authldap','add_user_uname'));
    $data['adduseremail'] = xarVarPrepForDisplay(xarML('LDAP Email Attribute Name'));
    $data['adduseremailvalue'] = xarVarPrepForDisplay(xarModGetVar('authldap','add_user_email'));
        

    // Get groups
    $data['defaultgrouplabel'] = xarVarPrepForDisplay(xarML('Default Group'));
    $data['defaultgroup'] = xarModGetVar('authldap', 'defaultgroup');

    // Get default users group
    if (!isset($data['defaultgroup'])) {
        // See if Users role exists
        if( xarFindRole("Users"))
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

    // Submit button
    $data['submitlabel'] = xarVarPrepForDisplay(xarML('Click "Submit" to change configuration:'));
    $data['submitbutton'] = xarVarPrepForDisplay(xarML('Submit'));
       
    // everything else happens in Template for now
    return $data;
}

?>
