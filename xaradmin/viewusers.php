<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/

/**
 * View a list of Newsletter owners
 *
 * @author Richard Cave
 * @param $startnum starting number to display
 * @param $sortby sort ('name' or group')
 * @returns array
 * @return $data
 */
function newsletter_admin_viewusers()
{
    // Security check
    if(!xarSecurityCheck('AdminNewsletter')) return;

    // Get parameters
    if (!xarVarFetch('startnum', 'int:0:', $startnum, 1)) return;
    if (!xarVarFetch('sortby', 'str:1:', $sortby, 'name')) return;

    // Get the admin edit menu
    $data['menu'] = xarModFunc('newsletter', 'admin', 'configmenu');

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // Prepare the array variable that will hold all items for display
    $data['sortby'] = $sortby;

    // Set template labels
    $data['addlabel'] = xarVarPrepForDisplay(xarML('Add User'));

    // Get list of all current owners
    $owners = xarModAPIFunc('newsletter',
                            'user',
                            'get',
                            array('startnum' => 1, 
                                  'numitems' => xarModGetVar('newsletter',
                                                             'itemsperpage'),
                                  'phase' => 'owner'));
    // Get the list of users
    $roles = xarModAPIFunc('roles','user','getall');

    // Don't include users that have already been added
    if (empty($owners)) { 
        $data['roles'] = $roles;
    } else {
        $data['roles'] = array();
        foreach ($roles as $role) {
            $foundName = false;

             // Loop through owners and find matching names
            foreach ($owners as $owner) {
                if ($owner['id'] == $role['uid']) {
                    $foundName = true;
                    break;
                }
            }
            if (!$foundName)
                $data['roles'][] = $role;
        }
    }

    // Get default group for a user
    $writerGroup = xarModGetVar('newsletter', 'writer');

    // Get groups
    $data['defaultgroup'] = $writerGroup;

    // Get the list of groups
    if (!$groupRoles = xarGetGroups()) return; // throw back

    $i=0;
    while (list($key,$group) = each($groupRoles)) {
        // Don't include the top newsletter group
        $groups[$i]['name'] = xarVarPrepForDisplay($group['name']);
        $i++;
    }
    // sort groups by level
    sort($groups);

    // Set groups for template
    $data['groups'] = $groups;

    // See if the newsletter groups have already been created
    $data['creategroups'] = xarModGetVar('newsletter', 'creategroups');

    // Get list of owners
    $users = xarModAPIFunc('newsletter',
                           'user',
                           'getowners',
                           array('startnum' => $startnum,
                                 'numowners' => xarModGetVar('newsletter',
                                 'ownersperpage')));

    // Check for exceptions
    if (!isset($users) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Create sort by URLs
    if ($sortby != 'name' ) {
        $data['nameurl'] = xarModURL('newsletter',
                                     'admin',
                                     'viewusers',
                                     array('sortby' => 'name'));
    } else {
        $data['nameurl'] = '';
    }

    if ($sortby != 'group' ) {
        $data['groupurl'] = xarModURL('newsletter',
                                      'admin',
                                      'viewusers',
                                      array('sortby' => 'group'));
    } else {
        $data['groupurl'] = '';
    }

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($users); $i++) {
        $item = $users[$i];

        // Get the group for the user 
        $role = xarModAPIFunc('roles',
                              'user',
                              'get',
                               array('uid' => $item['rid'],
                                     'type' => 1));
        
        $users[$i]['group'] = $role['name'];

        // Edit URL
        $users[$i]['editurl'] = xarModURL('newsletter',
                                          'admin',
                                          'modifyowner',
                                          array('id' => $item['id']));

        $users[$i]['edittitle'] = xarML('Edit');

        // Delete URL
        $users[$i]['deleteurl'] = xarModURL('newsletter',
                                            'admin',
                                            'deleteowner',
                                            array('id' => $item['id']));

        $users[$i]['deletetitle'] = xarML('Delete');
    }

    // If $sortby is group, then resort array by group name
    // since default is to sort by owner name
    if ($sortby == 'group') {
        usort( $users, "newsletter_admin__cmpgroup" );
    }

    // Add the array of users to the template variables
    $data['users'] = $users;

    // Return the template variables defined in this function
    return $data;
}


/**
 * Comparision functions for sorting by name
 *
 * @private
 * @author Richard Cave
 * @param a multi-dimensional array
 * @param b multi-dimensional array
 * @returns strcmp
 */
function newsletter_admin__cmpgroup ($a, $b) 
{
    $cmp1 = trim(strtolower($a['group']));
    $cmp2 = trim(strtolower($b['group']));
    return strcmp($cmp1, $cmp2);
}

?>
