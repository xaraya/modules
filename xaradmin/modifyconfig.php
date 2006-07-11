<?php
/**
 * AuthURL Administrative Display Functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AuthURL
 * @link http://xaraya.com/index.php/release/42241.html
 * @author Court Shrock <shrockc@inhs.org>
 */

/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function authurl_admin_modifyconfig()
{
    # Security check
    if(!xarSecurityCheck('AdminAuthURL')) return;

    # Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    # prepare labels and values for display by the template
    $data['title'] = xarVarPrepForDisplay(xarML('Administration'));
    $data['configadmin'] = xarVarPrepForDisplay(xarML('Configure AuthURL'));

    # var - auth_URL
    $data['authurl'] = xarVarPrepForDisplay(xarML('Authentication URL'));
    $data['authurlvalue'] = xarVarPrepForDisplay(xarModGetVar('authurl','auth_url'));

    # var - debug_level
    $data['debuglevellabel'] = xarVarPrepForDisplay(xarML('Debug/Log Level'));
    $data['debuglevelvalue'] = xarVarPrepForDisplay(xarModGetVar('authurl','debug_level'));
    $data['levels'][] = array('level'=>'0', 'name'=>xarVarPrepForDisplay(xarML('None')));
    $data['levels'][] = array('level'=>'1', 'name'=>xarVarPrepForDisplay(xarML('Detail when Fail')));
    $data['levels'][] = array('level'=>'2', 'name'=>xarVarPrepForDisplay(xarML('All Detail')));

    # var - add_user
    $data['adduser'] = xarVarPrepForDisplay(xarML('Add AuthURL User to Xaraya Database on Login'));
    if (xarModGetVar('authurl','add_user') == 'true') {
        $data['adduservalue'] = xarVarPrepForDisplay('checked="checked"');
    } else {
        $data['adduservalue'] = "";
    }

    # var - default_group
    $data['defaultgrouplabel'] = xarVarPrepForDisplay(xarML('Default Group for new Users'));
    $data['defaultgroup'] = xarModGetVar('authurl', 'default_group');

    # Get the list of groups
    if (!$groupRoles = xarGetGroups()) return; // throw back

    $i=0;
    while (list($key,$group) = each($groupRoles)) {
        $groups[$i]['name'] = xarVarPrepForDisplay($group['name']);
        $i++;
    }// while
    $data['groups'] = $groups;

    # Submit button
    $data['submitlabel'] = xarVarPrepForDisplay(xarML('Click [Update Config] to change this configuration:'));
    $data['submitbutton'] = xarVarPrepForDisplay(xarML('Update Config'));

    # Everything else happens in Template for now
    return $data;
}

?>
