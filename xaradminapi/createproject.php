<?php
/**
 * Create a new example item
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 */

/**
 * Create a new example item
 *
 * This is a standard adminapi function to create a module item
 *
 * @author the Example module development team
 * @param  $args ['name'] name of the item
 * @param  $args ['number'] number of the item
 * @returns int
 * @return example item ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function todolist_adminapi_createproject($args)
{ 
    extract($args);
    /* Argument check
     */
    $invalid = array();
    if (!isset($project_name) || !is_string($project_name)) {
        $invalid[] = 'project_name';
    }
    if (!isset($project_description) || !is_string($project_description)) {
        $invalid[] = 'project_description';
    }
    if (!isset($project_leader) || !is_numeric($project_leader)) {
        $invalid[] = 'project_leader';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'createproject', 'Todolist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* Security check - important to do this as early on as possible to
     * avoid potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('AddTodolist', 1, 'Item', "All:All:All")) {//TODO
        return;
    } 
    /* Insert group */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $projectstable = $xartable['todolist_projects'];
    /* Next id */
    $nextId = $dbconn->GenId($projectstable);
    /* Add item
     */
    $query = "INSERT INTO $projectstable
            xar_id,
            xar_project_name,
            xar_description,
            xar_project_leader
            VALUES (?,?,?,?)";

    $bindvars = array($nextId, (string) $project_name, $project_description, $project_leader);
    $result = &$dbconn->Execute($query,$bindvars);


    if (!$result) return;
    
    /* Get the ID of the item that we inserted.  It is possible, depending
     * on your database, that this is different from $nextId as obtained
     * above, so it is better to be safe than sorry in this situation
     */
    $exid = $dbconn->PO_Insert_ID($projectstable, 'xar_id');
    
    /* Let any hooks know that we have created a new item.
     * TODO: include itemtype here
     * 
     */
    $item = $args;
    $item['module'] = 'todolist';
    $item['itemid'] = $project_id;
    xarModCallHooks('item', 'create', $project_id, $item);
    /* Return the id of the newly created item to the calling process */
    return $exid;
}
?>