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
    if (!xarVarFetch('itemtype', 'int', $itemtype, 3, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str:1:', $invalid, '', XARVAR_NOT_REQUIRED)) return;
    
    // At this stage we check to see if we have been passed $objectid, the
    // generic item identifier.
    
    if (!empty($objectid)) {
        $courseid = $objectid;
    }
    // Get the course
    $coursedata = xarModAPIFunc('courses',
                          'user',
                          'get',
                          array('courseid' => $courseid));
    // Check for exceptions
    if (!isset($coursedata) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    $name = $coursedata['name'];
    // Security check
    if (!xarSecurityCheck('EditCourses', 1, 'Course', "$name:All:$courseid")) {
        return;
    }
    // Get menu variables
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
    return array('authid'       => xarSecGenAuthKey(),
                 'menutitle'    => xarVarPrepForDisplay(xarML('Edit a course')),
                 'courseid'     => $courseid,
                 'namelabel'    => xarVarPrepForDisplay(xarML('Course Name')),
                 'lastmodilabel' => xarVarPrepForDisplay(xarML('Last Modified')),
                 'numberlabel'  => xarVarPrepForDisplay(xarML('Course Number')),
                 'freqlabel'    => xarVarPrepForDisplay(xarML('Course frequency')),
                 'coursetypelabel'  => xarVarPrepForDisplay(xarML('Course Type (Category)')),
                 'levellabel'   => xarVarPrepForDisplay(xarML('Course Level')),
                 'languagelabel' => xarVarPrepForDisplay(xarML('Language')),
                 'shortdesclabel'  => xarVarPrepForDisplay(xarML('Short Description')),
                 'contactlabel' => xarVarPrepForDisplay(xarML('Course Contact details')),
                 'invalid'      => $invalid,
                 'hidecourselabel' => xarVarPrepForDisplay(xarML('Hide Course')),
                 'updatebutton' => xarVarPrepForDisplay(xarML('Update Course')),
                 'cancelbutton' => xarVarPrepForDisplay(xarML('Cancel')),
                 'hooks'        => $hooks,
                 'coursedata'   => $coursedata,
                 'levels'       => $levels);
}

?>
