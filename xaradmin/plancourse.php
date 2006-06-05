<?php
/**
 * Plan a course
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * Add new planning for a course
 *
 * You will need to add a planned course to the list, before it becomes available for students
 *
 * @author Courses module development team
 * @param id $courseid Id of the course that will be planned
 * @return array
 */
function courses_admin_plancourse($args)
{
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('courseid',        'id', $courseid)) return;
    if (!xarVarFetch('name',            'str:1:', $name, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('number',          'str:1:', $number, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('coursetype',      'str:1:', $coursetype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('level',           'int:1:', $level, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('year',            'int:1:', $year, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('credits',         'float::', $credits, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('creditsmin',      'float::', $creditsmin, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('creditsmax',      'float::', $creditsmax, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shortdesc',       'str:1:', $shortdesc, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('prerequisites',   'str:1:', $prerequisites, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aim',             'str:1:', $aim, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('method',          'str:1:', $method, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('language',        'str:1:', $language, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('location',        'str:1:', $location, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('costs',           'str:1:', $costs, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('material',        'str:1:', $material, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startdate',       'str::', $startdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('enddate',         'str::', $enddate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hideplanning',    'checkbox', $hideplanning, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('info',            'str:1:', $info, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('program',         'str:1:', $progra, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('extreg',          'checkbox', $extreg, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('regurl',          'str:1:255', $regurl, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('info',            'str:1:', $info, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',         'array::', $invalid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('minparticipants', 'int::', $minparticipants, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxparticipants', 'int::', $maxparticipants, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('closedate',       'str::', $closedate, '', XARVAR_NOT_REQUIRED)) return;

    // Initialise the $data variable
    $data = xarModAPIFunc('courses', 'admin', 'menu');
    // Security check
    if (!xarSecurityCheck('EditCourses', 0, 'Course', '$courseid:All:All')) return;
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    $data['invalid'] = $invalid;

    //Get info on the course already in main table
    $coursedata = xarModAPIFunc('courses',
                          'user',
                          'get',
                          array('courseid' => $courseid));
    // Check for exceptions
    if (!isset($coursedata) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // Place in $data
    $data['coursedata'] = $coursedata;
    $data['itemtype'] = $coursedata['coursetype'];
    $data['itemid'] = $courseid;

    // Specify some labels for display
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
    $data['hideplanninglabel'] = xarVarPrepForDisplay(xarML('Hide this occurence'));
    $data['addplanningbutton'] = xarVarPrepForDisplay(xarML('Add planning'));
    $data['cancelbutton'] = xarVarPrepForDisplay(xarML('Cancel'));

    $data['level'] = xarModAPIFunc('courses', 'user', 'gets',
                                      array('itemtype' => 1003));
    $data['year'] = xarModAPIFunc('courses', 'user', 'gets',
                                      array('itemtype' => 1005));

    $item = array();
    $item['module'] = 'courses';
    $item['itemtype'] = $coursedata['coursetype'];
    $hooks = xarModCallHooks('item', 'display', $courseid, $item);

    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }

    $data['item'] = $item;
    // For E_ALL purposes, we need to check to make sure the vars are set.
    // If they are not set, then we need to set them empty to surpress errors

     if (empty($coursetype)) {
        $data['coursetype'] = '';
    } else {
        $data['coursetype'] = $coursetype;
    }

     if (empty($level)) {
        $data['level'] = '';
    } else {
        $data['level'] = $level;
    }

    if (empty($credits)) {
        $data['credits'] = '';
    } else {
        $data['credits'] = $credits;
    }
    if (empty($creditsmin)) {
        $data['creditsmin'] = '';
    } else {
        $data['creditsmin'] = $creditsmin;
    }
    if (empty($creditsmax)) {
        $data['creditsmax'] = '';
    } else {
        $data['creditsmax'] = $creditsmax;
    }

    if (empty($shortdesc)) {
        $data['shortdesc'] = '';
    } else {
        $data['shortdesc'] = $shortdesc;
    }

    if (empty($prerequisites)) {
        $data['prerequisites'] = '';
    } else {
        $data['prerequisites'] = $prerequisites;
    }
    if (empty($aim)) {
        $data['aim'] = '';
    } else {
        $data['aim'] = $aim;
    }
    if (empty($language)) {
        $data['language'] = '';
    } else {
        $data['language'] = $language;
    }
    if (empty($method)) {
        $data['method'] = '';
    } else {
        $data['method'] = $method;
    }
    if (empty($costs)) {
        $data['costs'] = '';
    } else {
        $data['costs'] = $costs;
    }
    if (empty($material)) {
        $data['material'] = '';
    } else {
        $data['material'] = $material;
    }
    if (empty($startdate)) {
        $data['startdate'] = '';
    } else {
        $data['startdate'] = $startdate;
    }
    if (empty($enddate)) {
        $data['enddate'] = '';
    } else {
        $data['enddate'] = $startdate;
    }
    if (empty($location)) {
        $data['location'] = '';
    } else {
        $data['location'] = $location;
    }

    if (empty($info)) {
        $data['info'] = '';
    } else {
        $data['info'] = $info;
    }
    if (empty($regurl)) {
        $data['regurl'] = '';
    } else {
        $data['regurl'] = $regurl;
    }
    if (empty($maxparticipants)) {
        $data['maxparticipants'] = '';
    } else {
        $data['maxparticipants'] = $maxparticipants;
    }
    if (empty($minparticipants)) {
        $data['minparticipants'] = '';
    } else {
        $data['minparticipants'] = $minparticipants;
    }

    if (empty($closedate)) {
        $data['closedate'] = '';
    } else {
        $data['closedate'] = $closedate;
    }
    $data['hideplanning'] = $hideplanning;
    $data['extreg'] = $extreg;
    // Return the template variables defined in this function
    return $data;
}

?>
