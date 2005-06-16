<?php
/**
 * File: $Id:
 * 
 * Standard function to update a current course
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
 * form supplied by xarModFunc('example','admin','modifycourse') to update a current item
 * 
 * @param  $ 'courseid' the id of the course to be updated
 * @param  $ 'name' the name of the course to be updated
 * @param  $ 'number' the number of the course to be updated
 */
function courses_admin_updatecourse($args)
{
    // Admin functions of this type can be called by other modules.  If this
    // happens then the calling module will be able to pass in arguments to
    // this function through the $args parameter.  Hence we extract these
    // arguments *before* we have obtained any form-based input through
    // xarVarFetch(), so that parameters passed by the modules can also be
    // checked by a certain validation.
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('courseid', 'int:1:', $courseid, $courseid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'int:1:', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name', 'str:1:', $name, $name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('number', 'str:1:', $number, $number,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('coursetype', 'str:1:', $coursetype, $coursetype, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('level', 'str:1:', $level, $level, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shortdesc', 'str:1:', $shortdesc, $shortdesc, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('language', 'str:1:', $language, $language, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('freq', 'str:1:', $freq, $freq, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contact', 'str:1:', $contact, $contact, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hidecourse', 'str:1:', $hidecourse, $hidecourse, XARVAR_NOT_REQUIRED)) return;

    // At this stage we check to see if we have been passed $objectid, the
    // generic item identifier.  This could have been passed in by a hook or
    // through some other function calling this as part of a larger module, but
    // if it exists it overrides $courseid

    // Note that this module could just use $objectid everywhere to avoid all
    // of this munging of variables, but then the resultant code is less
    // descriptive, especially where multiple objects are being used. The
    // decision of which of these ways to go is up to the module developer
    if (!empty($objectid)) {
        $courseid = $objectid;
    }

    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (!xarSecConfirmAuthKey()) return;
    // Notable by its absence there is no security check here.  This is because
    // the security check is carried out within the API function and as such we
    // do not duplicate the work here

    $invalid = array();
	
    if (empty($name)) {
        $data['name'] = '';
    } else {
        $data['name'] = $name;
    }

    if (empty($number)) {
        $data['number'] = '';
    } else {
        $data['number'] = $number;
    }

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

     if (empty($shortdesc)) {
        $data['shortdesc'] = '';
    } else {
        $data['shortdesc'] = $shortdesc;
    }

    if (empty($language)) {
        $data['language'] = '';
    } else {
        $data['language'] = $language;
    }
    if (empty($freq)) {
        $data['freq'] = '';
    } else {
        $data['freq'] = $freq;
    }
    if (empty($contact)) {
        $data['contact'] = '';
    } else {
        $data['contact'] = $contact;
    }
    if (empty($hidecourse)) {
        $data['hidecourse'] = '';
    } else {
        $data['hidecourse'] = $hidecourse;
    }

    // check if we have any errors
    if (count($invalid) > 0) {
        // call the admin_modifycourse function and return the template vars
        // (you need to copy admin-new.xd to admin-create.xd here)
        return xarModFunc('courses', 'admin', 'modifycourse',
                          array('courseid' => $courseid,
						        'name' => $name,
                                'number' => $number,
                                'coursetype' => $coursetype,
                                'level' => $level,
                                'shortdesc' => $shortdesc,
                                'language' => $language,
                                'freq' => $freq,
                                'contact' => $contact,
                                'hidecourse' => $hidecourse,
								'invalid' => $invalid));
    }

    // The API function is called.  Note that the name of the API function and
    // the name of this function are identical, this helps a lot when
    // programming more complex modules.  The arguments to the function are
    // passed in as their own arguments array.

    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
    if (!xarModAPIFunc('courses',
                       'admin',
                       'updatecourse',
                       array('courseid' => $courseid,
                             'name' => $name,
                             'number' => $number,
                             'coursetype' => $coursetype,
                             'level' => $level,
                             'shortdesc' => $shortdesc,
                             'language' => $language,
                             'freq' => $freq,
                             'contact' => $contact,
                             'hidecourse' => $hidecourse))) {
        return; // throw back
    } 
    xarSessionSetVar('statusmsg', xarML('Course Was Successfully Updated!'));
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('courses', 'admin', 'viewcourses'));
    // Return
    return true;
}

?>
