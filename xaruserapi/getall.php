<?php
/**
 * Get all todos
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 */

/**
 * Get all todos
 * 
 * @author the Todolist module development team 
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function todolist_userapi_getall($args)
{ 
    /* Get arguments from argument array - all arguments to this function
     * should be obtained from the $args array, getting them from other places
     * such as the environment is not allowed, as that makes assumptions that
     * will not hold in future versions of Xaraya
     */
    extract($args);
    /* Optional arguments.*/
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    /* Argument check - make sure that all required arguments are present and
     * in the right format, if not then set an appropriate error message
     * and return
     * Note : since we have several arguments we want to check here, we'll
     * report all those that are invalid at the same time...
     */
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
    /* Get database setup - note that both xarDBGetConn() and xarDBGetTables()
     * return arrays but we handle them differently.  For xarDBGetConn() we
     * currently just want the first item, which is the official database
     * handle.  For xarDBGetTables() we want to keep the entire tables array
     * together for easy reference later on
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $todostable = $xartable['todolist_todos'];
    /* Get item */
    $query = "SELECT 
            xar_todo_id,
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
              FROM $todostable";
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
    // Put items into result array.
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
        if (xarSecurityCheck('ViewTodolist', 0, 'Item', "All:All:All")) {
            $items[] = array('todo_id'      => $todo_id,
                  'project_id'              => $project_id,
                  'todo_text'               => $todo_text,
                  'todo_priority'           => $todo_priority,
                  'percentage_completed'    => $percentage_completed,
                  'created_by'              => $created_by,
                  'due_date'                => $due_date,
                  'date_created'            => $date_created,
                  'date_changed'            => $date_changed,
                  'changed_by'              => $changed_by,
                  'status'                  => $status);
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