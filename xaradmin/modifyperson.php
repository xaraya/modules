<?php
/**
 * File: $Id:
 * 
 * Standard function to modify an item
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author Michel V. 
 */
/**
 * modify an item
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 * 
 * @param  $ 'personid' the id of the item to be modified
 */
function sigmapersonnel_admin_modifyperson($args)
{ 
    extract($args);
    // Get parameters from whatever input we need.
    if (!xarVarFetch('exid', 'int:1:', $exid)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str:1:', $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('number', 'str:1:', $number, $number,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name', 'str:1:', $name, $name, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $personid = $objectid;
    } 
    // The user API function is called.
    $item = xarModAPIFunc('sigmapersonnel',
                          'user',
                          'get',
                          array('personid' => $personid)); 
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
     
    if (!xarSecurityCheck('EditSIGMAPersonnel', 1, 'PersonnelItem', "$personid:All:$item[persstatus]")) { // add catid:
        return;
    } 
    // Get menu variables - it helps if all of the module pages have a standard
    // menu at their head to aid in navigation
    // $menu = xarModAPIFunc('sigmapersonnel','admin','menu','modify');
    $item['module'] = 'sigmapersonnel';
    $hooks = xarModCallHooks('item', 'modify', $personid, $item);
    // Return the template variables defined in this function
    return array('authid'       => xarSecGenAuthKey(),
                 'firstnamelabel'    => xarVarPrepForDisplay(xarML('First Name:')),
                 'firstname'         => $item['firstname'],
                 'numberlabel'  => xarVarPrepForDisplay(xarML('SIGMA Number:')),
                 'number'       => $number,
                 'invalid'      => $invalid,
                 'updatebutton' => xarVarPrepForDisplay(xarML('Update Person')),
                 'hookoutput'   => $hooks,

                 'item'         => $item);
} 

?>
