<?php
/**
 * Create a new course
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
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('courses','admin','newcourse') to create a new course
 *
 * @param string $name the name of the course to be created
 * @param string $number the number of the course to be created
 * @param int $coursetype the number of the course to be created
 * @param  $level the number of the course to be created
 * @param string $shortdesc the number of the course to be created
 * @param int $intendedcredits the number of the course to be created
 * @param string $freq the number of the course to be created
 * @param string $contact the number of the course to be created
 * @param int $contactuid the uid of the coordinator of the course to be created
 * @param int $hidecourse the number of the course to be created
 * @return array
 * @throws
 */
function courses_admin_createcourse($args)
{
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('name',        'str:1:', $name, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('number',      'str:1:', $number, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('coursetype',  'int:1:', $coursetype, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('level',       'isset', $level, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shortdesc',   'str:1:', $shortdesc, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('intendedcredits', 'float::', $intendedcredits, '', XARVAR_NOT_REQUIRED)) return;
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
        xarSessionSetVar('statusmsg', xarML('Please check the data you have provided!'));
        // call the admin_newcourse function and return the template vars
        return xarModFunc('courses', 'admin', 'newcourse',
                          array('name'          => $name,
                                'number'        => $number,
                                'coursetype'    => $coursetype,
                                'level'         => $level,
                                'shortdesc'     => $shortdesc,
                                'intendedcredits' => $intendedcredits,
                                'freq'          => $freq,
                                'contact'       => $contact,
                                'contactuid'    => $contactuid,
                                'hidecourse'    => $hidecourse,
                                'invalid'       => $invalid));
    }
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    $courseid = xarModAPIFunc('courses',
                          'admin',
                          'createcourse',
                          array('name'           => $name,
                                'number'         => $number,
                                'coursetype'     => $coursetype,
                                'level'          => $level,
                                'shortdesc'      => $shortdesc,
                                'intendedcredits' => $intendedcredits,
                                'freq'           => $freq,
                                'contact'        => $contact,
                                'contactuid'     => $contactuid,
                                'hidecourse'     => $hidecourse));
    // The return value of the function is checked here, and if the function
    // succeeded then an appropriate message is posted.
    if (!isset($courseid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    xarResponseRedirect(xarModURL('courses', 'admin', 'viewcourses'));
    xarSessionSetVar('statusmsg', xarML('Course Was Successfully Created!'));
    // Return
    return true;
}

?>
