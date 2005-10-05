<?php
/**
 * Delete a group
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 */
/**
 * Delete a group
 *
 * Standard function to delete a module item
 *
 * @author the Example module development team 
 * @param  $args ['group_id'] ID of the item
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 * @TODO: We have to make user only ADMIN and group-leader can do that!
 * @TODO: MichelV <1> See if we delete the attached items here, or do it somewhere else...
 */
function todolist_adminapi_deletegroup($args)
{ 

    extract($args);

    if (!isset($group_id) || !is_numeric($group_id)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'admin', 'deletegroup', 'todolist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* The user API function is called.
     */
    $item = xarModAPIFunc('todolist',
        'user',
        'getgroup',
        array('group_id' => $group_id));
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    /* Security check
     */
    if (!xarSecurityCheck('DeleteTodolist', 1, 'Item', "All:All:All")) {//TODO
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $groupstable = $xartable['todolist_groups'];
    /* Delete the group
     */
    $query = "DELETE FROM $groupstable WHERE xar_group_id = ?";

    /* The bind variable $exid is directly put in as a parameter. */
    $result = &$dbconn->Execute($query,array($exid));
    
    if (!$result) return;
    /* 
     * Do we call these here again?
    $todolist_group_members_column = &$pntable['todolist_group_members_column'];
    $result = $dbconn->Execute("DELETE FROM $pntable[todolist_group_members]
        WHERE $todolist_group_members_column[group_id]=$group_id");
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Delete error occured'));
        return false;
    }

    $todolist_responsible_groups_column = &$pntable['todolist_responsible_groups_column'];
    $result = $dbconn->Execute("DELETE FROM $pntable[todolist_responsible_groups]
        WHERE $todolist_responsible_groups_column[group_id]=$group_id");
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Delete error occured'));
        return false;
    }
     * 
     */
    // HOOKS
    $item['module'] = 'todolist';
    $item['itemid'] = $group_id;
    xarModCallHooks('item', 'delete', $group_id, $item);
    
    /* Let the calling process know that we have finished successfully */
    return true;
}
?>