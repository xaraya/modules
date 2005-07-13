<?php
/**
 * File: $Id:
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
 * add new course
 * This is a standard function that is called whenever an administrator
 * wishes to create a new course
 */
function courses_admin_newcourse($args)
{
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('name', 'str:1:', $name, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('number', 'str:1:', $number, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('coursetype', 'str:1:', $coursetype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shortdesc', 'str:1:', $shortdesc, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('language', 'str:1:', $language, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('freq', 'str:1:', $freq, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contact', 'str:1:', $contact, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hidecourse', 'int:1:', $hidecourse, '', XARVAR_NOT_REQUIRED)) return;
    //if (!xarVarFetch('level', 'isset::', $level, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array', $invalid, '', XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('courses', 'admin', 'menu');

    // Security check 
    if (!xarSecurityCheck('AddCourses')) return;

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    $data['invalid'] = $invalid;
    // Specify some labels for display
    $data['namelabel'] = xarVarPrepForDisplay(xarML('Course Name'));
    $data['numberlabel'] = xarVarPrepForDisplay(xarML('Code Number'));
    $data['coursetypelabel'] = xarVarPrepForDisplay(xarML('Course Type (Category)'));
    $data['levellabel'] = xarVarPrepForDisplay(xarML('Level'));
    $data['shortdesclabel'] = xarVarPrepForDisplay(xarML('Short Course Description'));
    $data['languagelabel'] = xarVarPrepForDisplay(xarML('Language'));
    $data['freqlabel'] = xarVarPrepForDisplay(xarML('Frequency'));
    $data['contactlabel'] = xarVarPrepForDisplay(xarML('Contact details'));
    $data['hidecourselabel'] = xarVarPrepForDisplay(xarML('Hide Course'));
    $data['addcoursebutton'] = xarVarPrepForDisplay(xarML('Add Course'));
    $data['cancelbutton'] = xarVarPrepForDisplay(xarML('Cancel'));

    $data['level'] = xarModAPIFunc('courses', 'user', 'gets',
                                      array('itemtype' => 3));

    // Call hooks 
    $item = array();
    $item['module'] = 'courses';
    $item['multiple'] = false;
    $item['returnurl'] = xarModURL('courses', 'admin', 'newcourse');
    $hooks = xarModCallHooks('item', 'new', '', $item);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    // For E_ALL purposes, we need to check to make sure the vars are set.
    // If they are not set, then we need to set them empty to surpress errors
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
    xarSessionSetVar('statusmsg', '');
    // Return the template variables defined in this function
    return $data;
}

?>
