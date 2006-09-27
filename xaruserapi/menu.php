<?php
/**
 * Generate admin menu
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
 * Generate user menu
 * 
 * Standard function to generate a common admin menu configuration for the module
 *
 * @author the xProject module development team
 */
function xproject_userapi_menu()
{ 
    /*Initialise the array that will hold the menu configuration */
    $menu = array();
    /* Specify the menu title to be used in your blocklayout template */
    $menu['menutitle'] = xarML('xProject');
    /* Specify the menu labels to be used in your blocklayout template
     * Preset some status variable
     */
    $menu['status'] = '';
     
    return $menu;
} 
?>
