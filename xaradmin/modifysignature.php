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
 * Modify a Newsletter owner's signature
 *
 * @public
 * @author Richard Cave
 * @returns array
 * @return $templateVarArray
 */
function newsletter_admin_modifysignature() 
{
    // Security check
    if(!xarSecurityCheck('EditNewsletter')) return;

    // Check to see if this user is a issue owner
    $userId = xarSessionGetVar('uid');

    // The user API function is called
    $item = xarModAPIFunc('newsletter',
                          'user',
                          'getowner',
                          array('id' => $userId));

    if($item) {
        // Set hook variables
        $item['module'] = 'newsletter';
        $hooks = xarModCallHooks('item','modify',$userId,$item);
        if (empty($hooks) || !is_string($hooks)) {
            $hooks = '';
        }

        // Get the admin edit menu
        $menu = xarModFunc('newsletter', 'admin', 'editmenu');

        // Set the template variables defined in this function
        $templateVarArray = array('authid' => xarSecGenAuthKey(),
            'updatebutton' => xarVarPrepForDisplay(xarML('Update Signature')),
            'menu' => $menu,
            'hooks' => $hooks,
            'item' => $item);
    } else {
        $errorMsg = "You cannot edit another owner's signature.";
        $templateVarArray = array('errorMsg' => xarVarPrepForDisplay(xarML($errorMsg)),
                                  'modifylabel' =>  xarVarPrepForDisplay(xarML('Modify Owner')));
    }

    // Return the template variables defined in this function
    return $templateVarArray;
}

?>
