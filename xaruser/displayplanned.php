<?php
/**
 * Display a planned course
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author XarayaGeek, Michel V.
 */

/**
 * display a course
 *
 * This is the function to provide detailed information on a single course
 * and show the details of all planned occurences
 *
 * @author MichelV.
 *
 * @param id $objectid A generic object id (if called by other modules)
 * @param id $planningid The ID of the planned course
 */
function courses_user_displayplanned($args)
{
    extract($args);
    if (!xarVarFetch('planningid', 'id', $planningid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, '', XARVAR_NOT_REQUIRED)) return;

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
    // Get the course this planitem is related to
    $courseid = $item['courseid'];
    $course = xarModAPIFunc('courses','user','get', array('courseid' => $courseid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Let any transformation hooks know that we want to transform some text
    //TODO: necessary?
    $item['transform'] = array('name','lecturers');
    $item['itemtype'] = $course['coursetype'];
    $item = xarModCallHooks('item', 'transform', $planningid, $item);

    // Fill in the details of the item.
    $data['planningid'] = $planningid;
    $data['item'] = $item;
    $data['HideEmptyFields'] = xarModGetVar('courses', 'HideEmptyFields');
    $data['course'] = $course;
    $data['levelname'] = xarModAPIFunc('courses', 'user', 'getlevel',
                                      array('level' => $course['level']));

    // Get the username so we can pass it to the enrollment function
    $uid = xarUserGetVar('uid');
    if (xarSecurityCheck('ReadCourses', 0, 'Course', "$courseid:$planningid:All")) {
        // See if the date for enrollment is surpassed
        $closedate = $item['closedate'];
        $timenow = time();
        if($closedate > $timenow) {
            $data['closed'] = false;
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
            } else {
                $data['enrolled'] = 0;
                $data['enrollbutton'] = xarVarPrepForDisplay(xarML('Enroll'));
                $data['action'] = "xarModUrl('courses', 'user', 'enroll')";
            }
        } else {
            $data['closed'] = true;
        }
    }
    // Add edit link to this planned course
    $courseyear = $item['courseyear'];
    if (xarSecurityCheck('EditCourses', 0, 'Course', "$courseid:$planningid:$courseyear")) {
        $data['editlink'] = xarModURL('courses',
                'admin',
                'modifyplanned',
                array('planningid' => $planningid));
    } else {
        $data['editlink'] ='';
    }

    // Get all planned courses for this course
    $items = xarModAPIFunc('courses',
        'user',
        'getplandates',
        array('courseid' => $courseid));
    //TODO: howto check for correctness here?
    //if (!isset($plandates) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Check individual permissions for Enroll/Edit/Viewstatus
    for ($i = 0; $i < count($items); $i++) {
        $planitem = $items[$i];
        $planningid = $planitem['planningid'];
        if (xarSecurityCheck('EditCourses', 0, 'Course', "$courseid:$planningid:All")) {
            $items[$i]['participantsurl'] = xarModURL('courses',
                'admin',
                'participants',
                array('planningid' => $planningid));
        } else {
            $items[$i]['participantsurl'] = '';
        }
        $items[$i]['participantstitle'] = xarML('Participants');

        if (xarSecurityCheck('ReadCourses', 0, 'Course', "$courseid:$planningid:All")) {
            // See if the date for enrollment is surpassed
            $closedate = $planitem['closedate'];
            $timenow = time();
            if($closedate > $timenow) {
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
                                               array('planningid' => $planningid));
                } else {
                    $items[$i]['enrolltitle'] = xarML('Enroll');
                    $items[$i]['enrollurl'] = xarModURL('courses',
                        'user',
                        'enroll',
                        array('planningid' => $planningid));
                }
            } else {
                $items[$i]['enrolltitle'] = xarML('Registration Closed');
                $items[$i]['enrollurl'] = xarModURL('courses',
                                                  'user',
                                                  'displayplanned',
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

    // Call the hooks
    $item['returnurl'] = xarModURL('courses',
        'user',
        'displayplanned',
        array('planningid' => $planningid));

    $data['catid'] = xarModAPIFunc('categories','user','getitemcats',
                                   array('itemid' => $courseid,
                                         'modid' => xarModGetIDFromName('courses'),
                                         'itemtype' => $course['coursetype']));
    $data['authid'] = xarSecGenAuthKey();
    // Set the page name according to the coursename
    xarTplSetPageTitle(xarVarPrepForDisplay($course['name']));
    // Return the template variables defined in this function
    return $data;
}

?>
