<?php
/**
 * Get admin menu links
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Lists Module
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function lists_adminapi_getmenulinks()
{
    // Security Check
    //if (xarSecurityCheck('EditLists',0)) {
        $menulinks[] = array(
            'url'   => xarModURL('lists', 'admin', 'view'),
            'title' => xarML('View lists'),
            'label' => xarML('View lists'));
    //}

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>