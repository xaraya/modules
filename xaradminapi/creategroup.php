<?php
/**
 * Create a new group
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 */

/**
 * Create a new group
 *
 * This is a standard adminapi function to create a group
 *
 * @author the Todolist module development team
 * @param  $args ['name'] name of the item
 * @param  $args ['number'] number of the item
 * @returns int
 * @return example item ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function todolist_adminapi_creategroup($args)
{ 
    extract($args);
    $invalid = array();
    
    if (!isset($group_name) || !is_string($group_name)) {
        $invalid[] = 'group_name';
    }
    if (!isset($group_description) || !is_string($group_description)) {
        $invalid[] = 'group_description';
    }
    if (!isset($group_leader) || !is_numeric($group_leader)) {
        $invalid[] = 'group_leader';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create', 'Todolist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    if (!xarSecurityCheck('AddTodolist', 1, 'Item', "All:All:All")) {//TODO
        return;
    } 
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $groupstable = $xartable['todolist_groups'];
    /* Get next ID in table - this is required prior to any insert that
     * uses a unique ID, and ensures that the ID generation is carried
     * out in a database-portable fashion
     */
    $nextId = $dbconn->GenId($groupstable);
    $query = "INSERT INTO $groupstable (
              xar_group_id,
              xar_group_name
              xar_description,
              xar_group_leader)
            VALUES (?,?,?,?)";

    $bindvars = array($nextId, (string) $group_name, $group_description, $group_leader);
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;
    
    $group_id = $dbconn->PO_Insert_ID($groupstable, 'xar_group_id');
    

    $item = $args;
    $item['module'] = 'todolist';
    $item['itemid'] = $group_id;
    xarModCallHooks('item', 'create', $group_id, $item);
    /* Return the id of the newly created item to the calling process */
    return $group_id;
}
?>