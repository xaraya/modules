<?php
/**
 * Update a project
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
/**
 * Update an example item
 * 
 * @author the Example module development team 
 * @param  $args ['exid'] the ID of the item
 * @param  $args ['name'] the new name of the item
 * @param  $args ['number'] the new number of the item
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function todolist_adminapi_updateproject($args)
{ 
    /* Get arguments from argument array - all arguments to this function

    $todolist_projects_column = &$pntable['todolist_projects_column'];
    $query = "UPDATE $pntable[todolist_projects] SET
                     $todolist_projects_column[project_name]='$project_name',
                     $todolist_projects_column[description]='$project_description',
                     $todolist_projects_column[project_leader]=$project_leader
                     WHERE $todolist_projects_column[id] = $project_id";
    $result = $dbconn->Execute($query);
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Update error occured'));
        return false;
    }
    // update project-members... Is there a more elegant way to do this?
    // do we have to delete the tasks where someone is assigned who is no longer
    // member of the project?
    $todolist_project_members_column = &$pntable['todolist_project_members_column'];
    $query = "DELETE FROM $pntable[todolist_project_members]
              WHERE $todolist_project_members_column[project_id] = $project_id";
    $result = $dbconn->Execute($query);
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Delete error occured'));
        return false;
    }
    if (sizeof($project_members) > 0) {
        $query="INSERT INTO $pntable[todolist_project_members] VALUES ";
         
        while ($member_id=array_pop($project_members)){
            $query .= "($project_id, $member_id)";
            if (sizeof($project_members) > 0)
                $query .= ',';
        }
    }
    $result = $dbconn->Execute("$query");
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Insert error occured'));
        return false;
    }
  
    pnSessionSetVar('errormsg', xarML('Project was updated'));
    return true;
}
     * assumptions that will not hold in future versions of Xaraya
     */
    extract($args);
    /* Argument check  */
    $invalid = array();
    if (!isset($project_id) || !is_numeric($project_id)) {
        $invalid[] = 'item ID';
    }

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
            join(', ', $invalid), 'admin', 'updateproject', 'Todolist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // The user API function is called. Get the current project
    $item = xarModAPIFunc('todolist',
        'user',
        'getproject',
        array('project_id' => $project_id)); 
    /*Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    /* Security check - important to do this as early on as possible to
     * avoid potential security holes or just too much wasted processing.
     * However, in this case we had to wait until we could obtain the item
     * name to complete the instance information so this is the first
     * chance we get to do the check
     * Note that at this stage we have two sets of item information, the
     * pre-modification and the post-modification.  We need to check against
     * both of these to ensure that whoever is doing the modification has
     * suitable permissions to edit the item otherwise people can potentially
     * edit areas to which they do not have suitable access
     */
    if (!xarSecurityCheck('EditExample', 1, 'Item', "$item[name]:All:$exid")) {
        return;
    }
    if (!xarSecurityCheck('EditExample', 1, 'Item', "$name:All:$exid")) {
        return;
    }
    /* Get database setup - note that both xarDBGetConn() and xarDBGetTables()
     * return arrays but we handle them differently.  For xarDBGetConn()
     * we currently just want the first item, which is the official
     * database handle.  For xarDBGetTables() we want to keep the entire
     * tables array together for easy reference later on
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table and column definitions you
     * are getting - $table and $column don't cut it in more complex
     * modules
     */
    $projectstable = $xartable['todolist_projects'];
    /* Update the item - the formatting here is not mandatory, but it does
     * make the SQL statement relatively easy to read.  Also, separating
     * out the sql statement from the Execute() command allows for simpler
     * debug operation if it is ever needed
     */
    $query = "UPDATE $projectstable
            SET xar_name =?, xar_number = ?
            WHERE xar_exid = ?";
    $bindvars = array($name, $number, $exid);
    $result = &$dbconn->Execute($query,$bindvars);
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Let any hooks know that we have updated an item.  As this is an
     * update hook we're passing the updated $item array as the extra info
     */
    $item['module'] = 'example';
    $item['itemid'] = $exid;
    $item['name'] = $name;
    $item['number'] = $number;
    xarModCallHooks('item', 'update', $exid, $item);
    
    /* Let the calling process know that we have finished successfully */
    return true;
} 
?>