<?php
/**
 * Upcoming courses block initialisation
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses
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
 */
function courses_upcomingblock_info()
{
    // Values
    return array(
        'text_type' => 'Upcoming',
        'module' => 'courses',
        'text_type_long' => 'Show upcoming and current courses',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true);
}

/**
 * display block
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
        $vars['numitems'] = 5;
    }
		$BlockDays = 7; // TODO replace these in settings
    $today=date("Y-m-d");
    $tomorrow=date("Y-m-d",strtotime("tomorrow"));
    $endblockdate=date("Y-m-d",strtotime("+$BlockDays days"));
    $beginblockdate=date("Y-m-d",strtotime("-$BlockDays days"));
    // Database information
    xarModDBInfoLoad('courses');
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $courses_planning = $xartable['courses_planning'];

    // Query
    $sql = "SELECT xar_planningid,
                   xar_courseid,
                   xar_startdate,
                   xar_enddate
            FROM $courses_planning
            WHERE  (xar_startdate <'". $today ."' AND xar_enddate > '".$today."')  
				      OR (xar_startdate > '" . $today . "' AND xar_enddate < '" .$endblockdate ."')
            ORDER by xar_startdate ASC ";
    $result = $dbconn->SelectLimit($sql, $vars['numitems'], $startnum-1 );
 
    if ($dbconn->ErrorNo() != 0) {
        return;
    }

    if ($result->EOF) {
        echo "empty";
        return;
    }

    // Create output object
    $items = array();

    // Display each item, permissions permitting
    for (; !$result->EOF; $result->MoveNext()) {
        list($planningid, $courseid, $startdate, $enddate) = $result->fields;

        if (xarSecurityCheck('ViewCourses', 0, 'Course', "All:All:All")) {
            if (xarSecurityCheck('ReadCourses', 0, 'Course', "All:All:All")) {
                $item = array();
                $item['link'] = xarModURL('courses', 'user', 'displayplanned',
                                          array('planningid' => $planningid));
            }
            $item['startdate'] = $startdate;
				$item['enddate'] = $enddate;
            $coursename = xarModAPIFunc('courses', 'user', 'getcoursename', array('courseid'=>$courseid));
            $item['coursename'] = $coursename['name'];
        } 
        $items[] = $item;
    } 

    $blockinfo['content'] = array('items' => $items,'BlockDays'=> $BlockDays);

    return $blockinfo;
}

?>