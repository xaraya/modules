<?php
/**
 * Utility function counts number of items held by this module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * utility function to count the number of courses present.
 * The function takes the categories and hidden courses into account.
 *
 * @author the Courses module development team
 * @param $catid Category id.
 * @return integer. Number of items held by this module
 * @throws DATABASE_ERROR
 */
function courses_userapi_countitems($args)
{
    extract ($args);
    if (!xarVarFetch('catid', 'int:1:', $catid, '', XARVAR_DONT_SET)) return;
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $coursestable = $xartable['courses'];

    // Set to be able to see all courses or only non-hidden ones
    if (xarSecurityCheck('AdminCourses', 0)) {
    $where = "0, 1";
    } else {
    $where = "0";
    }

    $query = "SELECT COUNT(*) ";

    // Categories
    if (!empty($catid) && xarModIsHooked('categories','courses')) {
        // Get the LEFT JOIN ... ON ...  and WHERE parts from categories
        $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                                       array('modid' => xarModGetIDFromName('courses'),
                                             'catid' => $catid));
        if (!empty($categoriesdef)) {
            $query .= " FROM ($coursestable
                        LEFT JOIN $categoriesdef[table]
                        ON $categoriesdef[field] = xar_courseid )
                        $categoriesdef[more]
                        WHERE $categoriesdef[where]
                        AND xar_hidecourse in ($where)";
            } else {
                $query .= " FROM $coursestable
                            WHERE xar_hidecourse in ($where)";
            }
     } else {
        $query .= " FROM $coursestable
                    WHERE xar_hidecourse in ($where)";
     }

    $result = &$dbconn->Execute($query);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
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
