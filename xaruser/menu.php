<?php
/**
 * Common menu configuration
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
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