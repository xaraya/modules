<?php
/**
 * Ratings Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ratings Module
 * @link http://xaraya.com/index.php/release/41.html
 * @author Jim McDonald
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Ratings module development team
 * @return array containing the menulinks for the main menu items.
 */
function ratings_adminapi_getmenulinks()
{
    // Security Check
    if (xarSecurityCheck('AdminRatings')) {
        $menulinks[] = Array('url'   => xarModURL('ratings',
                                                  'admin',
                                                  'view'),
                              'title' => xarML('View ratings statistics per module'),
                              'label' => xarML('View Statistics'));
        $menulinks[] = Array('url'   => xarModURL('ratings',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the ratings module configuration'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>
