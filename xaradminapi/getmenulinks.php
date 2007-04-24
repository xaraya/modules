<?php
/**
 * Pass individual menu items to the admin menu
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
 * Pass individual menu items to the admin  menu
 *
 * @author the MP3 Jukebox module development team
 * @return array containing the menulinks for the main menu items.
 */
function mp3jukebox_adminapi_getmenulinks()
{
    /* First we need to do a security check to ensure that we only return menu items
     * that we are suppose to see. It will be important to add for each menu item that
     * you want to filter. No sense in someone seeing a menu link that they have no access
     * to edit. Notice that we are checking to see that the user has permissions, and
     * not that he/she doesn't.
     * Security Check
     */

    /* The main menu will look for this  menulinks array and return it for a tree view of the module
     * We are just looking for three items in the array, the url, which we need to use the
     * xarModURL function, the title of the link, which will display a tool tip for the
     * module url, in order to keep the label short, and finally the exact label for the
     * function that we are displaying.
     */

    /* We usually display the menu links in a standard order
     * An optional Overview link -
     *  - overview shows by default immediately admin chooses the module with overviews switched on
     *    but it is useful to have it show as a menu item also when overviews are switched off
     *    so that it is still accessible without having to switch the overviews back on in Adminpanels
     * Add items link
     * View with edit/delete item link
     * Modify Config Link usually comes last in the menu
     */

    /* Show an overview menu option here if you like */

  
    if (xarSecurityCheck('AddMP3Jukebox', 0)) {

        $menulinks[] = array('url' => xarModURL('mp3jukebox','admin','new'),
            'title' => xarML('Adds a new song.'),
            'label' => xarML('New Song'));
    }
    /* Security Check */
    if (xarSecurityCheck('EditMP3Jukebox', 0)) {
        /* We do the same for each new menu item that we want to add to our admin panels.
         * This creates the tree view for each item. Obviously, we don't need to add every
         * function, but we do need to have a way to navigate through the module.
         */
        $menulinks[] = array('url' => xarModURL('mp3jukebox','admin','view'),
            'title' => xarML('View Songs.'),
            'label' => xarML('Manage Songs'));
        $menulinks[] = array('url' => xarModURL('mp3jukebox','admin','viewplaylist'),
            'title' => xarML('View Playlists.'),
            'label' => xarML('Manage Playlists'));
    }
    /* Security Check */
    if (xarSecurityCheck('AdminMP3Jukebox', 0)) {
        /* We do the same for each new menu item that we want to add to our admin panels.
         * This creates the tree view for each item. Obviously, we don't need to add every
         * function, but we do need to have a way to navigate through the module.
         */
        $menulinks[] = array('url' => xarModURL('mp3jukebox','admin','modifyconfig'),
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'));
    }
    if (xarSecurityCheck('ViewMP3Jukebox', 0)) {
        /* We do the same for each new menu item that we want to add to our admin panels.
         * This creates the tree view for each item. Obviously, we don't need to add every
         * function, but we do need to have a way to navigate through the module.
         */
        $menulinks[] = array('url' => xarModURL('mp3jukebox','admin','overview'),
            'title' => xarML('Read a general description of MP3 Jukebox'),
            'label' => xarML('Overview'));
    }
    /* If we return nothing, then we need to tell PHP this, in order to avoid an ugly
     * E_ALL error.
     */
    if (empty($menulinks)) {
        $menulinks = '';
    }
    /* The final thing that we need to do in this function is return the values back
     * to the main menu for display.
     */
    return $menulinks;
}
?>
