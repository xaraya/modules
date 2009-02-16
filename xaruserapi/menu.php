<?php
/**
 * Generate the common menu configuration
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
 * Generate the common menu configuration
 * The complete explanation for generating menu items is at example/xaradminapi/menu.php
 *
 * @author the Example module development team
 */
function example_userapi_menu()
{
    // The complete explanation for generating menu items is at example/xaradminapi/menu.php
    $menu = array();
    $menu['menutitle'] = xarML('Example');
    $menu['menuitems'] = xarModAPIFunc('example', 'user', 'getmenulinks');

    return $menu;
}
?>