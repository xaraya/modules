<?php
 /**
 * File: $Id: 
 * 
 * Display an item
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author XarayaGeek , Michel V.
 */

/**
 * display a course
 * This is the function to provide detailed information on a single course
 * and show the details of all planned occurences
 * @author Michel V.
 * 
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args ['objectid'] a generic object id (if called by other modules)
 * @param  $args ['planningid'] the ID of the course
 */
function courses_user_displayplanned($args)
{
    extract($args);
    if (!xarVarFetch('planningid', 'int:1:', $planningid)) return;
    if (!xarVarFetch('objectid', 'int:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $planningid = $objectid;
    }
    // Initialise the $data variable
    $data = xarModAPIFunc('courses', 'user', 'menu');
    // Prepare the variable that will hold some status message if necessary
    $data['status'] = '';
    // Get the planned course details
    $item = xarModAPIFunc('courses',
        'user',
        'getplanned',
        array('planningid' => $planningid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    
    $course = xarModAPIFunc('courses',
        'user',
        'get',
        array('courseid' => $item['courseid']));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Let any transformation hooks know that we want to transform some text.
    $item['transform'] = array('name');
    $item = xarModCallHooks('item',
        'transform',
        $planningid,
        $item);
    // Fill in the details of the item.
    $data['namelabel'] = xarVarPrepForDisplay(xarML('Course Name'));
    $data['numberlabel'] = xarVarPrepForDisplay(xarML('Course Number'));
    $data['coursetypelabel'] = xarVarPrepForDisplay(xarML('Course Type (Category)'));
    $data['levellabel'] = xarVarPrepForDisplay(xarML('Course Level'));
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
    $data['shortdesclabel'] = xarVarPrepForDisplay(xarML('Short Course Description'));
    $data['longdesclabel'] = xarVarPrepForDisplay(xarML('Long Course Description'));
    $data['methodlabel'] = xarVarPrepForDisplay(xarML('Course Method'));
    $data['languagelabel'] = xarVarPrepForDisplay(xarML('Course Language'));
    $data['freqlabel'] = xarVarPrepForDisplay(xarML('Course Frequency'));
    $data['contactlabel'] = xarVarPrepForDisplay(xarML('Course Contact details'));
    $data['hideplanninglabel'] = xarVarPrepForDisplay(xarML('Hide this occurence'));
    $data['infolabel'] = xarVarPrepForDisplay(xarML('Other Course info'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarML('Options'));
    $data['maxpartlabel'] = xarVarPrepForDisplay(xarML('Maximum Participants'));
    $data['minpartlabel'] = xarVarPrepForDisplay(xarML('Minimum Participants'));
    $data['closedatelabel'] = xarVarPrepForDisplay(xarML('Closing date for registration'));
    $data['lastmodilabel'] = xarVarPrepForDisplay(xarML('Date last modified'));
    $data['planningid'] = $planningid;
    $data['item'] = $item;
    $data['HideEmptyFields'] = xarModGetVar('courses', 'HideEmptyFields');
    $data['course'] = $course;
     // Get the username so we can pass it to the enrollment function
    $uid = xarUserGetVar('uid');
    // See if student is already enrolled 
    $enrolled = xarModAPIFunc('courses',
                              'user',
                              'check_enrolled',
                               array('uid' => $uid,
                                     'planningid' => $planningid));
                                
    if (count($enrolled)!=0) {
        $data['enrolled'] = 1;
        $data['enrollbutton'] = xarVarPrepForDisplay(xarML('You are enrolled in this course'));
        $data['action'] = "xarModUrl('courses', 'user', 'viewstatus')";
    }
    else {
        $data['enrolled'] = 0;
        $data['enrollbutton'] = xarVarPrepForDisplay(xarML('Enroll'));
        $data['action'] = "xarModUrl('courses', 'user', 'enroll')";
        
    }
    $courseid = $item['courseid'];
    $data['levelname'] = xarModAPIFunc('courses', 'user', 'getlevel',
                                      array('level' => $course['level']));
    $items = xarModAPIFunc('courses',
        'user',
        'getplandates',
        array('courseid' => $courseid));
    //TODO: howto check for correctness here?
    //if (!isset($plandates) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Check individual permissions for Enroll/Edit/Viewstatus

    for ($i = 0; $i < count($items); $i++) {
        $planitem = $items[$i];
        if (xarSecurityCheck('EditCourses', 0, 'Course', "$courseid:All:All")) {
        $planningid = $planitem['planningid'];

            $items[$i]['participantsurl'] = xarModURL('courses',
                'admin',
                'participants',
                array('planningid' => $planningid));
        } else {
            $items[$i]['participantsurl'] = '';
        }
        $items[$i]['participantstitle'] = xarML('Participants');
        
        if (xarSecurityCheck('ReadCourses', 0, 'Course', "$courseid:$planningid:All")) {
            // Add check for already enrolled
            $enrolled = xarModAPIFunc('courses',
                          'user',
                          'check_enrolled',
                          array('uid' => $uid,
                                'planningid' => $planningid));
            if (count($enrolled)!=0) {
            $items[$i]['enrolltitle'] = xarML('Enrolled');
            // When enrolled, redirect to details page instead
            $items[$i]['enrollurl'] = xarModURL('courses',
                                      'user',
                                      'displayplanned',
                                       array('planningid' => $planningid));; 
            } else {
            $items[$i]['enrolltitle'] = xarML('Enroll');
            $items[$i]['enrollurl'] = xarModURL('courses',
                'user',
                'enroll',
                array('planningid' => $planningid));
            }
        }
        
        if (xarSecurityCheck('EditCourses', 0, 'Course', "$courseid:$planningid:All")) {
            $items[$i]['deleteurl'] = xarModURL('courses',
                'admin',
                'deleteplanned',
                array('planningid' => $planningid));
        } else {
            $items[$i]['statusurl'] = '';
        }
        $items[$i]['statustitle'] = xarML('Status');
    }
    
    // Add the array of items to the template variables
    $data['items'] = $items;    
    
    // Save the currently displayed item ID in a temporary variable cache
    // for any blocks that might be interested (e.g. the Others block)
    // You should use this -instead of globals- if you want to make
    // information available elsewhere in the processing of this page request
    xarVarSetCached('Blocks.courses', 'planningid', $planningid);
    // Let any hooks know that we are displaying an item.
    $item['returnurl'] = xarModURL('courses',
        'user',
        'displayplanned',
        array('planningid' => $planningid));
    $hooks = xarModCallHooks('item',
        'display',
        $planningid,
        $item);
    if (empty($hooks)) {
        $data['hookoutput'] = '';
    } else {
        // You can use the output from individual hooks in your template too, e.g. with
        // $hookoutput['comments'], $hookoutput['hitcount'], $hookoutput['ratings'] etc.
        $data['hookoutput'] = $hooks;
    }
    $data['authid'] = xarSecGenAuthKey();
    // Once again, we are changing the name of the title for better
    // Search engine capability.
    xarTplSetPageTitle(xarVarPrepForDisplay($course['name']));
    // Return the template variables defined in this function
    return $data;
}

?>
