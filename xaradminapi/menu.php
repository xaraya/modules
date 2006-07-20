<?php
/**
 * Generate admin menu
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * generate the common admin menu configuration
 *
 * This function is called by most admin display functions
 * An additional function of it is to create the array
 *
 * @return array
 */
function dyn_example_adminapi_menu()
{
    // Initialise the array that will hold the menu configuration
    $menu = array();

    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('Dynamic Example Administration');

    // Return the array containing the menu configuration
    return $menu;
}

?>
