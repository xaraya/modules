<?php
/**
 * Generate the common menu configuration
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * generate the common menu configuration for users
 * @todo keep this? Currently not in use
 */
function courses_userapi_menu()
{
    // Initialise the array that will hold the menu configuration
    $menu = array();
    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('Courses');
    // Specify the menu items to be used in your blocklayout template
    $menu['menulabel_view'] = xarML('Courses View');
    $menu['menulink_view'] = xarModURL('courses', 'user', 'view');
    // No real use for now
    return $menu;
}
?>
