<?php
/**
 * Modify a group
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 */

/**
 * Modify a group
 *
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 *
 * @author Todolist Module Development Team
 * @param  $ 'exid' the id of the item to be modified
 */
function todolist_admin_modifygroup($args)
{ 
    extract($args);

    /* Get parameters from whatever input we need. 
     */
    if (!xarVarFetch('group_id', 'int:1:', $group_id)) return;
    if (!xarVarFetch('objectid', 'int:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str:1:', $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('group_description', 'str::', $group_description, $group_description,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('group_name', 'str:1:', $group_name, $group_name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('group_leader', 'int:1:', $group_leader, '', XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $group_id = $objectid;
    }

    $item = xarModAPIFunc('todolist',
                          'user',
                          'getgroup',
                          array('group_id' => $group_id));
    
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    /* Security check
     */
    if (!xarSecurityCheck('EditTodolist', 1, 'Item', "All:All:All")) {//TODO
        return;
    }
    /* Get menu variables - it helps if all of the module pages have a standard
     * menu at their head to aid in navigation
     * $menu = xarModAPIFunc('example','admin','menu','modify');
     */
    $group_members = xarModAPIFunc('todolist','user','getgroupmembers',
                       array('group_id' => $group_id));
    $leaderid = $item['group_leader'];
    
    $item['module'] = 'todolist';
    $hooks = xarModCallHooks('item', 'modify', $group_id, $item);

    /* Return the template variables defined in this function */
    return array('authid'       => xarSecGenAuthKey(),
                 'group_name'         => $group_name,
                 'group_description'       => $group_description,
                 'invalid'      => $invalid,
                 'hookoutput'   => $hooks,
                 'hooks'        => '',
                 'item'         => $item,
                 'group_members'=> $group_members,
                 'groupleader'  => $item['group_leader'],
                 'groupleadername' => xarUserGetVar('name', $leaderid)
                 ); 
}
?>