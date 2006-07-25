<?php
/**
 * Access Methods Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Access Methods Module
 * @link http://xaraya.com/index.php/release/333.html
 * @author St.Ego <webmaster@ivory-tower.net>
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author St.Ego
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function accessmethods_userapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurityCheck('ViewAccessMethods',0)) {

        $menulinks[] = Array('url'   => xarModURL('accessmethods',
                                                   'user',
                                                   'view'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('View all websites'),
                              'label' => xarML('View Access Methods'));

        // this shows a link to the user settings
        if (xarUserIsLoggedIn()) {
            $menulinks[] = Array('url'   => xarModURL('accessmethods',
                                                      'user',
                                                      'settings'),
                                 // In order to display the tool tips and label in any language,
                                 // we must encapsulate the calls in the xarML in the API.
                                 'title' => xarML('Change site list options'),
                                 'label' => xarML('Settings'));
        }
    }

    return $menulinks;
}

?>
