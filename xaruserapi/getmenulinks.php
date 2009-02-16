<?php
/**
 * Utility function pass individual menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */

/**
 * Utility function pass individual menu items to the main menu
 * Full explanation for generating menu items is at example/xaradminapi/getmenulinks.php
 *
 * @author the Example module development team
 * @return array containing the menulinks for the main menu items.
 */
function example_userapi_getmenulinks()
{
    // For full explanation see example/xaradminapi/getmenulinks.php
    $menulinks = array();
    if (xarSecurityCheck('ViewExample', 0)) {
        $menulinks[] = array('url' => xarModURL('example', 'user', 'view'),
            'title' => xarML('Displays all example items for view'),
            'label' => xarML('Display'),
            'active'=> array('view'));
        $menulinks[] = array('url' => xarModURL('example', 'user', 'main'),
            'title' => xarML('Main page for Example module'),
            'label' => xarML('Main'),
            'active'=> array('main'));
    }
    return $menulinks;
}
?>