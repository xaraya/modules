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
    if (!xarVarFetch('invalid',    'array::', $invalid, array(), XARVAR_NOT_REQUIRED)) return;

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
    $data['year'] = xarModAPIFunc('courses', 'user', 'gets',
                                      array('itemtype' => 1005));
    $data['authid']         = xarSecGenAuthKey();
    $data['menutitle']      = xarVarPrepForDisplay(xarML('Edit a planned course'));
    $data['updatebutton']   = xarVarPrepForDisplay(xarML('Update Planned Course'));
    $data['hooks']          = $hooks;
    $data['planneddata']    = $planneddata;

    return $data;
}
?>