<?php
/**
 * Generate admin menu
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage MP3 Jukebox Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author MP3 Jukebox Module Development Team
 */
/**
 * Generate admin menu
 * 
 * Standard function to generate a common admin menu configuration for the module
 *
 * @author the MP3 Jukebox module development team
 */
function mp3jukebox_adminapi_menu()
{ 
    /*Initialise the array that will hold the menu configuration */
    $menu = array();
    /* Specify the menu title to be used in your blocklayout template */
    $menu['menutitle'] = xarML('MP3Jukebox Administration');
    /* Specify the menu labels to be used in your blocklayout template
     * Preset some status variable
     */
    $menu['status'] = '';
    /* Note : you could also specify the menu links here, and pass them
     * on to the template as variables
     * $menu['menulink_view'] = xarModURL('mp3jukebox','admin','view');
     * Note : you could also put all menu items in a $menu['menuitems'] array

     * Initialise the array that will hold the different menu items
     * $menu['menuitems'] = array();

     * Define a menu item
     * $item = array();
     * $item['menulabel'] = _EXAMPLEVIEW;
     * $item['menulink'] = xarModURL('mp3jukebox','user','view');

     * Add it to the array of menu items
     * $menu['menuitems'][] = $item;

     * Add more menu items to the array
     * ...
     */
    
    /* Then you can let the blocklayout template create the different
     * menu items *dynamically*, e.g. by using something like :

     * <xar:loop name="menuitems">
     * <td><a href="&xar-var-menulink;">&xar-var-menulabel;</a></td>
     * </xar:loop>

     * in the templates of your module. Or you could even pass an argument
     * to the admin_menu() function to turn links on/off automatically
     * depending on which function is currently called...

     * But most people will prefer to specify all this manually in each
     * blocklayout template anyway :-)
     */
     /* Return the array containing the menu configuration */
    return $menu;
} 
?>