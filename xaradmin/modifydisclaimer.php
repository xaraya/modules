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
 * Modify an Newsletter disclaimer
 *
 * @public
 * @author Richard Cave
 * @param 'id' the id of the disclaimer to be modified
 * @returns array
 * @return $templateVarArray
 */
function newsletter_admin_modifydisclaimer() 
{
    // Security check
    if(!xarSecurityCheck('EditNewsletter')) return;

    // Get input parameters
    if (!xarVarFetch('id', 'id', $id)) return;

    // The user API function is called
    $item = xarModAPIFunc('newsletter',
                          'user',
                          'getdisclaimer',
                          array('id' => $id));

    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Set hook variables
    $item['module'] = 'newsletter';
    $hooks = xarModCallHooks('item','modify',$id,$item);
    if (empty($hooks) || !is_string($hooks)) {
        $hooks = '';
    }

    // Get the admin menu
    $menu = xarModAPIFunc('newsletter', 'admin', 'menu');

    // Return the template variables defined in this function
    $templateVarArray = array('authid' => xarSecGenAuthKey(),
        'updatebutton' => xarVarPrepForDisplay(xarML('Update Disclaimer')),
        'menu' => $menu,
        'hooks' => $hooks,
        'item' => $item);

    // Return the template variables defined in this function
    return $templateVarArray;
}

?>
