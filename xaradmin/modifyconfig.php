<?php
/**
 * File: $Id$
 *
 * Administrator modify config
 *
 * @package authentication
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage authinvision2
 * @author Brian McCloskey <brian@nexusden.com>
*/
function authinvision2_admin_modifyconfig()
{
    // Security check
    if(!xarSecurityCheck('Adminauthinvision2')) return;
    
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    
    // prepare labels and values for display by the template
    $data['server'] = xarVarPrepForDisplay(xarModGetVar('authinvision2','server'));
    $data['database'] = xarVarPrepForDisplay(xarModGetVar('authinvision2','database'));
    $data['username'] = xarVarPrepForDisplay(xarModGetVar('authinvision2','username'));
    $data['password'] = xarVarPrepForDisplay(xarModGetVar('authinvision2','password'));
    $data['prefix'] = xarVarPrepForDisplay(xarModGetVar('authinvision2','prefix'));
    $data['forumroot'] = xarVarPrepForDisplay(xarModGetVar('authinvision2','forumroot'));
    $data['onlyauth'] = xarVarPrepForDisplay(xarModGetVar('authinvision2','onlyauth'));

    // Get groups
    $data['defaultgroup'] = xarModGetVar('authinvision2', 'defaultgroup');

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
    $data['submitbutton'] = xarML('Submit');

    // everything else happens in Template for now
    return $data;
}
?>
