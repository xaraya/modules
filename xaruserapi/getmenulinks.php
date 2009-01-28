<?php
/**
 * Utility function pass individual menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
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
function twitter_userapi_getmenulinks()
{ 
    if (xarSecurityCheck('ViewTwitter', 0)) {
        $menulinks[] = array(
            'url'   => xarModURL('twitter','user','view'), 
            'title' => xarML('Display twitter timeline'),
            'label' => xarML('Timeline')
        );
    } 
    if (empty($menulinks)) {
        $menulinks = '';
    }
    return $menulinks;
} 
?>