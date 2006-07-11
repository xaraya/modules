<?php
/**
 * AuthSSO Administrative Display Functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AuthSSO
 * @link http://xaraya.com/index.php/release/51.html
 * @author Jonn Beames <jsb@xaraya.com> | Richard Cave <rcave@xaraya.com>
*/

/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function authsso_admin_modifyconfig()
{
    // Security check
    if(!xarSecurityCheck('AdminAuthSSO')) return;

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // prepare labels and values for display by the template
    $data['title'] = xarVarPrepForDisplay(xarML('Administration'));
    $data['configadmin'] = xarVarPrepForDisplay(xarML('Configure AuthSSO'));
    $data['adduser'] = xarVarPrepForDisplay(xarML('Add SSO User to Xaraya Database on Login'));
    $data['adduserhelp'] = xarVarPrepForDisplay(xarML('If a user has been authenticated by the web server but does not have a login account to Xaraya, the user will not be able to login. By selecting this option, a user that has been authenticated by the web server will be automatically added to the Xaraya database and allowed to login.'));
    if (xarModGetVar('authsso', 'add_user')) {
        $data['adduservalue'] = 'checked';
    } else {
        $data['adduservalue'] = '';
    }
    $data['useldap'] = xarVarPrepForDisplay(xarML('Use LDAP to Retrieve User Information'));
    $data['useldaphelp'] = xarVarPrepForDisplay(xarML('User information can be retrieved from an LDAP server via the xarLDAP module. Be sure that xarLDAP is configured appropriately.'));
    if (xarModGetVar('authsso','getfromldap')) {
        $data['useldapvalue'] = 'checked';
    } else {
        $data['useldapvalue'] = '';
    }
    $data['maildomain'] = xarVarPrepForDisplay(xarML('Mail Domain of Added User'));
    $data['mailvalue'] = xarVarPrepForDisplay(xarModGetVar('authsso','add_user_maildomain'));
    $data['ldapnameattr'] = xarVarPrepForDisplay(xarML('LDAP Display Name Attribute'));
    $data['ldapnamevalue'] = xarVarPrepForDisplay(xarModGetVar('authsso','ldapdisplayname'));
    $data['ldapmailattr'] = xarVarPrepForDisplay(xarML('LDAP Email Attribute'));
    $data['ldapmailvalue'] = xarVarPrepForDisplay(xarModGetVar('authsso','ldapmail'));
    $data['submitbutton'] = xarVarPrepForDisplay(xarML('Update AuthSSO Configuration'));

    // Get groups
    $data['defaultgrouplabel'] = xarVarPrepForDisplay(xarML('Default Group'));
    $data['defaultgroup'] = xarModGetVar('authsso', 'defaultgroup');

    // Get default users group
    if (empty($data['defaultgroup'])) {
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

    // Send the values to the template
    return $data;
}

?>