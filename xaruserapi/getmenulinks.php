<?php
/**
 * Utility function pass individual menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */

/**
 * Utility function pass individual menu items to the main menu
 * 
 * @author the Example module development team
 * @return array containing the menulinks for the main menu items.
 */
function userpoints_userapi_getmenulinks()

    if (xarSecurityCheck('ReadUserpoints', 0)) {
        $menulinks[] = array('url' => xarModURL('userpoints','user','display'),

            'title' => xarML('Displays all point status'),
            'label' => xarML('Display'));
    }
    /* If we return nothing, then we need to tell PHP this, in order to avoid an ugly
     * E_ALL error.
     */
    if (empty($menulinks)) {
        $menulinks = '';
    }
    return $menulinks;
} 
?>