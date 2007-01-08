<?php
/**
 * Display a course
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author XarayaGeek, MichelV.
 */
/**
 * Display a single course
 *
 * This is the function to provide detailed information on a single course
 * and show the details of all planned occurences for this course
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param  array $args an array of arguments (if called by other modules)
 * @param  int objectid A generic object id (if called by other modules) OPTIONAL
 * @param  int courseid The ID of the course
 * @return array with all data for the template
 */
function courses_user_display($args)
{
    extract($args);
    if (!xarVarFetch('courseid', 'id', $courseid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, '', XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $courseid = $objectid;
    }
    // Initialise the $data variable
    $data = array();

    // The API function is called to get the course.
    $item = xarModAPIFunc('courses','user','get',
                          array('courseid' => $courseid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Let any transformation hooks know that we want to transform some text.
    $item['transform'] = array('name', 'location', 'shortdesc');
    $item = xarModCallHooks('item',
        'transform',
        $courseid,
        $item);
    // Fill in the details of the item.
    $data['courseid'] = $courseid;
    $data['item'] = $item;
    $data['HideEmptyFields'] = xarModGetVar('courses', 'HideEmptyFields');
    // TODO: Evaluate the use of this?
    $data['catid'] = xarModAPIFunc('categories','user','getitemcats',
                                   array('itemid' => $courseid,
                                         'modid' => xarModGetIDFromName('courses'),
                                         'itemtype' => $courseid));

     // Get the username so we can pass it to the enrollment function
    $uid = xarUserGetVar('uid');
    $data['levelname'] = xarModAPIFunc('courses', 'user', 'getlevel',
                                      array('level' => $item['level']));
    // Get the number of months that we will show in the past
    $nummonths = xarModGetVar('courses', 'OldPlannedMonths');
    $startafter = mktime(0, 0, 0, date("m")-$nummonths, date("d"), date("Y"));
    $items = xarModAPIFunc('courses',
        'user',
        'getplandates',
        array('courseid' => $courseid,'startafter'=>$startafter));
    // TODO: howto check for correctness here?
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    $allowregistration = xarModGetVar('courses','allowregi'.$item['coursetype']) ? true : false;
    $data['allowregistration'] = $allowregistration;

    $iscontact = xarModAPIFunc('courses',
                                   'admin',
                                   'check_contact',
                                    array('userid' => $uid,
                                          'courseid' => $courseid));
    // Check individual permissions for Enroll/Edit/Viewstatus
    for ($i = 0; $i < count($items); $i++) {
        $planitem = $items[$i];
        $planningid = $planitem['planningid'];
        if ($allowregistration) {
            // Check to see if user is teacher
            $isteacher = xarModAPIFunc('courses',
                                   'admin',
                                   'check_teacher',
                                    array('userid' => $uid,
                                          'planningid' => $planningid));
            // With a result, the teacher can see the menu, or when there is an appropriate priv
            if (($isteacher == true) || ($iscontact == true) || xarSecurityCheck('EditCourses', 0, 'Course', "$courseid:$planningid:All")) {
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
        }

        if (xarSecurityCheck('ReadCourses', 0, 'Course', "$courseid:$planningid:All")) {
            $items[$i]['detailsurl'] = xarModURL('courses',
                'user',
                'displayplanned',
                array('planningid' => $planningid));
        } else {
            $items[$i]['detailsurl'] = '';
        }
        $items[$i]['detailstitle'] = xarML('Details');

        // The expected date is set?
        if(!empty($planitem['expected']) && is_string($planitem['expected'])); {
            $items[$i]['expected'] = $planitem['expected'];
        }

    }

    // Add the array of items to the template variables
    $data['items'] = $items;

    // Save the currently displayed item ID in a temporary variable cache
    // for any blocks that might be interested (e.g. the Others block)
    xarVarSetCached('Blocks.courses', 'courseid', $courseid);
    // Let any hooks know that we are displaying an item.
    $item['returnurl'] = xarModURL('courses',
        'user',
        'display',
        array('courseid' => $courseid));
    $item['itemtype'] =$item['coursetype'];
    $hooks = xarModCallHooks('item',
        'display',
        $courseid,
        $item);
    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        // You can use the output from individual hooks in your template too, e.g. with
        // $hookoutput['comments'], $hookoutput['hitcount'], $hookoutput['ratings'] etc.
        $data['hookoutput'] = $hooks;
    }
    $data['authid'] = xarSecGenAuthKey();
    // Set page name
    xarTplSetPageTitle(xarVarPrepForDisplay($item['name']));
    // Return the template variables defined in this function
    return $data;
}
?>