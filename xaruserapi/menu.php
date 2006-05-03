<?php
/**
 * Generate the common menu configuration
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
 * Generate the common menu configuration
 *
 * @author the Example module development team
 */
function example_userapi_menu()
{ 
    /* Initialise the array that will hold the menu configuration */
    $menu = array(); 

    /* Specify the menu title to be used in your blocklayout template */
    $menu['menutitle'] = xarML('Example');
 
    /* Specify the menu items to be used in your blocklayout template */
    $menu['menulabel_view'] = xarML('View example items');
    $menu['menulink_view'] = xarModURL('example', 'user', 'view');

    /* Specify the labels/links for more menu items if relevant */
    /* $menu['menulabel_other'] = xarML('Some other menu item');
     * $menu['menulink_other'] = xarModURL('example','user','other');
     * ...
     * Note : you could also put all menu items in a $menu['menuitems'] array

     * Initialise the array that will hold the different menu items
     * $menu['menuitems'] = array();

     * Define a menu item
     * $item = array();
     * $item['menulabel'] = _EXAMPLEVIEW;
     * $item['menulink'] = xarModURL('example','user','view');

     * Add it to the array of menu items
     * $menu['menuitems'][] = $item;

     * Add more menu items to the array
     * ...

     * Then you can let the blocklayout template create the different
     * menu items *dynamically*, e.g. by using something like :

     * <xar:loop name="menuitems">
     * <td><a href="&xar-var-menulink;">&xar-var-menulabel;</a></td>
     * </xar:loop>

     * in the templates of your module. Or you could even pass an argument
     * to the user_menu() function to turn links on/off automatically
     * depending on which function is currently called...

     * But most people will prefer to specify all this manually in each
     * blocklayout template anyway :-)
     */
     
     /* Return the array containing the menu configuration */
    return $menu;
}
?>