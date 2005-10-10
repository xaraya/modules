<?php
/**
 * Standard function to update a current item
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 */
/**
 * Update a group
 *
 * This function is called with the results of the
 * form supplied by xarModFunc('example','admin','modify') to update a current item
 * 
 * @author Todolist module development team
 * @param  $ 'group_id' the id of the item to be updated
 * @param  $ 'group_name' the name of the item to be updated
 * @param  $ 'group_description' the description of the item to be updated
 * @param  $ 'number' the number of the item to be updated
 *
 * @TODO MichelV See if we need an update on other objects as well
 */
function todolist_adminapi_updategroup($args)
{ 
    extract($args);

    $invalid = array();
    if (!isset($group_id) || !is_numeric($group_id)) {
        $invalid[] = 'item ID';
    }
    if (!isset($group_name) || !is_string($group_name)) {
        $invalid[] = 'Group_Name';
    }
    if (!isset($group_description) || !is_string($group_description)) {
        $invalid[] = 'Group_Description';
    }
    if (!isset($group_leader) || !is_numeric($group_leader)) {
        $invalid[] = 'group_leader';
    }

    /* check if we have any errors */
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update', 'Todolist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('todolist',
        'user',
        'getgroup',
        array('group_id' => $group_id)); 
    /*Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditTodolist', 1, 'Item', "All:All:All")) {
        return;
    }
    /* Get database setup
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $groupstable = $xartable['todolist_groups'];

    /* Update the item - the formatting here is not mandatory, but it does
     * make the SQL statement relatively easy to read.  Also, separating
     * out the sql statement from the Execute() command allows for simpler
     * debug operation if it is ever needed
     */
    $query = "UPDATE $groupstable
            SET xar_group_name =?,
               xar_description =?,
               xar_group_leader =?
            WHERE xar_group_id = ?";
    $bindvars = array($group_name, $group_description, $group_leader, $group_id);
    $result = &$dbconn->Execute($query,$bindvars);
    /* 

    // update group-members... Is there a more elegant way to do this?
    // do we have to delete the tasks where someone is assigned who is no longer
    // member of the group?
    $todolist_group_members_column = &$pntable['todolist_group_members_column'];
    $query = "DELETE from $pntable[todolist_group_members]
                      WHERE $todolist_group_members_column[group_id]=$group_id";
    $result = $dbconn->Execute($query);
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Delete error occured'));
        return false;
    }

    if (sizeof($new_group_members) > 0) {
        $query="INSERT INTO $pntable[todolist_group_members] VALUES ";
        
        while ($member_id=array_pop($new_group_members)){
            $query .= "($group_id, $member_id)";
            if (sizeof($new_group_members) > 0)
                $query .= ',';
        }
    }
    $result = $dbconn->Execute("$query");
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Insert error occured'));
        return false;
    }

    pnSessionSetVar('errormsg', xarML('Group was updated'));

     */
    if (!$result) return;
    /* Let any hooks know that we have updated an item.  As this is an
     * update hook we're passing the updated $item array as the extra info
     */
    $item['module'] = 'todolist';
    $item['itemid'] = $group_id;
    $item['group_name'] = $group_name;
    $item['group_description'] = $group_description;
    $item['group_leader'] = $group_leader;
    xarModCallHooks('item', 'update', $group_id, $item);
    
    /* Let the calling process know that we have finished successfully */
    return true;
} 
