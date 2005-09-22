<?php
/**
 * File: $Id:
 * 
 * Utility function counts number of items held by this module
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
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
    $studentstable = $xartable['courses_students'];
    $query = "SELECT COUNT(*)
            FROM $studentstable
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
