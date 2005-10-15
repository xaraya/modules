<?php
/**
 * Utility function for menu items for main menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function dyn_example_userapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurityCheck('ViewDynExample',0)) {

        $menulinks[] = Array('url'   => xarModURL('dyn_example',
                                                   'user',
                                                   'view'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('View all dynamic example items'),
                              'label' => xarML('View Items'));

        // this shows a link to the user settings
        if (xarUserIsLoggedIn()) {
            $menulinks[] = Array('url'   => xarModURL('dyn_example',
                                                      'user',
                                                      'settings'),
                                 // In order to display the tool tips and label in any language,
                                 // we must encapsulate the calls in the xarML in the API.
                                 'title' => xarML('Change your preferences for this module'),
                                 'label' => xarML('Settings'));
        }
    }

    return $menulinks;
}

?>
