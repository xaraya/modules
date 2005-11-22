<?php
/**
 * Get all example items
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 */

/**
 * Get all groups
 *
 * @author the Example module development team
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function todolist_userapi_getallgroups($args)
{
    extract($args);
    /* Optional arguments.
     * FIXME: (!isset($startnum)) was ignoring $startnum as it contained a null value
     * replaced it with ($startnum == "") (thanks for the talk through Jim S.) NukeGeek 9/3/02
     * if (!isset($startnum)) { */
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
            join(', ', $invalid), 'user', 'getallgroups', 'Todolist');
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
    /* It's good practice to name the table definitions you are
     * using - $table doesn't cut it in more complex modules
     */
    $groupstable = $xartable['todolist_groups'];

    $query = "SELECT xar_id,
               xar_group_name,
               xar_description,
               xar_group_leader
              FROM $groupstable
              ORDER BY xar_group_name";
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
    /* Put items into result array.
     */
    for (; !$result->EOF; $result->MoveNext()) {
        list($groupid,$group_name,$description,$group_leader) = $result->fields;
        if (xarSecurityCheck('ViewTodolist', 0, 'Item', "All:All:All")) { // TODO
            $items[] = array('groupid' =>$groupid,
                             'group_name' => $group_name,
                             'description' => $description,
                             'group_leader' => $group_leader);
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