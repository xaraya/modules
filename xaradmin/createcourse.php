<?php
/**
 * Create a new course
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
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
 * @param  $name the name of the course to be created
 * @param  $number the number of the course to be created
 */
function courses_admin_createcourse($args)
{
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('name',        'str:1:', $name, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('number',      'str:1:', $number, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('coursetype',  'str:1:', $coursetype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('level',       'isset', $level, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shortdesc',   'str:1:', $shortdesc, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('intendedcredits', 'int:1:30', $intendedcredits, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('freq',        'str:1:', $freq, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contact',     'str:1:', $contact, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactuid',  'int:1:', $contactuid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hidecourse',  'int:1:', $hidecourse, '', XARVAR_NOT_REQUIRED)) return;
    // Argument check
    $item = array();
    // Check for duplicate name and/or number
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
    if (in_array ($name, $item)) {
        $invalid['duplicatename'] = 1;
    }
    if (in_array($number, $item)) {
        $invalid['duplicatenumber'] = 1;
    }

    // check if we have any errors
    if (count($invalid) > 0) {
        // call the admin_newcourse function and return the template vars
        return xarModFunc('courses', 'admin', 'newcourse',
                          array('name' => $name,
                                'number' => $number,
                                'coursetype' => $coursetype,
                                'level' => $level,
                                'shortdesc' => $shortdesc,
                                'intendedcredits' => $intendedcredits,
                                'freq' => $freq,
                                'contact' => $contact,
                                'contactuid' => $contactuid,
                                'hidecourse' => $hidecourse,
                                'invalid' => $invalid));
    }
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;
    $last_modified = date("Y-m-d H:i:s");
    $courseid = xarModAPIFunc('courses',
                          'admin',
                          'createcourse',
                          array('name' => $name,
                                'number' => $number,
                                'coursetype' => $coursetype,
                                'level' => $level,
                                'shortdesc' => $shortdesc,
                                'intendedcredits' => $intendedcredits,
                                'freq' => $freq,
                                'contact' => $contact,
                                'contactuid' => $contactuid,
                                'hidecourse' => $hidecourse,
                                'last_modified' => $last_modified));
    // The return value of the function is checked here, and if the function
    // succeeded then an appropriate message is posted.
    if (!isset($courseid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    xarResponseRedirect(xarModURL('courses', 'admin', 'viewcourses'));
    xarSessionSetVar('statusmsg', xarML('Course Was Successfully Created!'));
    // Return
    return true;
}

?>
