<?php
/**
 * Get all example items
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 * @link http://xaraya.com/index.php/release/67.html
 * @author Todolist Module Development Team
 */

/**
 * Get all projects
 * 
 * @author the Todolist module development team 
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function todolist_userapi_getallprojects($args)
{ 
    extract($args);
    /* Optional arguments.*/
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    /* Argument check
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
    if (!xarSecurityCheck('ViewTodolist')) return;
    /* Get database setup
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table definitions you are
     * using - $table doesn't cut it in more complex modules
     */
    $projectstable = $xartable['todolist_projects'];

    $query = "SELECT xar_project_id,
                    xar_project_name,
                    xar_description,
                    xar_project_leader
              FROM $projectstable
              ORDER BY xar_project_id";
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Put items into result array.
     */
    for (; !$result->EOF; $result->MoveNext()) {
        list($project_id, $project_name, $description, $project_leader) = $result->fields;
        if (xarSecurityCheck('ViewTodolist', 0, 'Item', "All:All:All")) { // TODO
            $items[] = array('project_id'     => $project_id,
                             'project_name'   => $project_name,
                             'description'    => $description,
                             'project_leader' => $project_leader);
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