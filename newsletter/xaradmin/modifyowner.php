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
 * Modify an Newsletter owner
 *
 * @author Richard Cave
 * @param 'id' the id of the owner to be modified
 * @returns array
 * @return $data
 */
function newsletter_admin_modifyowner() 
{
    // Get parameters from input
    if (!xarVarFetch('id', 'id', $id)) return;

    // The user API function is called
    $owner = xarModAPIFunc('newsletter',
                           'user',
                           'getowner',
                           array('id' => $id));

    if(!$owner) {
        $msg = xarML('Error in #(1) #(2): could not find owner id #(3)',
                    'Newsletter', 'modifyowner', xarVarPrepForDisplay($id));
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get the role for the selected group
    $role = xarModAPIFunc('roles',
                          'user',
                          'get',
                           array('uid' => $owner['rid'],
                                 'type' => 1));

    // Get groups
    $owner['defaultgroup'] = $role['name'];

    // Get the list of groups
    if (!$groupRoles = xarGetGroups()) return; // throw back

    $i=0;
    while (list($key,$group) = each($groupRoles)) {
        // Check to see if this is an newsletter group
        $groups[$i]['name'] = xarVarPrepForDisplay($group['name']);
        $i++;
    }
    sort($groups);

    $owner['groups'] = $groups;

    // Set hook variables
    $owner['module'] = 'newsletter';
    $hooks = xarModCallHooks('owner','modify',$id,$owner);
    if (empty($hooks) || !is_string($hooks)) {
        $hooks = '';
    }

    // Get the admin menu
    $menu = xarModAPIFunc('newsletter', 'admin', 'menu');

    // Set the template variables defined in this function
    $templateVarArray = array('authid' => xarSecGenAuthKey(),
            'updatebutton' => xarVarPrepForDisplay(xarML('Update User')),
            'hooks' => $hooks,
            'menu' => $menu, 
            'owner' => $owner);

    // Return the template variables defined in this function
    return $templateVarArray;
}

?>
