<?php
/**
 * File: $Id:
 * 
 * Standard function to generate the common admin menu configuration
 * 
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com *
 */
/**
 * generate the common admin menu configuration
 */
function xarcpshop_adminapi_menu()
{
    // Initialise the array that will hold the menu configuration
    $menu = array();
    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('CP Shop - Cafe Press for Xaraya');
    $menu['status'] = '';

    // Initialise the array that will hold the different menu items
     $menu['menuitems'] = array();

    // Define a menu item
     $item = array();
     $item['menulabel'] = xarML('New Shop');
     $item['menulink'] = xarModURL('xarcpshop','admin','new');
     $menu['menuitems'][] = $item;

     $item['menulabel'] = xarML('View Stores');
     $item['menulink'] = xarModURL('xarcpshop','admin','view');
     $menu['menuitems'][] = $item;

     $item['menulabel'] = xarML('Product Types');
     $item['menulink'] = xarModURL('xarcpshop','admin','prodtypes');
     $menu['menuitems'][] = $item;

     $item['menulabel'] = xarML('Modify Config');
     $item['menulink'] = xarModURL('xarcpshop','admin','modifyconfig');
     $menu['menuitems'][] = $item;
    // Return the array containing the menu configuration
    return $menu;
} 

?>
