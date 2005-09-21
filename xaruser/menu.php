<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitecontact
 */

/**
 * generate the common menu configuration
 * @author Jo Dalle Nogare
 */
function sitecontact_userapi_menu()
{ 
    // Initialise the array that will hold the menu configuration
    $menu = array(); 
    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('Contact Us');
    // Specify the menu items to be used in your blocklayout template
    $menu['menulabel_view'] = xarML('Contact Us');
    $menu['menulink_view'] = xarModURL('sitecontact', 'user', 'main');
    return $menu;
}

?>
