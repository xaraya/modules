<?php
/**
 * Perform search in Courses
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author courses Development team
 */
/**
 * Searches all courses
 *
 * @author MichelV (original: J. Cox) <michelv@xaraya.com>
 * @access private
 * @return array with courses found
 */
function courses_userapi_search($args)
{
    if (empty($args) || count($args) < 1) {
        return;
    }

    extract($args);
    if($q == ''){
        return;
    }
    // Optional arguments.

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $coursestable = $xartable['courses'];
    $planningtable = $xartable['courses_planning'];

    $courses = array();
    $where = array();
    if ($name == 1){
        $where[] = "$coursestable.xar_name LIKE '%$q%'";
    }
    if ($number == 1){
        $where[] = "$coursestable.xar_number LIKE '%$q%'";
    }
    $join = '';
    if ($shortdesc == 1){
        $where[] = "$coursestable.xar_shortdesc LIKE '%$q%'";
    }
    if ($longdesc == 1){
        $join = "LEFT JOIN $planningtable ON $coursestable.xar_courseid = $planningtable.xar_courseid";
        $where[] = "$planningtable.xar_longdesc LIKE '%$q%'";
    }

    if(count($where) > 1){
        $clause = join($where, ' OR ');
    }
    elseif(count($where) == 1){
        $clause = $where[0];
    }
    else {
        return;
    }

    // Get item
    $sql = "SELECT DISTINCT $coursestable.xar_courseid,
                   $coursestable.xar_name,
                   $coursestable.xar_number
            FROM $coursestable $join
            WHERE $clause";

    $result =& $dbconn->Execute($sql);
        if (!$result) return;

    // Put polls into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($courseid, $name, $number) = $result->fields;
        if (xarSecurityCheck('ViewCourses', 0, 'Course', "$courseid:All:All")) {
            $courses[] = array('courseid' => $courseid,
                               'name'     => $name,
                               'number'   => $number);
        }
    }
    $result->Close();

    // Return the courses
    return $courses;

}
?>