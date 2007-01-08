<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * Add new course
 *
 * This is a standard function that is called whenever an administrator
 * wishes to create a new course
 *
 * @author MichelV
 * @return array
 */
function courses_admin_newcourse($args)
{
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('name',            'str:1:', $name,        '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('number',          'str:1:', $number,      '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('coursetype',      'int:1:', $coursetype,  1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shortdesc',       'str:1:', $shortdesc,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('intendedcredits', 'float::', $intendedcredits, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('freq',            'str:1:', $freq,        '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contact',         'str:1:', $contact,     '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactuid',      'int:1:', $contactuid,  '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hidecourse',      'int:1:', $hidecourse,  '', XARVAR_NOT_REQUIRED)) return;
    //if (!xarVarFetch('level', 'isset::', $level, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',         'array',  $invalid,     array(), XARVAR_NOT_REQUIRED)) return;
    // TODO: make the menu usefull
    $data = xarModAPIFunc('courses', 'admin', 'menu');

    // Security check
    if (!xarSecurityCheck('AddCourses')) return;

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    $data['invalid'] = $invalid;
    // Specify some labels for display
    $data['addcoursebutton'] = xarVarPrepForDisplay(xarML('Add Course'));
    $data['cancelbutton'] = xarVarPrepForDisplay(xarML('Cancel'));

    $data['level'] = xarModAPIFunc('courses', 'user', 'gets',
                                      array('itemtype' => 1003));
    // Standard course coordinator group
    $coord_group = xarModGetVar('courses', 'coord_group');
    // Build the group name. Type 1 is a group
    $coord_group = xarModAPIFunc ('roles', 'user', 'get', array('uid'=> $coord_group, 'type' =>1));
    $data['group_validation'] = 'group:'.$coord_group['name'];
    // Call hooks for new course, with coursetype as the itemtype
    $item = array();
    $item['module'] = 'courses';
    $item['returnurl'] = xarModURL('courses', 'admin', 'newcourse');
    $item['itemtype'] = $coursetype; // Coursetypes
    $hooks = xarModCallHooks('item', 'new', '', $item);
    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }
    $ctype = xarModApiFunc('courses','user','gettype',array('tid'=> $coursetype));
    $data['coursetype'] = $coursetype;
    $data['ctypename'] = $ctype['coursetype'];
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

     if (empty($shortdesc)) {
        $data['shortdesc'] = '';
    } else {
        $data['shortdesc'] = $shortdesc;
    }

    if (empty($intendedcredits)) {
        $data['intendedcredits'] = '';
    } else {
        $data['intendedcredits'] = $intendedcredits;
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
    xarSessionSetVar('statusmsg', '');
    // Return the template variables defined in this function
    return $data;
}

?>
