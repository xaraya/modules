<?php
/**
 * Get all todo items
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 * @link http://xaraya.com/index.php/release/67.html
 * @author Todolist Module Development Team
 */

/**
 * Get all example items
 * 
 * @author the Example module development team 
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function todolist_userapi_getall($args)
{ 
    extract($args);
    /* Optional arguments.
     */
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    // Argument check
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'getall', 'Todolist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();
    /* Security check - important to do this as early on as possible to
     * avoid potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('ViewTodolist')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $todostable = $xartable['todolist_todos'];
    
    /* TODO: how to select by cat ids (automatically) when needed ???
     * Get items - the formatting here is not mandatory, but it does make the
     * SQL statement relatively easy to read.  Also, separating out the sql
     * statement from the SelectLimit() command allows for simpler debug
     * operation if it is ever needed
     */
    $query = "SELECT xar_todo_id,
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
              ORDER BY xar_todo_id";
    /* SelectLimit also supports bind variable, they get to be put in
     * as the last parameter in the function below. In this case we have no
     * bind variables, so we left the parameter out. We could have passed in an
     * empty array though.
     */
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Put items into result array.  Note that each item is checked
     * individually to ensure that the user is allowed *at least* OVERVIEW
     * access to it before it is added to the results array.
     * If more severe restrictions apply, e.g. for READ access to display
     * the details of the item, this *must* be verified by your function.
     */
    for (; !$result->EOF; $result->MoveNext()) {
        list($todo_id,
            $project_id,
            $todo_text,
            $todo_priority,
            $percentage_completed,
            $created_by,
            $due_date,
            $date_created,
            $date_changed,
            $changed_by,
            $status) = $result->fields;
        if (xarSecurityCheck('ViewTodolist', 0, 'Item', "All:All:All")) { //TODO
            $items[] = array('todo_id' => $todo_id,
                            'project_id' => $project_id,
                            'todo_text' => $todo_text,
                            'todo_priority' => $todo_priority,
                            'percentage_completed' => $percentage_completed,
                            'created_by' => $created_by,
                            'due_date' => $due_date,
                            'date_created' => $date_created,
                            'date_changed' => $date_changed,
                            'changed_by' => $changed_by,
                            'status' => $status);
        }
    }
    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close(); 
    /* Return the items */
    return $items;
} 
?>