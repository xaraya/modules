<?php
/**
 * Utility function counts number of items held by this module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage example
 * @author Courses module development team
 */
/**
 * utility function to count the number of participants per planned course
 *
 * @author MichelV <michelv@xarayahosting.nl>
 *
 * @param planningid ID for the course
 * @return integer. Number of participants
 * @raise DATABASE_ERROR
 */
function courses_userapi_countparticipants($args)
{
    extract ($args);
    if (!xarVarFetch('planningid', 'id', $planningid)) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $studentstable = $xartable['courses_students'];
    $query = "SELECT COUNT(*)
              FROM $studentstable
              WHERE xar_planningid = $planningid";
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // Obtain the number of items
    list($numitems) = $result->fields;
    $result->Close();

    return $numitems;
}
?>
