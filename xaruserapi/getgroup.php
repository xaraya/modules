<?php
/**
 * Get a specific item
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 */

/**
 * Get a specific item
 * 
 * Standard function of a module to retrieve a specific item
 *
 * @author the Todolist module development team
 * @param  $args ['exid'] id of example item to get
 * @returns array
 * @return item array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function todolist_userapi_getgroup($args)
{
    /* Get arguments from argument array - all arguments to this function


/**
 * get a specific group
 * @param $args['group_id'] id of group to get
 * @returns array
 * @return item array, or false on failure
 
    if (!isset($group_id)) {
        pnSessionSetVar('errormsg', xarML('Error in API arguments'));
        return false;
    }


    $todolist_groups_column = &$pntable['todolist_groups_column'];

    $sql = "SELECT $todolist_groups_column[id],$todolist_groups_column[group_name], 
         $todolist_groups_column[description],$todolist_groups_column[group_leader]
         FROM $pntable[todolist_groups] WHERE $todolist_groups_column[id] = ".
         pnVarPrepForStore($group_id);

    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    if ($result->EOF) {
        return false;
    }

    list($gid, $gname, $gdescription,$gleader) = $result->fields;

    $result->Close();

    if (!pnSecAuthAction(0, 'todolist::', "$gname::$gid", ACCESS_READ)) {
        return false;
    }

    $item = array('group_id' => $gid,
                  'group_name' => $gname,
                  'group_description' => $gdescription,
                  'group_leader' => $gleader);
    return $item;
}
     */
    extract($args);
    /* Argument check - make sure that all required arguments are present and
     * in the right format, if not then set an appropriate error message
     * and return
     */
    if (!isset($exid) || !is_numeric($exid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'get', 'Example');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* Get database setup - note that both xarDBGetConn() and xarDBGetTables()
     * return arrays but we handle them differently.  For xarDBGetConn() we
     * currently just want the first item, which is the official database
     * handle.  For xarDBGetTables() we want to keep the entire tables array
     * together for easy reference later on
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table and column definitions you are
     * getting - $table and $column don't cut it in more complex modules
     */
    $exampletable = $xartable['example'];
    /* Get item - the formatting here is not mandatory, but it does make the
     * SQL statement relatively easy to read.  Also, separating out the sql
     * statement from the Execute() command allows for simpler debug operation
     * if it is ever needed
     */
    $query = "SELECT xar_name,
                     xar_number
              FROM $exampletable
              WHERE xar_exid = ?";
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
    list($name, $number) = $result->fields;
    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close();
    /* Security check - important to do this as early on as possible to avoid
     * potential security holes or just too much wasted processing.  Although
     * this one is a bit late in the function it is as early as we can do it as
     * this is the first time we have the relevant information.
     * For this function, the user must *at least* have READ access to this item
     */
    if (!xarSecurityCheck('ReadExample', 1, 'Item', "$name:All:$exid")) {
        return;
    }
    /* Create the item array */
    $item = array('exid'   => $exid,
                  'name'   => $name,
                  'number' => $number);
    /* Return the item array */
    return $item;
}
?>