<?php
/**
 * File: $Id:
 * 
 * Utility function to pass menu items to the main menu
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
 * utility function pass individual menu items to the main menu
 * 
 * @author the Michel V. 
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function sigmapersonnel_adminapi_getmenulinks()
{ 
    // Security Check
    if (xarSecurityCheck('AddSIGMAPersonnel', 0)) {
        $menulinks[] = Array('url' => xarModURL('sigmapersonnel',
                'admin',
                'newperson'), 
            // In order to display the tool tips and label in any language,
            // we must encapsulate the calls in the xarML in the API.
            'title' => xarML('Adds a new person to the system.'),
            'label' => xarML('Add Person'));
    } 
    // Security Check
    if (xarSecurityCheck('EditSIGMAPersonnel', 0)) {

        $menulinks[] = Array('url' => xarModURL('sigmapersonnel',
                'admin',
                'viewpersons'), 
            // In order to display the tool tips and label in any language,
            // we must encapsulate the calls in the xarML in the API.
            'title' => xarML('View all sigmapersonnel items that have been added.'),
            'label' => xarML('View Persons'));
    } 
    // Security Check
    if (xarSecurityCheck('AdminSIGMAPersonnel', 0)) {
        $menulinks[] = Array('url' => xarModURL('sigmapersonnel',
                'admin',
                'view'), 
            'title' => xarML('View DD configuration'),
            'label' => xarML('View DD parameters'));
    } 

    // Security Check
    if (xarSecurityCheck('AdminSIGMAPersonnel', 0)) {
        $menulinks[] = Array('url' => xarModURL('sigmapersonnel',
                'admin',
                'modifyconfig'), 
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'));
    } 
    // If we return nothing, then we need to tell PHP this, in order to avoid an ugly
    // E_ALL error.
    if (empty($menulinks)) {
        $menulinks = '';
    } 
    // The final thing that we need to do in this function is return the values back
    // to the main menu for display.
    return $menulinks;
} 

?>
