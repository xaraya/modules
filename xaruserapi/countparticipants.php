<?php
/**
 * File: $Id:
 * 
 * Utility function counts number of items held by this module
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage example
 * @author Courses module development team 
 */
/**
 * utility function to count the number of participants per planned course
 * 
 * @author Michel V.
 *
 * @param planningid ID for the course
 * @returns integer
 * @return number of participants
 * @raise DATABASE_ERROR
 */
function courses_userapi_countparticipants($args)
{
    extract ($args);
    if (!xarVarFetch('planningid', 'int:1:', $planningid)) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table and column definitions you are
    // getting - $table and $column don't cut it in more complex modules
    $planningtable = $xartable['courses_planning'];
    // Get item - the formatting here is not mandatory, but it does make the
    // SQL statement relatively easy to read.  Also, separating out the sql
    // statement from the Execute() command allows for simpler debug operation
    // if it is ever needed
    $query = "SELECT COUNT(1)
            FROM $planningtable
            WHERE xar_planningid = $planningid";
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
