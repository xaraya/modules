<?php
/**
 * HTML Module
 *
 * @package modules
 * @subpackage html module
 * @category Third Party Xaraya Module
 * @version 1.5.0
 * @copyright see the html/credits.html file in this release
 * @link http://www.xaraya.com/index.php/release/779.html
 * @author John Cox
 */

/**
 * Generate the common admin menu configuration
 *
 * @access public
 * @author Richard Cave
 * @return array $menu
 */
function html_adminapi_menu()
{
    // Initialise the array that will hold the menu configuration
    $menu = [];

    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('HTML Administration');

    // Return the array containing the menu configuration
    return $menu;
}
