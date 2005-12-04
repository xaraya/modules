<?php
/**
 * Get the score for a user
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Userpoints Module
 * @link http://xaraya.com/index.php/release/782.html
 * @author Userpoints Module Development Team
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Userpoints module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function userpoints_adminapi_getmenulinks()
{
    // Security Check
    if (xarSecurityCheck('AdminUserpoints')) {
        $menulinks[] = Array('url'   => xarModURL('userpoints',
                                                  'admin',
                                                  'newrank'),
                              'title' => xarML('Add A New User Rank'),
                              'label' => xarML('Add Rank'));
        $menulinks[] = Array('url'   => xarModURL('userpoints',
                                                  'admin',
                                                  'viewrank'),
                              'title' => xarML('View The Existing Ranks'),
                              'label' => xarML('View Ranks'));
        $menulinks[] = Array('url'   => xarModURL('userpoints',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the userpoints module configuration'),
                              'label' => xarML('Modify Config'));

    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>
