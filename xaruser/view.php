<?php
/**
 * View a list of items
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Todolist Module Development Team
 */

/**
 * View a list of todos and tasks
 *
 * This is a standard function to provide an overview of all of the items
 * available from the module.
 *
 * @author the Todolist module development team
 * @TODO MichelV Apply filtering
 *               Define what to view
 */
function todolist_user_view()
{ 
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    /* Initialise the $data variable that will hold the data to be used in
     * the blocklayout template, and get the common menu configuration
     */

    $data = xarModAPIFunc('todolist', 'user', 'menu');
    /* Prepare the variable that will hold some status message if necessary */
    $data['status'] = '';
    /* Prepare the array variable that will hold all items for display */
    $data['items'] = array();
    /* Specify some other variables for use in the function template */
    $data['pager'] = '';
    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('ViewTodolist')) return;
    /* Lets get the UID of the current user to check for overridden defaults */
    $uid = xarUserGetVar('uid');
    /* Get all Todos.
     * Later build in more specific filters here
     */
    $items = xarModAPIFunc('todolist',
        'user',
        'getall',
        array('startnum' => $startnum,
              'numitems' => xarModGetUserVar('todolist','itemsperpage', $uid)));
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    /* 
     * Loop through each item and display it.
     */
    foreach ($items as $item) {
        /* 
         * Note : for your module, you might not want to call transformation
         * hooks in this overview list, but only in the display of the details
         * in the display() function.
         * 
         */
        if (xarSecurityCheck('ReadTodolist', 0, 'Item', "All:All:All")) {//Todo
            $item['link'] = xarModURL('todolist',
                'user',
                'display',
                array('todo_id' => $item['todo_id']));
            /* Security check 2 - else only display the item name (or whatever is
             * appropriate for your module)
             */
        } else {
            $item['link'] = '';
        }
        /* Clean up the item text before display */
        //$item['name'] = xarVarPrepForDisplay($item['name']);
        
        /*
         * Get the project of this person
         * Get the groups
         */
        /* Add this item to the list of items to be displayed */
        $data['items'][] = $item;
    }
    /* Call the xarTPL helper function to produce a pager in case of there
     * being many items to display.
     *
     * Note that this function includes another user API function.  The
     * function returns a simple count of the total number of items in the item
     * table so that the pager function can do its job properly
     */
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('todolist', 'user', 'countitems'),
        xarModURL('todolist', 'user', 'view', array('startnum' => '%%')),
        xarModGetUserVar('todolist', 'itemsperpage', $uid));

    /* Same as above.  We are changing the name of the page to raise
     * better search engine compatibility.
     */
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('View Todos')));
    /* Return the template variables defined in this function */
    return $data;
}
?>