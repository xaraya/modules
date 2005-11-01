<?php
/**
 * Get a specific project
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 */

/**
 * Get a specific project
 * 
 * Standard function of a module to retrieve a specific item
 *
 * @author the Todolist module development team
 * @param $args['project_id'] id of project to get
 * @returns array
 * @return item array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function todolist_userapi_getproject($args)
{
    extract($args);

    if (!isset($project_id) || !is_numeric($project_id)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'get', 'Todolist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $projectstable = $xartable['todolist_projects'];
    /* Get item
     */

    $query = "SELECT xar_project_name,
                xar_description,
                xar_project_leader
              FROM $projectstable
              WHERE xar_id = ?";
    $result = &$dbconn->Execute($query,array($project_id));
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Check for no rows found, and if so, close the result set and return an exception */
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This project does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }
    /* Obtain the item information from the result set */
    list($project_name, $project_description, $project_leader) = $result->fields;
    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close();
    /* Security check  */
    if (!xarSecurityCheck('ReadTodolist', 1, 'Item', "All:All:All")) {//TODO
        return;
    }
    /* Create the item array */
    $item = array('project_id'   => $project_id,
                  'project_name'   => $project_name,
                  'project_description' => $project_description,
                  'project_leader' => $project_leader);
    /* Return the item array */
    return $item;
}
?>