<?php
/**
 * Modify a course
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * Modify a course
 *
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 * @author MichelV <michelv@xarayahosting.nl>
 * @author Courses module development team
 *
 * @param  $ 'courseid' the id of the item to be modified
 */
function courses_admin_modifycourse($args)
{
    extract($args);
    // Get parameters from whatever input we need.
    if (!xarVarFetch('courseid', 'id', $courseid, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemtype', 'int', $itemtype, 3, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',  'str:1:', $invalid, '', XARVAR_NOT_REQUIRED)) return; // array?

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

    // Security check
    if (!xarSecurityCheck('EditCourses', 1, 'Course', "$courseid:All:All")) {
        return;
    }
    // Call hooks
    $coursedata['module'] = 'courses';
    $coursedata['itemtype'] = $coursedata['coursetype'];
    $hooks = xarModCallHooks('item', 'modify', $courseid, $coursedata);
    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        /* You can use the output from individual hooks in your template too, e.g. with
         * $hookoutput['categories'], $hookoutput['dynamicdata'], $hookoutput['keywords'] etc.
         */
        $data['hookoutput'] = $hooks;
    }

    $levels = array();
    $levels = xarModAPIFunc('courses', 'user', 'gets', array('itemtype' => 3));

    // Return the template variables defined in this function
    // TODO: rewrite to $data
    return array('authid'           => xarSecGenAuthKey(),
                 'menutitle'        => xarVarPrepForDisplay(xarML('Edit a course')),
                 'courseid'         => $courseid,
                 'namelabel'        => xarVarPrepForDisplay(xarML('Course Name')),
                 'lastmodilabel'    => xarVarPrepForDisplay(xarML('Last Modified')),
                 'numberlabel'      => xarVarPrepForDisplay(xarML('Course Number')),
                 'freqlabel'        => xarVarPrepForDisplay(xarML('Course frequency')),
                 'coursetypelabel'  => xarVarPrepForDisplay(xarML('Course Type (other than Category)')),
                 'levellabel'       => xarVarPrepForDisplay(xarML('Course Level')),
                 'intendedcreditslabel' => xarVarPrepForDisplay(xarML('Intended credits')),
                 'shortdesclabel'   => xarVarPrepForDisplay(xarML('Short Description')),
                 'contactlabel'     => xarVarPrepForDisplay(xarML('Course Contact details')),
                 'contactuidlabel'  => xarVarPrepForDisplay(xarML('Course Coordinator uid')),
                 'invalid'          => $invalid,
                 'hidecourselabel'  => xarVarPrepForDisplay(xarML('Hide Course')),
                 'updatebutton'     => xarVarPrepForDisplay(xarML('Update Course')),
                 'cancelbutton'     => xarVarPrepForDisplay(xarML('Cancel')),
                 'coursedata'       => $coursedata,
                 'name'             => $coursedata['name'],
                 'contactuid'       => $coursedata['contactuid'],
                 'hookoutput'       => $data['hookoutput'],
                 'levels'           => $levels);
}

?>
