<?php
/**
 * Utility function for menu items for main menu
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * utility function pass individual menu items to the main user menu
 *
 * This function helps the xaraya to build a menu. It checks for the privileges of the current user\
 * and will prevent the user from seeing links he can't enter.
 *
 * @author the Dyn_Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function dyn_example_userapi_getmenulinks()
{
    $menulinks = array();
    // Check for the privilege of the current user
    // We hide a possible error
    if (xarSecurityCheck('ViewDynExample',0)) {

        $menulinks[] = Array('url'   => xarModURL('dyn_example',
                                                   'user',
                                                   'view'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('View all dynamic data example items'),
                              'label' => xarML('View Items'));
    }
    // Return all the links to the menu
    return $menulinks;
}

?>
