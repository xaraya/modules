<?php
/**
 * Count the number courses a user is a teacher in
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author Courses module development team 
 */
/**
 * utility function to count the number courses a user is a teacher in
 * 
 * @author Michel V.
 *
 * @param uid UserID for the user that is a teacher
 * @returns integer
 * @return number of courses
 * @raise DATABASE_ERROR
 */
function courses_userapi_countteaching($args)
{
    extract ($args);
    if (!xarVarFetch('uid', 'int:1:', $uid)) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table and column definitions you are
    // getting - $table and $column don't cut it in more complex modules
    $teacherstable = $xartable['courses_teachers'];
    $query = "SELECT COUNT(1)
            FROM $teacherstable
            WHERE xar_userid = $uid";
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    // Obtain the number of items
    list($numitems) = $result->fields;
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Return the number of items
    return $numitems;
}

?>
