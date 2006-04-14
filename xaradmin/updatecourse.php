<?php
/**
 * Standard function to update a current course
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
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('courses','admin','modifycourse') to update a current item
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param  id $courseid the id of the course to be updated
 * @param  str $name the name of the course to be updated
 * @param  str $number the number of the course to be updated
 * @return true
 */
function courses_admin_updatecourse($args)
{
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('courseid',        'id', $courseid)) return;
    if (!xarVarFetch('objectid',        'id', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name',            'str:1:', $name)) return;
    if (!xarVarFetch('number',          'str:1:', $number)) return;
    if (!xarVarFetch('coursetype',      'str:1:', $coursetype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('level',           'int:1:', $level, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shortdesc',       'str:1:', $shortdesc, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('intendedcredits', 'float::', $intendedcredits, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('freq',            'str:1:', $freq, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contact',         'str:1:', $contact, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactuid',      'int:1:', $contactuid,'', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hidecourse',      'int:1:', $hidecourse, '', XARVAR_NOT_REQUIRED)) return;
    // At this stage we check to see if we have been passed $objectid, the
    // generic item identifier.
    if (!empty($objectid)) {
        $courseid = $objectid;
    }

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;
    // We don't make an invalid here... so why need it?
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

    if (empty($intendedcredits)) {
        $data['intendedcredits'] = '';
    } else {
        $data['lintendedcredits'] = $intendedcredits;
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
    if (empty($contactuid)) {
        $data['contactuid'] = '';
    } else {
        $data['contactuid'] = $contactuid;
    }
    if (empty($hidecourse)) {
        $data['hidecourse'] = '';
    } else {
        $data['hidecourse'] = $hidecourse;
    }

    // check if we have any errors
    if (count($invalid) > 0) {
        xarSessionSetVar('statusmsg', xarML('There is an error in the data you have provided, please check!'));
       // call the admin_modifycourse function and return the template vars
       // (you need to copy admin-new.xd to admin-create.xd here)
       return xarModFunc('courses', 'admin', 'modifycourse',
                          array('courseid' => $courseid,
                                'name' => $name,
                                'number' => $number,
                                'coursetype' => $coursetype,
                                'level' => $level,
                                'shortdesc' => $shortdesc,
                                'freq' => $freq,
                                'contact' => $contact,
                                'contactuid' => $contactuid,
                                'hidecourse' => $hidecourse,
                                'last_modified' => $last_modified,
                                'intendedcredits' => $intendedcredits,
                                'invalid' => $invalid));
    }


    // The API function is called.
    if (!xarModAPIFunc('courses',
                       'admin',
                       'updatecourse',
                       array('courseid' => $courseid,
                             'name' => $name,
                             'number' => $number,
                             'coursetype' => $coursetype,
                             'level' => $level,
                             'shortdesc' => $shortdesc,
                             'freq' => $freq,
                             'contact' => $contact,
                             'contactuid' => $contactuid,
                             'hidecourse' => $hidecourse,
                             'intendedcredits' => $intendedcredits))) {
        return false; // throw back
    }
    // TODO: how to remove previous statusmsg?
    xarSessionSetVar('statusmsg', xarML('Course Was Successfully Updated!'));

    xarResponseRedirect(xarModURL('courses', 'admin', 'viewcourses', array('courseid' => $courseid)));
    // Return
    return true;
}

?>
