<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.zwiggybo.com
 *
 * @subpackage shouter
 * @link http://xaraya.com/index.php/release/236.html
 * @author Neil Whittaker
 */

/**
 * Pass menu variables back to the template
 *
 * @return array
 */
function shouter_adminapi_menu()
{ 
    // Initialise the array that will hold the menu configuration
    $menu = array(); 
    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('Shouter Administration'); 

    return $menu;
} 
?>
