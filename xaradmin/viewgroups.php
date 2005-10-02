<?php
/**
 * View groups
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 */

/**
 * Standard function to view groups
 *
 * @author Todolist module development team
 * @param int startnum
 * @return array
 */
function todolist_admin_viewgroups()
{ 
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    /* Initialise the $data variable that will hold the data to be used in
     * the blocklayout template, and get the common menu configuration - it
     * helps if all of the module pages have a standard menu at the top to
     * support easy navigation
     */
    $data = xarModAPIFunc('todolist', 'admin', 'menu');
    /* Initialise the variable that will hold the items, so that the template
     * doesn't need to be adapted in case of errors
     */
    $data['items'] = array();

    /* Call the xarTPL
     */
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('todolist', 'user', 'countgroups'),
        xarModURL('todolist', 'admin', 'viewgroups', array('startnum' => '%%')),
        xarModGetVar('todolist', 'itemsperpage'));
    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('EditTodolist')) return;
    // Labels
    $data['namelabel']        = xarVarPrepForDisplay(xarML('Group Name')
    $data['desciptionlabel']  = xarVarPrepForDisplay(xarML('Group description')
    $data['groupleaderlabel'] = xarVarPrepForDisplay(xarML('Group Leader')
    
    /* The user API function is called. */

    $items = xarModAPIFunc('todolist',
                           'user',
                           'getallgroups',
                            array('startnum' => $startnum,
                                  'numitems' => xarModGetVar('todolist','itemsperpage')));
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    /* Check individual permissions for Edit / Delete
     */
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        if (xarSecurityCheck('EditTodolist', 0, 'Item', "All:All:All")) {//TODO
            $items[$i]['editurl'] = xarModURL('todolist',
                'admin',
                'modifygroup',
                array('id' => $item['id']));
        } else {
            $items[$i]['editurl'] = '';
        }
        if (xarSecurityCheck('DeleteTodolist', 0, 'Item', "All:All:All")) {//TODO
            $items[$i]['deleteurl'] = xarModURL('todolist',
                'admin',
                'deletegroups',
                array('id' => $item['id']));
        } else {
            $items[$i]['deleteurl'] = '';
        }
    $item['group_name'] = xarVarPrepForDisplay($item['group_name']);
    $item['group_description'] = xarVarPrepForDisplay($item['group_description']);
    $item['group_leader_name'] = xarUserGetVar('name', $item['group_leader']);
    } 
    /* Add the array of items to the template variables */
    $data['items'] = $items;

    /* Return the template variables defined in this function */
    return $data;

}
?>