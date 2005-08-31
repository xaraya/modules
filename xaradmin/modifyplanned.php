<?php
/**
 * File: $Id:
 * 
 * Standard function to modify an item
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author Courses module development team 
 */
/**
 * modify a planned course
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 * 
 * @param  $ 'planningid' the id of the item to be modified
 */
function courses_admin_modifyplanned($args)
{
    extract($args);
    // Get parameters from whatever input we need.
    if (!xarVarFetch('planningid', 'isset:', $planningid, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array::', $invalid, '', XARVAR_NOT_REQUIRED)) return;
    
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

    // Security check
    if (!xarSecurityCheck('EditCourses', 1, 'Course', "All:$planningid:All")) {
        return;
    }
    // Coursedata
    $coursedata = xarModAPIFunc('courses',
                                'user',
                                'get',
                                 array('courseid' => $planneddata['courseid']));
    if (!isset($coursedata) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    
    // Get menu variables
    $planneddata['module'] = 'courses';
    $hooks = xarModCallHooks('item', 'modify', $planningid, $planneddata); //Correct?
    if (empty($hooks)) {
        $hooks = '';
    } elseif (is_array($hooks)) {
        $hooks = join('', $hooks);
    }
    $data['levels'] = xarModAPIFunc('courses', 'user', 'gets', array('itemtype' => 3));
    $data['invalid'] = $invalid;
    $data['planningid'] = $planningid;
    $data['coursedata'] = $coursedata;
    $data['namelabel'] = xarVarPrepForDisplay(xarML('Course Name'));
    $data['numberlabel'] = xarVarPrepForDisplay(xarML('Course Number'));
    $data['coursetypelabel'] = xarVarPrepForDisplay(xarML('Course Type (Category)'));
    $data['levellabel'] = xarVarPrepForDisplay(xarML('Course Level'));
    $data['yearlabel'] = xarVarPrepForDisplay(xarML('Year for this occurence'));
    $data['creditslabel'] = xarVarPrepForDisplay(xarML('Course Credits'));
    $data['startdatelabel'] = xarVarPrepForDisplay(xarML('Start date'));
    $data['enddatelabel'] = xarVarPrepForDisplay(xarML('End date'));
    $data['costslabel'] = xarVarPrepForDisplay(xarML('Course Fee'));
    $data['materiallabel'] = xarVarPrepForDisplay(xarML('Course materials'));
    $data['creditsminlabel'] = xarVarPrepForDisplay(xarML('Course Minimum Credits'));
    $data['creditsmaxlabel'] = xarVarPrepForDisplay(xarML('Course Maximum Credits'));
    $data['prereqlabel'] = xarVarPrepForDisplay(xarML('Course Prerequisites'));
    $data['aimlabel'] = xarVarPrepForDisplay(xarML('Course Aim'));
    $data['coordinatorslabel'] = xarVarPrepForDisplay(xarML('Course coordinators'));
    $data['committeelabel'] = xarVarPrepForDisplay(xarML('Course committee'));
    $data['lecturerslabel'] = xarVarPrepForDisplay(xarML('Course lecturers'));
    $data['locationlabel'] = xarVarPrepForDisplay(xarML('Course location'));
    $data['programlabel'] = xarVarPrepForDisplay(xarML('Course Programme'));
    $data['longdesclabel'] = xarVarPrepForDisplay(xarML('Long Course Description'));
    $data['methodlabel'] = xarVarPrepForDisplay(xarML('Course Method'));
    $data['languagelabel'] = xarVarPrepForDisplay(xarML('Course Language'));
    $data['freqlabel'] = xarVarPrepForDisplay(xarML('Course Frequency'));
    $data['contactlabel'] = xarVarPrepForDisplay(xarML('Course Contact details'));
    $data['minpartlabel'] = xarVarPrepForDisplay(xarML('Minimum Participants'));
    $data['maxpartlabel'] = xarVarPrepForDisplay(xarML('Maximum Participants'));
    $data['closedatelabel'] = xarVarPrepForDisplay(xarML('Registration closing date'));
    $data['lastmodilabel'] = xarVarPrepForDisplay(xarML('Date last modified'));
    $data['hideplanninglabel'] = xarVarPrepForDisplay(xarML('Hide this occurence'));
    $data['infolabel'] = xarVarPrepForDisplay(xarML('Other Course info'));
    $data['cancelbutton'] = xarVarPrepForDisplay(xarML('Cancel'));
    $data['level'] = xarModAPIFunc('courses', 'user', 'gets',
                                      array('itemtype' => 3));
    $data['year'] = xarModAPIFunc('courses', 'user', 'gets',
                                      array('itemtype' => 5));
    $data['authid'] = xarSecGenAuthKey();
    $data['menutitle'] = xarVarPrepForDisplay(xarML('Edit a planned course'));
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Course'));
    $data['hooks'] = $hooks;
    $data['planneddata'] = $planneddata;

    return $data;
}

?>
