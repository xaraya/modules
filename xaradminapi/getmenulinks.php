<?php
/**
 * Pass individual menu items to the admin menu
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
 * Pass individual menu items to the admin  menu
 *
 * @author the Example module development team
 * @return array containing the menulinks for the main menu items.
 */
function twitter_adminapi_getmenulinks()
{
    if (xarSecurityCheck('AdminTwitter', 0)) {
        $menulinks[] = array('url' => xarModURL('twitter','admin','modifyconfig'),
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)) {
        $menulinks = '';
    }
    return $menulinks;
}
?>
