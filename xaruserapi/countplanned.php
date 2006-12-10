<?php
/**
 * Utility function counts number planned courses
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * utility function to count the number of planned courses
 *
 * @author the Courses module development team
 * @param $catid Category id.
 * @return integer. Number of items held by this module
 * @throws DATABASE_ERROR
 */
function courses_userapi_countplanned($args)
{
    extract ($args);
    if (!xarVarFetch('catid', 'int:1:', $catid, '', XARVAR_DONT_SET)) return;
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $planningtable = $xartable['courses_planning'];

    // Set to be able to see all courses or only non-hidden ones
    if (xarSecurityCheck('AdminCourses', 0)) {
    $where = "0, 1";
    } else {
    $where = "0";
    }

    $query = "SELECT COUNT(*) ";
    // Include category navigation possibility
    if (!empty($catid) && xarModIsHooked('categories','courses', 2)) {
        // Get the LEFT JOIN ... ON ...  and WHERE parts from categories
        $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                                       array('modid' => xarModGetIDFromName('courses'),
                                             'catid' => $catid));
        if (!empty($categoriesdef)) {
            $query .= " FROM ($planningtable
                        LEFT JOIN $categoriesdef[table]
                        ON $categoriesdef[field] = xar_planningid )
                        $categoriesdef[more]
                        WHERE $categoriesdef[where]
                        AND xar_hideplanning in ($where)";
            } else {
                $query .= " FROM $planningtable
                            WHERE xar_hideplanning in ($where)";
            }
     } else {
        $query .= " FROM $planningtable
                    WHERE xar_hideplanning in ($where)";
     }

    $result = &$dbconn->Execute($query);
    if (!$result) return;

    list($numitems) = $result->fields;
    $result->Close();

    return $numitems;
}

?>
