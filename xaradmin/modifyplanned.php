<?php
/**
 * Modify a planned course
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
 * Modify a planned course
 *
 * Modify a planned occurence of a course.
 *
 * @author Courses module development team
 * @param  $ 'planningid' the id of the item to be modified
 * @return array with data for this planned course
 */
function courses_admin_modifyplanned($args)
{
    extract($args);
    // Get parameters from whatever input we need.
    if (!xarVarFetch('planningid', 'id', $planningid, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('objectid',   'id', $objectid, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('name', 'str:1:', $name, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('number', 'str:1:', $number, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('coursetype', 'str:1:', $coursetype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('level', 'int:1:', $level, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('year', 'int:1:', $year, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('credits', 'float::', $credits, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('creditsmin', 'float::', $creditsmin, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('creditsmax', 'float::', $creditsmax, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('longdesc', 'str:1:', $longdesc, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('prerequisites', 'str:1:', $prerequisites, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('program', 'str:1:', $program, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('committee', 'str:1:', $committee, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('coordinators', 'str:1:', $coordinators, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('lecturers', 'str:1:', $lecturers, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aim', 'str:1:', $aim, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('method', 'str:1:', $method, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('language', 'str:1:', $language, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('location',        'str:1:', $location, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('costs',           'str:1:', $costs, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('material',        'str:1:', $material, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startdate',       'str::', $startdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('enddate',         'str::', $enddate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('info',            'str:1:', $info, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('program',         'str:1:', $progra, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('extreg',          'checkbox', $extreg, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('regurl',          'str:5:255', $regurl, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',         'array::', $invalid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('minparticipants', 'int::', $minparticipants, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxparticipants', 'int::', $maxparticipants, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('closedate', 'str::', $closedate, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hideplanning', 'checkbox', $hideplanning, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('last_modified', 'int', $last_modified, time(), XARVAR_NOT_REQUIRED)) return;
    // At this stage we check to see if we have been passed $objectid, the
    // generic item identifier.
    if (!empty($objectid)) {
        $planningid = $objectid;
    }
    // Get the planned course
    $planneddata = xarModAPIFunc('courses',
                                 'user',
                                 'getplanned',
                                  array('planningid' => $planningid));
    if (!isset($planneddata) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    $courseid = $planneddata['courseid'];
    $yearid = $planneddata['courseyear'];
    // Security check
    if (!xarSecurityCheck('EditCourses', 1, 'Course', "$courseid:$planningid:$yearid")) {
        return;
    }
    // Coursedata
    $coursedata = xarModAPIFunc('courses',
                                'user',
                                'get',
                                 array('courseid' => $courseid));
    if (!isset($coursedata) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Get menu variables
    $planneddata['module'] = 'courses';
    $planneddate['itemtype'] = $coursedata['coursetype'];
    $hooks = array();
    $hooks = xarModCallHooks('item', 'modify', $planningid, $planneddata); //Correct?

    $data['invalid']        = $invalid;
    $data['planningid']     = $planningid;
    $data['coursedata']     = $coursedata;
    // Extra labels
    $data['coursetypelabel'] = xarVarPrepForDisplay(xarML('Course Type'));
    $data['levellabel']     = xarVarPrepForDisplay(xarML('Course Level'));
    $data['contactlabel']   = xarVarPrepForDisplay(xarML('Course Contact details'));

    $data['cancelbutton']   = xarVarPrepForDisplay(xarML('Cancel'));

    $data['level'] = xarModAPIFunc('courses', 'user', 'gets',
                                      array('itemtype' => 1003));
    $data['years'] = xarModAPIFunc('courses', 'user', 'gets',
                                      array('itemtype' => 1005));
    $data['authid']         = xarSecGenAuthKey();
    $data['menutitle']      = xarVarPrepForDisplay(xarML('Edit a planned course'));
    $data['updatebutton']   = xarVarPrepForDisplay(xarML('Update Planned Course'));
    $data['hooks']          = $hooks;
    $data['planneddata']    = $planneddata;

    return $data;
}
?>