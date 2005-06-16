<?php
/**
 * File: $Id: 
 * 
 * Standard function to create a new item
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
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('courses','admin','newcourse') to create a new course
 *
 * @param  $name the name of the item to be created
 * @param  $number the number of the item to be created
 */
function courses_admin_createplanning($args)
{
    // Admin functions of this type can be called by other modules.  If this
    // happens then the calling module will be able to pass in arguments to
    // this function through the $args parameter.  Hence we extract these
    // arguments *before* we have obtained any form-based input through
    // xarVarFetch().
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('courseid', 'int:1:', $courseid)) return;
    if (!xarVarFetch('year', 'int:1:', $year)) return;
    if (!xarVarFetch('credits', 'int:1:', $credits, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('creditsmin', 'int::', $creditsmin, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('creditsmax', 'int::', $creditsmax, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startdate', 'str:1:', $startdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('enddate', 'str:1:', $enddate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('prerequisites', 'str:1:', $prerequisites, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aim', 'str:1:', $aim, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('method', 'str:1:', $method, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('longdesc', 'str:1:', $longdesc, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('costs', 'str:1:', $costs, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('committee', 'str:1:', $committee, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('coordinators', 'str:1:', $coordinators, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('lecturers', 'str:1:', $lecturers, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('location', 'str:1:', $location, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('material', 'str:1:', $material, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('info', 'str:1:', $info, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('program', 'str:1:', $program, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hideplanning', 'str::', $hideplanning, '', XARVAR_NOT_REQUIRED)) return;
    // Argument check - make sure that all required arguments are present
    // and in the right format, if not then return to the add form with the
    // values that are there and a message with a session var. If you perform
    // this check now, you could do away with the check in the API along with
    // the exception that comes with it.
	/*
	$item = array();
    $item = xarModAPIFunc('courses',
                          'admin',
                          'validatecourse',
                          array('name' => $name,
	                            'number' => $number));
    
    // Argument check
	
	
    $invalid = array();
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (!isset($number) || !is_string($number)) {
        $invalid[] = 'number';
    }
    if (!empty($name) && strcmp($item['name'], $name)) {
        $invalid[] = 'duplicatename';
    }
    if (!empty($number) && strcmp($item['number'], $number)) {
        $invalid[] = 'duplicatenumber';
    }


    // check if we have any errors
    if (count($invalid) > 0) {
        // call the admin_newcourse function and return the template vars
        // (move from admin-new.xd to admin-create.xd here)
		return xarModFunc('courses', 'admin', 'newcourse',
                          array('name' => $name,
                                'number' => $number,
                                'coursetype' => $coursetype,
                                'level' => $level,
                                'credits' => $credits,
                                'creditsmin' => $creditsmin,
                                'creditsmax' => $creditsmax,
                                'shortdesc' => $shortdesc,
                                'prerequisites' => $prerequisites,
                                'aim' => $aim,
                                'method' => $method,
                                'language' => $language,
                                'freq' => $freq,
                                'contact' => $contact,
                                'hidecourse' => $hidecourse,
                                'invalid' => $invalid));
    }*/
    // Confirm authorisation code. This checks that the form had a valid
    // authorisation code attached to it. If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (!xarSecConfirmAuthKey()) return;
    // Notable by its absence there is no security check here.  This is because
    // the security check is carried out within the API function and as such we
    // do not duplicate the work here
    // The API function is called. Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules. The arguments to the function are
    // passed in as their own arguments array
    $courseid = xarModAPIFunc('courses',
                          'admin',
                          'createplanning',
                          array('courseid' => $courseid,
                                'year' => $year,
                                'credits' => $credits,
								'creditsmin' => $creditsmin,
								'creditsmax' => $creditsmax,
                                'startdate' => $startdate,
								'enddate' => $enddate,
								'prerequisites' => $prerequisites,
                                'aim' => $aim,
                                'method' => $method,
                                'longdesc' => $longdesc,
								'costs' => $costs,
                                'committee' => $committee,
                                'coordinators' => $coordinators,
                                'lecturers' => $lecturers,
								'location' => $location,
								'material' => $material,
                                'info' => $info,
								'program' => $program,
                                'hideplanning' => $hideplanning));
    //Check returnvalue
    if (!isset($planningid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('courses', 'admin', 'viewcourses'));
    // Return
    return true;
}

?>
