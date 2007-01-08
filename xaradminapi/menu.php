<?php
/**
 * Standard function to generate the common admin menu configuration
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * generate the common admin menu configuration
 * @TODO ALL
 */
function courses_adminapi_menu()
{
    // Initialise the array that will hold the menu configuration
    $menu = array();
    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('Courses Administration');
    // Specify the menu labels to be used in your blocklayout template
    // Preset some status variable
    $menu['status'] = '';
    // Note : you could also specify the menu links here, and pass them
    // on to the template as variables
     $menu['menulink_view'] = xarModURL('courses','admin','viewcourses');

    return $menu;
}

?>
