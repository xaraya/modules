<?php
/**
 * Get all courses
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * get all courses
 *
 * @author the Courses module development team
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @param sortby $ the parameter to sort by (default name) enum 'name','shortdesc','number'
 * @param id catid category id
 * @param id level courselevelid
 * @param id type coursetypeid
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_userapi_getall($args)
{
    extract($args);
    if (!xarVarFetch('startnum', 'int:1:',         $startnum, 1,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'int:1:',         $numitems, -1,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('level',    'int:1:',         $level,    0,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('type',     'int:1:',         $type,     0,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('catid',    'int:1:',         $catid,    '',    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortby',   'str:1:',         $sortby,   'name',  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortorder','enum:DESC:ASC:', $sortorder,'DESC',  XARVAR_NOT_REQUIRED)) return;

    // Argument check
    $valid = array('name','shortdesc','number');
    if (!isset($sortby) || !in_array($sortby,$valid)) { // Should be orderby and then sortby ASC DESC
        $sortby = 'name';
    }

    $items = array();
    if (!xarSecurityCheck('ViewCourses')) return;
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
    $query = "SELECT xar_courseid,
                   $coursestable.xar_name,
                   xar_number,
                   xar_type,
                   xar_level,
                   xar_shortdesc,
                   xar_intendedcredits,
                   xar_freq,
                   xar_contact,
                   xar_contactuid,
                   xar_hidecourse,
                   xar_last_modified";


    // Category selection
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
    // Level selection
    if (($level > 0) && is_numeric($level)) {
        $query .= " AND xar_level = $level ";
    }
    // Level selection
    if (($type > 0) && is_numeric($type)) {
        $query .= " AND xar_type = $type ";
    }
    $query .= " ORDER BY $coursestable.xar_" . $sortby;
    $query .= " $sortorder";
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($courseid, $name, $number, $coursetype, $level, $shortdesc, $intendedcredits, $freq, $contact, $contactuid, $hidecourse, $last_modified) = $result->fields;
        if (xarSecurityCheck('ViewCourses', 0, 'Course', "$courseid:All:All")) {
            $items[] = array('courseid'     => $courseid,
                            'name'          => $name,
                            'number'        => $number,
                            'coursetype'    => $coursetype,
                            'level'         => $level,
                            'shortdesc'     => $shortdesc,
                            'intendedcredits' => $intendedcredits,
                            'freq'          => $freq,
                            'contact'       => $contact,
                            'contactuid'    => $contactuid,
                            'hidecourse'    => $hidecourse,
                            'last_modified' => $last_modified);
        }
    }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Return the items
    return $items;
}

?>
