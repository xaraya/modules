<?php
/**
 * Modify an project
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 */

/**
 * Modify an project
 *
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 *
 * @author Todolist Module Development Team
 * @param  $ 'project_id' the id of the item to be modified
 */
function todolist_admin_modifyproject($args)
{ 

    extract($args);

    if (!xarVarFetch('project_id', 'int:1:', $project_id)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str:1:', $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('project_description', 'str:1:', $project_description, $project_description,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('project_name', 'str:1:', $project_name, $project_name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('project_leader', 'int:1:', $project_leader, $project_leader,XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $project_id = $objectid;
    }
    // Get Project by ID
    $item = xarModAPIFunc('todolist',
                          'user',
                          'getproject',
                          array('project_id' => $project_id));
    
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    /* Security check on This Project */
    if (!xarSecurityCheck('EditTodolist', 1, 'Item', "All:All:All")) { // TODO
        return;
    }
    $project_members = xarModAPIFunc('todolist','user','getprojectmembers',
                                      array('project_id' => $project_id));    
/*
    // Project members
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Project members')));
    $row[] = $output->Text(makeUserDropdownList("new_project_members",$project_members,"all",false,true,''));
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->TableAddrow($row, 'LEFT');
    $output->SetInputMode(_PNH_PARSEINPUT);
*/
    $leaderid = $item['project_leader'];
    $project_leader = xarUserGetVar('name', $leaderid);

    /* Get menu variables - it helps if all of the module pages have a standard
     * menu at their head to aid in navigation
     * $menu = xarModAPIFunc('example','admin','menu','modify');
     */
    $item['module'] = 'todolist';
    $hooks = xarModCallHooks('item', 'modify', $project_id, $item);

    /* Return the template variables defined in this function */
    return array('authid'       => xarSecGenAuthKey(),
                 'project_name'         => xarVarPrepForDisplay($item['project_name']),
                 'project_description'  => xarVarPrepForDisplay($item['project_description']),
                 'invalid'      => $invalid,
                 'hookoutput'   => $hooks,
                 'hooks'        => '',
                 'item'         => $item,
                 'title'        => xarML('Edit Project'));
}
?>