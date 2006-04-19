<?php
/**
 * Modify a course
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
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
 *
 * @param  int courseid the id of the item to be modified or
 * @param  int objectid the universal id of the item to be modified
 * @param int itemtype
 * @param array invalid
 * @return array with data for template
 */
function courses_admin_modifycourse($args)
{
    extract($args);
    // Get parameters from whatever input we need.
    if (!xarVarFetch('courseid', 'id', $courseid, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, '', XARVAR_NOT_REQUIRED)) return;
//    if (!xarVarFetch('itemtype', 'int', $itemtype, 3, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',  'array', $invalid, array(), XARVAR_NOT_REQUIRED)) return; // array?

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

    $data['coord_group'] = xarModGetVar('courses', 'coord_group');
    // Build the group name. Type 1 is a group
    $coord_group = xarModAPIFunc ('roles', 'user', 'get', array('uid'=> $data['coord_group'], 'type' =>1));

    $levels = array();
    $levels = xarModAPIFunc('courses', 'user', 'gets', array('itemtype' => 1003));

    // Return the template variables defined in this function
    // TODO: rewrite to $data
    return array('authid'           => xarSecGenAuthKey(),
                 'menutitle'        => xarVarPrepForDisplay(xarML('Edit a course')),
                 'courseid'         => $courseid,
                 'cancelbutton'     => xarVarPrepForDisplay(xarML('Cancel')),
                 'coursedata'       => $coursedata,
                 'name'             => xarVarPrepForDisplay($coursedata['name']),
                 'contactuid'       => $coursedata['contactuid'],
                 'hookoutput'       => $data['hookoutput'],
                 'group_validation' => 'group:'.$coord_group['name'],
                 'levels'           => $levels);
}

?>
