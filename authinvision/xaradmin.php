<?php
/**
 * File: $Id$
 *
 * AuthLDAP Administrative Display Functions
 * 
 * @package authentication
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage authinvision
 * @author Chris Dudley <miko@xaraya.com> | Richard Cave <rcave@xaraya.com>
*/

/**
 * the main administration function
 */
function authinvision_admin_main()
{
    // Security check
    if(!xarSecurityCheck('Adminauthinvision')) return;

    // return array from admin-main template
    return array();
}

/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function authinvision_admin_modifyconfig()
{
    // Security check
    if(!xarSecurityCheck('Adminauthinvision')) return;
    
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    
    // prepare labels and values for display by the template
    $data['server'] = xarVarPrepForDisplay(xarModGetVar('authinvision','server'));
    $data['database'] = xarVarPrepForDisplay(xarModGetVar('authinvision','database'));
    $data['username'] = xarVarPrepForDisplay(xarModGetVar('authinvision','username'));
    $data['password'] = xarVarPrepForDisplay(xarModGetVar('authinvision','password'));
    $data['prefix'] = xarVarPrepForDisplay(xarModGetVar('authinvision','prefix'));
    $data['forumroot'] = xarVarPrepForDisplay(xarModGetVar('authinvision','forumroot'));

    // Get groups
    $data['defaultgroup'] = xarModGetVar('authinvision', 'defaultgroup');

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

/**
 * Update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function authinvision_admin_updateconfig()
{
    // Get parameters
    
    list($server,
        $database,
        $username,
        $password,
        $prefix,
        $forumroot,
        $defaultgroup ) = xarVarCleanFromInput('server',
                                              'database', 
                                              'username', 
                                              'password', 
                                              'prefix', 
                                              'forumroot', 
                                              'defaultgroup');

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    xarModSetVar('authinvision', 'server', $server);
    xarModSetVar('authinvision', 'database', $database);
    xarModSetVar('authinvision', 'username', $username);
    xarModSetVar('authinvision', 'password', $password);
    xarModSetVar('authinvision', 'prefix', $prefix);
    xarModSetVar('authinvision', 'forumroot', $forumroot);

    // Get default users group
    if (!isset($defaultgroup)) {
        // See if Users role exists
        if( xarFindRole("Users"))
            $defaultgroup = 'Users';
    } 
    xarModSetVar('authinvision', 'defaultgroup', $defaultgroup);

    // lets update status and display updated configuration
    xarResponseRedirect(xarModURL('authinvision', 'admin', 'modifyconfig'));

    // Return
    return true;
}

/**
 * utility function pass individual menu items to the main menu
 *
 * @author Richard Cave
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function authinvision_adminapi_getmenulinks()
{
    // Security check 
    if(xarSecurityCheck('Adminauthinvision')) {
        $menulinks[] = Array('url'   => xarModURL('authinvision',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify the configuration for the module'),
                              'label' => xarML('Modify Config'));
    } else {
        $menulinks = '';
    }

    return $menulinks;
}

?>