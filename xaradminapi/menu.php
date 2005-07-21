<?php
/**
 * File: $Id:
 * 
 * Standard function to generate the common admin menu configuration
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage calendar
 * @author calendar module development team 
 */
/**
 * generate the common admin menu configuration
 */
function calendar_adminapi_menu()
{ 
    // Initialise the array that will hold the menu configuration
    $menu = array(); 
    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('Calendar Administration'); 
    // Specify the menu labels to be used in your blocklayout template
    // Preset some status variable
    $menu['status'] = ''; 
    
    // Return the array containing the menu configuration
    return $menu;
} 

?>
