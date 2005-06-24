<?php
/**
 * File: $Id:
 * 
 * Standard function to create a new module item
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
 * add new planning for a course
 * This is a standard function that is called whenever an administrator
 * wishes to create a new planning for a specified course
 *
 * @param ['courseid'] Id of the course that will be planned
 */
function courses_admin_plancourse($args)
{
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('courseid', 'int:1:', $courseid)) return;
    if (!xarVarFetch('name', 'str:1:', $name, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('number', 'str:1:', $number, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('coursetype', 'str:1:', $coursetype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('level', 'int:1:', $level, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('credits', 'int::', $credits, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('creditsmin', 'int::', $creditsmin, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('creditsmax', 'int::', $creditsmax, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shortdesc', 'str:1:', $shortdesc, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('prerequisites', 'str:1:', $prerequisites, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aim', 'str:1:', $aim, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('method', 'str:1:', $method, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('language', 'str:1:', $language, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('location', 'str:1:', $location, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('costs', 'str:1:', $costs, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('material', 'str:1:', $material, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startdate', 'str:1:', $startdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('enddate', 'str:1:', $enddate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hideplanning', 'str:1:', $hideplanning, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('info', 'str:1:', $info, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str::', $invalid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('minparticipants', 'str::', $minparticipants, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxparticipants', 'str::', $maxparticipants, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('closedate', 'str::', $closedate, '', XARVAR_NOT_REQUIRED)) return;

    // Initialise the $data variable
    $data = xarModAPIFunc('courses', 'admin', 'menu');
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AddCourses')) return;
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    $data['invalid'] = $invalid;

        //Get info on the course already in main table
        
    $coursedata = xarModAPIFunc('courses',
                          'user',
                          'getcourse',
                          array('courseid' => $courseid));
    // Check for exceptions
    if (!isset($coursedata) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
        //Put in $data
    $data['coursedata'] = $coursedata;

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
    $data['infolabel'] = xarVarPrepForDisplay(xarML('Other Course info'));
    $data['addplanningbutton'] = xarVarPrepForDisplay(xarML('Add planning'));
    $data['cancelbutton'] = xarVarPrepForDisplay(xarML('Cancel'));

    $data['level'] = xarModAPIFunc('courses', 'user', 'gets',
                                      array('itemtype' => 3));
    $data['year'] = xarModAPIFunc('courses', 'user', 'gets',
                                      array('itemtype' => 5));


    $item = array();
    $item['module'] = 'courses';
    $hooks = xarModCallHooks('item', 'new', '', $item);

    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
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
    
    if (empty($hideplanning)) {
        $data['hideplanning'] = '';
    } else {
        $data['hideplanning'] = $hideplanning;
    }
    if (empty($maxpart)) {
        $data['maxpart'] = '';
    } else {
        $data['maxpart'] = $maxpart;
    }
    if (empty($minpart)) {
        $data['minpart'] = '';
    } else {
        $data['minpart'] = $minpart;
    }
    if (empty($closedate)) {
        $data['closedate'] = '';
    } else {
        $data['closedate'] = $closedate;
    }
    
    
    // Return the template variables defined in this function
    return $data;
}

?>
