<?php
/**
 * Standard Utility function pass individual menu items to the main menu
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 */

/**
 * Standard Utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function todolist_adminapi_getmenulinks()
{
    /* First we need to do a security check to ensure that we only return menu items
     * that we are suppose to see.  It will be important to add for each menu item that
     * you want to filter.  No sense in someone seeing a menu link that they have no access
     * to edit.  Notice that we are checking to see that the user has permissions, and
     * not that he/she doesn't.
     * Security Check
     */

    /* The main menu will look for this  menulinks array and return it for a tree view of the module
// Main administration menu

    $output = new pnHTML();

    $authid = pnSecGenAuthKey();

    if(!(pnSecAuthAction(0, 'todolist::', '::', ACCESS_EDIT))) {
    $output->Text(xarML('Not authorised to access Todolist module'));
        return $output->GetOutput();
    }

    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(pnGetStatusMsg());
    $output->Linebreak();

    if (!pnModAPILoad('todolist', 'admin')) {
        $output->Text(xarML('Load of module failed'));
        return $output->GetOutput();
    }

    $output->TableStart(xarML('Todolist administration'), '', 1);
    $output->TableRowStart();
    $output->TableColStart();
    $output->URL(pnModURL('todolist','admin','viewusers'),xarML('Users'));
    $output->TableColEnd();
    $output->TableColStart();
    $output->URL(pnModURL('todolist','admin','viewgroups'),xarML('Groups'));
    $output->TableColEnd();
    $output->TableColStart();
    $output->URL(pnModURL('todolist','admin','viewprojects'),xarML('Projects'));
    $output->TableColEnd();
    $output->TableColStart();
    $output->URL(pnModURL('todolist','admin','modifyconfig'),xarML('Edit Configuration'));
    $output->TableColEnd();
    $output->TableRowEnd();
    $output->TableEnd();
    $output->SetInputMode(_PNH_PARSEINPUT);
    return $output->GetOutput();

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

    if (xarSecurityCheck('AddTodoList', 0)) {
    $menulinks[] = Array('url' => xarModURL('example','admin','overview'),

            'title' => xarML('TodoList Overview'),
            'label' => xarML('Overview'));
    }

    if (xarSecurityCheck('AddTodoList', 0)) {

        $menulinks[] = Array('url' => xarModURL('example','admin','new'),
            /* In order to display the tool tips and label in any language,
             * we must encapsulate the calls in the xarML in the API.
             */
            'title' => xarML('Adds a new item to system.'),
            'label' => xarML('Add Item'));
    }
    /* Security Check */
    if (xarSecurityCheck('EditTodoList', 0)) {
        /* We do the same for each new menu item that we want to add to our admin panels.
         * This creates the tree view for each item.  Obviously, we don't need to add every
         * function, but we do need to have a way to navigate through the module.
         */
        $menulinks[] = Array('url' => xarModURL('example','admin','view'),
            /* In order to display the tool tips and label in any language,
             * we must encapsulate the calls in the xarML in the API.
             */
            'title' => xarML('View all example items that have been added.'),
            'label' => xarML('View Items'));
    }
    /* Security Check */
    if (xarSecurityCheck('AdminTodoList', 0)) {
        /* We do the same for each new menu item that we want to add to our admin panels.
         * This creates the tree view for each item.  Obviously, we don't need to add every
         * function, but we do need to have a way to navigate through the module.
         */
        $menulinks[] = Array('url' => xarModURL('example','admin','modifyconfig'),
            /* In order to display the tool tips and label in any language,
             * we must encapsulate the calls in the xarML in the API.
             */
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'));
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