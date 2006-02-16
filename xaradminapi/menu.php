<?php
/**
 * Standard function to generate the common admin menu configuration
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
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
    // Note : you could also put all menu items in a $menu['menuitems'] array

    // Initialise the array that will hold the different menu items
    // $menu['menuitems'] = array();

    // Define a menu item
    // $item = array();
    // $item['menulabel'] = xarML('Courses');
    // $item['menulink'] = xarModURL('example','admin','viewcourses');

    // Add it to the array of menu items
    // $menu['menuitems'][] = $item;

    // Add more menu items to the array
    // ...

    // Then you can let the blocklayout template create the different
    // menu items *dynamically*, e.g. by using something like :

    // <xar:loop name="menuitems">
    // <td><a href="&xar-var-menulink;">&xar-var-menulabel;</a></td>
    // </xar:loop>

    // in the templates of your module. Or you could even pass an argument
    // to the admin_menu() function to turn links on/off automatically
    // depending on which function is currently called...

    // But most people will prefer to specify all this manually in each
    // blocklayout template anyway :-)
    // Return the array containing the menu configuration
    return $menu;
}

?>
