<?php
/**
 * Utility function for menu items for main menu
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Contact Form Module
 * @link http://xaraya.com/index.php/release/1049.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * utility function pass individual menu items to the main user menu
 *
 * This function helps the xaraya to build a menu. It checks for the privileges of the current user\
 * and will prevent the user from seeing links he can't enter.
 *
 * @author the Contact Form module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function contactform_userapi_getmenulinks()
{
    $menulinks = array();
    // Check for the privilege of the current user
    // We hide a possible error
    if (xarSecurityCheck('ViewContactForm',0)) {

        $menulinks[] = Array('url'   => xarModURL('contactform',
                                                   'user',
                                                   'new'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Contact Form'),
                              'label' => xarML('Contact Form'));
    }
    // Return all the links to the menu
    return $menulinks;
}

?>
