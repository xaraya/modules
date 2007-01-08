<?php
/**
 * Upcoming courses block initialisation
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */

/**
 * initialise block
 */
function courses_upcomingblock_init()
{
    return array(
        'numitems' => 5
    );
}

/**
 * get information on block
 * @return array
 */
function courses_upcomingblock_info()
{
    // Values
    return array(
        'text_type' => xarML('Upcoming'),
        'module' => 'courses',
        'text_type_long' => xarML('Show upcoming and current courses'),
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true);
}

/**
 * display block
 * @return array $blockinfo
 */
function courses_upcomingblock_display($blockinfo)
{
    // Optional arguments.
    if (!isset($startnum)) {
        $startnum = 1;
    }

    // Security check
    if (!xarSecurityCheck('ReadCoursesBlock', 0, 'Block', $blockinfo['title'])) return;

    // Get variables from content block.
    // Content is a serialized array for legacy support, but will be
    // an array (not serialized) once all blocks have been converted.
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 10;
    }

    //set dates for determining which events to show for the upcoming events
    if (!empty($vars['BlockDays'])) {
        $BlockDays = $vars['BlockDays'];
    } else {
        $BlockDays = 7;
    }
    $args['BlockDays'] = $BlockDays;
    // Format of the date
    if (!empty($vars['DateFormat'])) {
        $DateFormat = $vars['DateFormat'];
    } else {
        $DateFormat = 'short';
    }
    $args['DateFormat'] = $DateFormat;
    // In int
    $today=strtotime('today');
    $tomorrow=strtotime("tomorrow");
    $endblockdate=strtotime("+$BlockDays days");
    $beginblockdate=strtotime("-$BlockDays days");
    // Database information
    xarModDBInfoLoad('courses');
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $courses_planning = $xartable['courses_planning'];
    // Only get hidden courses for admin
    if (xarSecurityCheck('AdminCourses', 0)) {
    $where = "0, 1";
    } else {
    $where = "0";
    }
    // Query
    $sql = "SELECT xar_planningid,
                   xar_courseid,
                   xar_startdate,
                   xar_enddate
            FROM $courses_planning
            WHERE  (xar_startdate <'". $today ."' AND xar_enddate > '".$today."')
               OR (xar_startdate > '" . $today . "' AND xar_enddate < '" .$endblockdate ."')
               AND xar_hideplanning in ($where)
            ORDER by xar_startdate ASC ";
    $result = $dbconn->SelectLimit($sql, $vars['numitems'], $startnum-1 );

    if ($dbconn->ErrorNo() != 0) {
        return;
    }

    if ($result->EOF) {
    //    echo "empty";
        return;
    }

    // Create output object
    $items = array();

    // Display each item, permissions permitting
    for (; !$result->EOF; $result->MoveNext()) {
        list($planningid, $courseid, $startdate, $enddate) = $result->fields;
    // $courseid:$planningid:All
        if (xarSecurityCheck('ViewCourses', 0, 'Course', "$courseid:$planningid:All")) { // TODO: privileges
            if (xarSecurityCheck('ReadCourses', 0, 'Course', "$courseid:$planningid:All")) { // TODO: privileges
                $item = array();
                $item['link'] = xarModURL('courses', 'user', 'displayplanned',
                                          array('planningid' => $planningid));
            }
            $item['startdate'] = $startdate;
            $item['enddate'] = $enddate;
            $coursename = xarModAPIFunc('courses', 'user', 'getcoursename', array('courseid'=>$courseid));
            $item['coursename'] = $coursename;
            //string substr ( string string, int start [, int length] )
            $item['trimname'] = substr ( $coursename, 0, 14).'...';
        }
        $items[] = $item;
    }
    $blockinfo['content'] = array('items' => $items,'BlockDays'=> $BlockDays, 'numitems' => $vars['numitems'], 'DateFormat' => $DateFormat);

    return $blockinfo;
}

?>