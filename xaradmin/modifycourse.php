<?php
/**
 * File: $Id:
 * 
 * Standard function to modify an item
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
 * modify a course
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 * 
 * @param  $ 'courseid' the id of the item to be modified
 */
function courses_admin_modifycourse($args)
{
    extract($args);
    // Get parameters from whatever input we need.
    if (!xarVarFetch('courseid', 'isset:', $courseid, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    //if (!xarVarFetch('name', 'str:1:', $name, '', XARVAR_NOT_REQUIRED)) return;
    //if (!xarVarFetch('number', 'str:1:', $number, '',XARVAR_NOT_REQUIRED)) return;
    //if (!xarVarFetch('coursetype', 'str:1:', $coursetype, '', XARVAR_NOT_REQUIRED)) return;
    //if (!xarVarFetch('shortdesc', 'str:1:', $shortdesc, '', XARVAR_NOT_REQUIRED)) return;
    //if (!xarVarFetch('language', 'str:1:', $language, '', XARVAR_NOT_REQUIRED)) return;
    //if (!xarVarFetch('freq', 'str:1:', $freq, '', XARVAR_NOT_REQUIRED)) return;
    //if (!xarVarFetch('contact', 'str:1:', $contact, '', XARVAR_NOT_REQUIRED)) return;
    //if (!xarVarFetch('hidecourse', 'str:1:', $hidecourse, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemtype', 'int', $itemtype, 3, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str:1:', $invalid, '', XARVAR_NOT_REQUIRED)) return;
    // At this stage we check to see if we have been passed $objectid, the
    // generic item identifier.  This could have been passed in by a hook or
    // through some other function calling this as part of a larger module, but
    // if it exists it overrides $courseid

    if (!empty($objectid)) {
        $courseid = $objectid;
    }
    // The user API function is called.  This takes the item ID which we
    // obtained from the input and gets us the information on the appropriate
    // item.  If the item does not exist we post an appropriate message and
    // return
    $coursedata = xarModAPIFunc('courses',
                          'user',
                          'getcourse',
                          array('courseid' => $courseid));
    // Check for exceptions
    if (!isset($coursedata) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing.  However,
    // in this case we had to wait until we could obtain the item name to
    // complete the instance information so this is the first chance we get to
    // do the check
    if (!xarSecurityCheck('EditCourses', 1, 'Item', "$coursedata[name]:All:$courseid")) {
        return;
    }
    // Get menu variables - it helps if all of the module pages have a standard
    // menu at their head to aid in navigation
    // $menu = xarModAPIFunc('example','admin','menu','modify');
    $coursedata['module'] = 'courses';
    $hooks = xarModCallHooks('item', 'modify', $courseid, $coursedata);
    if (empty($hooks)) {
        $hooks = '';
    } elseif (is_array($hooks)) {
        $hooks = join('', $hooks);
    }
	$levels = array();
    $levels = xarModAPIFunc('courses', 'user', 'gets', array('itemtype' => 3));
	
    // Return the template variables defined in this function
    //$dateformat = '%Y-%m-%d %H:%M:%S';
    //$startdate = xarLocaleFormatDate($dateformat, $startdate);
    //$enddate = xarLocaleFormatDate($dateformat, $enddate);
    return array('authid'       => xarSecGenAuthKey(),
	             'menutitle'    => xarVarPrepForDisplay(xarML('Edit a course')),
				 'courseid'     => $courseid,
                 'namelabel'    => xarVarPrepForDisplay(xarML('Course Name')),
              //   'name'         => $coursedata['name'],
                 'numberlabel'  => xarVarPrepForDisplay(xarML('Course Number')),
               //  'number'       => $item['number'],
                 'freqlabel'    => xarVarPrepForDisplay(xarML('Course frequency')),
               //  'freq'         => $item['freq'],
                 'coursetypelabel'  => xarVarPrepForDisplay(xarML('Course Type (Category)')),
              //   'coursetype'   => $item['coursetype'],
                 'levellabel'   => xarVarPrepForDisplay(xarML('Course Level')),
              //  'level'        => $item['level'],
                 'languagelabel' => xarVarPrepForDisplay(xarML('Language')),
              //   'language'     => $item['language'],
                 'shortdesclabel'  => xarVarPrepForDisplay(xarML('Short Description')),
              //   'shortdesc'    => $item['shortdesc'],
                 'contactlabel' => xarVarPrepForDisplay(xarML('Course Contact details')),
              //   'contact'      => $item['contact'],
                 'invalid'      => $invalid,
                 'hidecourselabel' => xarVarPrepForDisplay(xarML('Hide Course')),
				// 'hidecourse'   => $item['hidecourse'],
                 'updatebutton' => xarVarPrepForDisplay(xarML('Update Course')),
				 'cancelbutton' => xarVarPrepForDisplay(xarML('Cancel')),
                 'hooks'        => $hooks,
				 'coursedata'   => $coursedata,
                 //'item'         => $item,
				 'levels'       => $levels);
}

?>
