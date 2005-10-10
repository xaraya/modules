<?php
/**
 * Utility function to count the number of items held by this module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 */

/**
 * Utility function to count the number of items held by this module
 * 
 * @author the Example module development team 
 * @returns integer
 * @return number of items held by this module
 * @raise DATABASE_ERROR
 */
function todolist_userapi_countusers()
{ 
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table and column definitions you are
     * getting - $table and $column don't cut it in more complex modules
     */
    $project_memberstable = $xartable['todolist_project_members'];
    /* Get item distinct
     * Should this be per project?
     */
    $query = "SELECT DISTINCT xar_member_id
            FROM $project_memberstable";
    /* If there are no variables you can pass in an empty array for bind variables
     * or no parameter.
     */
    $result = &$dbconn->Execute($query,array());
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Obtain the number of items */
    $numitems = $result->PO_RecordCount();
    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close();
    /* Return the number of items */
    return $numitems;
}
?>