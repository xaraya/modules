<?php
/**
 * File: $Id:
 *
 * SiteContact
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * generate the common menu configuration
 */
function sitecontact_userapi_menu()
{ 
    // Initialise the array that will hold the menu configuration
    $menu = array(); 
    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('ContactUs');
    // Specify the menu items to be used in your blocklayout template
    $menu['menulabel_view'] = xarML('Contact');
    $menu['menulink_view'] = xarModURL('sitecontact', 'user', 'main');
    return $menu;
} 

?>
