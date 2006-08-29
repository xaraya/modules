<?php
/**
 * Get all courses
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
 * get all courses
 *
 * @author the Courses module development team
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @param sortby $ the parameter to sort by (default name) enum 'name','shortdesc','number'
 * @param string catid category id or ids when using glue like a + or a -
 * @param id level courselevelid
 * @param id type coursetypeid
 * @return array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_userapi_getall($args)
{
    extract($args);

    // Argument check
    $valid = array('name','shortdesc','number');
    if (!isset($sortby) || !in_array($sortby,$valid)) { // Should be orderby and then sortby ASC DESC
        $sortby = 'number';
    }
    if (!isset($catid)) {
        $catid ='';
    }
    if (!isset($cids)) {
        $cids = array();
    }
    if (!isset($level) || !is_numeric($level)) {
        $level = 0;
    }
    if (!isset($coursetype) || !is_numeric($coursetype)) {
        $coursetype = 0;
    }
    if (!isset($startnum) || !is_numeric($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $numitems = -1;
    }
    if (!isset($sortorder) || (isset($sortorder) && !in_array($sortorder, array('DESC','ASC')))) {
        $sortorder = 'ASC';
    }
    $items = array();
    if (!xarSecurityCheck('ViewCourses')) return;
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $coursestable = $xartable['courses'];
    // Set to be able to see all courses or only non-hidden ones
    // TODO: when this simple check is used, and an admin level is given, then all are presented
    if (xarSecurityCheck('AdminCourses', 0)) {
    $where = "0, 1";
    } else {
    $where = "0";
    }
    $query = "SELECT DISTINCT xar_courseid,
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

    if (!empty($coursetype) && ($coursetype > 0)) {
        $itemtype = $coursetype;
    } else {
        $itemtype = NULL;
    }
    // Category selection
    if (((!empty($catid)) || !empty($cids)) && xarModIsHooked('categories','courses', array('itemtype' => $itemtype))) {
        // Get the LEFT JOIN ... ON ...  and WHERE parts from categories
        $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                                       array('modid' => xarModGetIDFromName('courses'),
                                             'catid' => $catid,
                                             'cids' => $cids,
                                             'itemtype' => $itemtype));
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
    if (($level > 0) && is_int($level)) {
        $query .= " AND xar_level = $level ";
    }
    // Level selection
    if ($coursetype > 0) {
        $query .= " AND xar_type = $coursetype ";
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
