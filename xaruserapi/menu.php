<?php
/**
 * Generate the common menu configuration
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls module
 * @author Michel Vorenhout
 */
/**
 * generate the common menu configuration
 */
function maxercalls_userapi_menu()
{
    // Initialise the array that will hold the menu configuration
    $menu = array();
    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('View calls');
    // Specify the menu items to be used in your blocklayout template
    $menu['menulabel_view'] = xarML('View your Maxercalls');
    $menu['menulink_view'] = xarModURL('maxercalls', 'user', 'view');

    return $menu;
}

?>
