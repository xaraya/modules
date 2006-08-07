<?php
/**
 * Utility function to pass admin menu links
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the XProject module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function xproject_userapi_getmenulinks()
{

    if (xarSecurityCheck('ReadXProject', 0)) {
        $menulinks[] = Array('url'   => xarModURL('xproject',
                                                   'user',
                                                   'view'),
                              'title' => xarML('List of current projects'),
                              'label' => xarML('View Projects'));

        $menulinks[] = Array('url'   => xarModURL('xproject',
                                                   'user',
                                                   'search'),
                              'title' => xarML('Query project entries'),
                              'label' => xarML('Search Projects'));
        if (xarUserIsLoggedIn()) {
            $menulinks[] = Array('url'   => xarModURL('xproject',
                                                      'user',
                                                      'settings'),
                                 // In order to display the tool tips and label in any language,
                                 // we must encapsulate the calls in the xarML in the API.
                                 'title' => xarML('Change your preferences for this module'),
                                 'label' => xarML('Settings'));
        }
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>