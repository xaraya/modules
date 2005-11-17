<?php
/**
 * Generate the common menu configuration
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 */

/**
 * Generate the common user menu configuration
 *
 * This function show a menu with the following items:
 *      Selector for all, allmine
 *      Selector for status
 *      Selector for groups/projects
 *
 * @author the Todolist module development team
 *
 */
function todolist_userapi_menu()
{
    /* Initialise the array that will hold the menu configuration */
    $menu = array();

    /* Specify the menu title to be used in your blocklayout template */
    $menu['menutitle'] = xarML('Todolist');

    /* Specify the menu items to be used in your blocklayout template */
    $menu['menulabel_view'] = xarML('View your todo items');
    $menu['menulink_view'] = xarModURL('todolist', 'user', 'view');

    /* Specify the labels/links for more menu items if relevant */
    /**
     * $menu['menulabel_other'] = xarML('Some other menu item');
     * $menu['menulink_other'] = xarModURL('example','user','other');
     * ...
     */

     // Get the projects for this person
     // Get the todos for this person
     // Show a dynamic dropdown for project selection
     // When this is submitted, a list of todos is regenerated.

     /* Return the array containing the menu configuration */
    return $menu;
}
?>