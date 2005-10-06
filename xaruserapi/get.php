<?php
/**
 * Get a specific item
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 */

/**
 * Get a todo
 * 
 * Standard function of a module to retrieve a specific item
 *
 * @author the Todolist module development team
 * @param  $args ['todo_id'] id of example item to get
 * @returns array
 * @return item array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function todolist_userapi_get($args)
{
    extract($args);

    if (!isset($todo_id) || !is_numeric($todo_id)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'get', 'Todolist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $todostable = $xartable['todolist_todos'];

    /* Get item */
    $query = "SELECT 
            xar_project_id,
            xar_todo_text,
            xar_todo_priority,
            xar_percentage_completed,
            xar_created_by,
            xar_due_date,
            xar_date_created,
            xar_date_changed,
            xar_changed_by,
            xar_status
              FROM $todostable
              WHERE xar_todo_id = ?";
    $result = &$dbconn->Execute($query,array($exid));
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Check for no rows found, and if so, close the result set and return an exception */
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }
    /* Obtain the item information from the result set */
    list($project_id,
            $todo_text,
            $todo_priority,
            $percentage_completed,
            $created_by,
            $due_date,
            $date_created,
            $date_changed,
            $changed_by,
            $status) = $result->fields;
    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close();
    /* Security check
     */
    if (!xarSecurityCheck('ReadTodolist', 1, 'Item', "All:All:All")) { //TODO
        return;
    }
    /* Create the item array */
    $item = array('todo_id'                 => $todo_id,
                  'project_id'              => $project_id,
                  'todo_text'               => $todo_text,
                  'todo_priority'           => $todo_priority,
                  'percentage_completed'    => $percentage_completed,
                  'created_by'              => $created_by,
                  'due_date'                => $due_date,
                  'date_created'            => $date_created,
                  'date_changed'            => $date_changed,
                  'changed_by'              => $changed_by,
                  'status'                  => $status
                  );
    /* Return the item array */
    return $item;
}
?>