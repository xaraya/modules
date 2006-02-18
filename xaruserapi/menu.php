<?php
/**
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
 */
/**
 * Generate the common menu configuration
 *
 * @public
 * @author Richard Cave
 * @return array $menu
 */
function newsletter_userapi_menu()
{
    // Initialise the array that will hnew the menu configuration
    $menu = array();

    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('Newsletter');

    // Specify the menu items to be used in your blocklayout template
    $menu['menulink_view'] = xarModURL('newsletter','user','view');

    // Return the array containing the menu configuration
    return $menu;
}

?>
