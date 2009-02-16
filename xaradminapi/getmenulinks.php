<?php
/**
 * Pass individual menu items to the admin menu
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
 * Pass individual menu items to the admin  menu
 * This function may be called for in-page admin menu items too
 *
 * @author the Example module development team
 * @return array containing the menulinks for the main and the in-page admin menus.
 */
function example_adminapi_getmenulinks()
{
    /* The main menu will look for this  menulinks array and return it for a tree view of the module
     * We are just looking for three items in the array, the url, which we need to use the
     * xarModURL function, the title of the link, which will display a tool tip for the
     * module url, in order to keep the label short, and finally the exact label for the
     * function that we are displaying.
     * The fourth item 'active' is optional and controls on which module functions the
     * menulink is highlighted in the in-page menus.
     */

    /* We usually display the menu links in a standard order
     * View with edit/delete item link
     * Add items link
     * Modify Config Link usually comes last in the menu
     * An optional Overview link -
     *  - overview shows mostly by default when chooses the module with overviews switched on
     *    but it is useful to have it show as a menu item also when overviews are switched off
     *    so that it is still accessible without having to switch the overviews back on in Adminpanels
     */

    /* We must return at least an empty array for minimizing checks in the
     * templates and to avoid an ugly E_ALL error.
     */
    static $menulinks = array();

    /* This function is typically called twice during a page request. So we check 
     * if we have already the result.
     */
    if (isset($menulinks[0])) {
        return $menulinks;
    }

    /* First we need to do a security check to ensure that we only return menu items
     * that we are suppose to see. It will be important to add for each menu item that
     * you want to filter. No sense in someone seeing a menu link that they have no access
     * to edit. Notice that we are checking to see that the user has permissions, and
     * not that he/she doesn't.
     */
    if (xarSecurityCheck('EditExample', 0)) {
        /* We do the same for each new menu item that we want to add to our admin panels.
         * This creates the tree view for each item. Obviously, we don't need to add every
         * function, but we do need to have a way to navigate through the module.
         */
        $menulinks[] = array('url' => xarModURL('example','admin','view'),
            'title' => xarML('View example item, with options to modify and delete them.'),
            'label' => xarML('Manage Items'),
            'active' => array('view'));
    }
    /* Security Check against a more powerful mask */
    if (xarSecurityCheck('AddExample', 0)) {
        $menulinks[] = array('url' => xarModURL('example','admin','new'),
            /* In order to display the tool tips and label in any language,
             * we must encapsulate the calls in the xarML in the API.
             */
            'title'  => xarML('Adds a new item to system.'),
            'label'  => xarML('Add Item'),
            'active' => array('new'));
    }
    /* Security Check against the mask with the most powerful privilege level */
    if (xarSecurityCheck('AdminExample', 0)) {
        $menulinks[] = array('url' => xarModURL('example','admin','modifyconfig'),
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'),
            'active' => array('modifyconfig'));
        /* Two menuitems use only one security check here */
        $menulinks[] = array('url' => xarModURL('example','admin','overview'),
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Overview'),
            /* The overview page is mostly the default view for the admin type.
             * We need it highlighted on 'main' and on 'overview'. */
            'active' => array('main', 'overview'));
    }

    /* The final thing that we need to do in this function is return the values back
     * to the main menu for display.
     */
    return $menulinks;
}
?>
