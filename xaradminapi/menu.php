<?php
/**
 * Generate admin menu
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
/**
 * Generate admin menu
 *
 * Standard function to generate a common admin menu configuration for the module
 *
 * @author the Example module development team
 */
function example_adminapi_menu()
{
    /*Initialise the array that will hold the menu configuration */
    $menu = array();

    /* Specify a menu title (seldom used in blocklayout templates). */
    $menu['menutitle'] = xarML('Example Administration');
    /* Specify the menu labels to be used in your blocklayout template
     * Preset some status variable
     */
    $menu['status'] = '';

    /* Initialise the array that will hold the different menu items */
    $menu['menuitems'] = array();

    /* Fill the array with the menu items from API function */
    $menu['menuitems'] = xarModAPIFunc('example', 'admin', 'getmenulinks');

    /* Note: As developer you have three ways to control the templated menus.
     *
     * 1) Use the data delivered from example_adminapi_getmenulinks(). This
     *    guarantees that you automatically have the same menu links and titles
     *    in the admin menu block and in your templates (Coded above).
     *
     * 2) Individually define menu items in this file. This allows different
     *    menu items in the main admin and the in-page admin menus.
     *      $item= array('url' => xarModURL('example','admin','new'),
     *                'title'  => xarML('Adds a new item to system.'),
     *                'label'  => xarML('Add Item'),
     *                'active' => array('new'));
     *      $menu['menulinks'][] = $item;
     *
     * 3) Manually specify the menu items and their security checks in the
     *    blocklayout templates.
     *
     * The first two ways let the blocklayout template create the different
     * menu items *dynamically*, e.g. by using something like :
     *    <xar:loop name="menuitems">
     *      <dd><a href="#$loop:item.url#">#$loop:item.label#</a></dd>
     *    </xar:loop>
     * in the templates of your module.
     *
     * As site admin you can completely design your own in-page menu with the
     * template themes/[yourtheme]/modules/example/includes/admin-menu.xt
     */

     /* Return the array containing the menu configuration */
    return $menu;
}
?>