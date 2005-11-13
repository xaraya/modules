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
 * @subpackage maxercalls
 * @author Example module development team 
 */
/**
 * utility function pass individual menu items to the main menu
 * 
 * @author the maxercalls module development team 
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function maxercalls_adminapi_getmenulinks()
{ 
    // First we need to do a security check to ensure that we only return menu items
    // that we are suppose to see.  It will be important to add for each menu item that
    // you want to filter.  No sense in someone seeing a menu link that they have no access
    // to edit.  Notice that we are checking to see that the user has permissions, and
    // not that he/she doesn't.
    // Security Check
    if (xarSecurityCheck('AddMaxercalls', 0)) {
        $menulinks[] = Array('url' => xarModURL('maxercalls',
                'user',
                'new'), 
            // In order to display the tool tips and label in any language,
            // we must encapsulate the calls in the xarML in the API.
            'title' => xarML('Adds a new Call to the system.'),
            'label' => xarML('Add call'));
    } 
    // Security Check
    if (xarSecurityCheck('EditMaxercalls', 0)) {
        $menulinks[] = Array('url' => xarModURL('maxercalls',
                'admin',
                'viewcalls'), 
            // In order to display the tool tips and label in any language,
            // we must encapsulate the calls in the xarML in the API.
            'title' => xarML('View all maxercalls items that have been added.'),
            'label' => xarML('View calls'));
    } 
    // Security Check
    if (xarSecurityCheck('AdminMaxercalls', 0)) {
        $menulinks[] = Array('url' => xarModURL('maxercalls',
                'admin',
                'view'), 
            // In order to display the tool tips and label in any language,
            // we must encapsulate the calls in the xarML in the API.
            'title' => xarML('View the standard call texts'),
            'label' => xarML('View call types'));
    } 
	
    // Security Check
    if (xarSecurityCheck('AdminMaxercalls', 0)) {
        $menulinks[] = Array('url' => xarModURL('maxercalls',
                'admin',
                'modifyconfig'), 
            // In order to display the tool tips and label in any language,
            // we must encapsulate the calls in the xarML in the API.
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
