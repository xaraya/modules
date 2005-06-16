`<?php
/**
 * File: $Id:
 * 
 * Standard function to view items
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
 * View all planned courses
 *
 * @author Michel V.
 * @param startnum
 */
function courses_admin_viewallplanned()
{
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    // Initialise the $data variable
    $data = xarModAPIFunc('courses', 'admin', 'menu');
    // Initialise the variable that will hold the items, so that the template
    // doesn't need to be adapted in case of errors
    $data['items'] = array();
    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.

    // TODO Counter
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('courses', 'user', 'countitems'),
        xarModURL('courses', 'admin', 'viewcourses', array('startnum' => '%%')),
        xarModGetVar('courses', 'itemsperpage'));
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('EditCourses')) return;
    
    // Get all planned courses 
    $items = xarModAPIFunc('courses',
        'user',
        'getallplanned',
        array('startnum' => $startnum,
              'numitems' => xarModGetVar('courses','itemsperpage')
              ));
    // Check for exceptions
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // Quick check for emptyness...
    if (count($items) == 0){return;
    }
    else {

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        if (xarSecurityCheck('EditPlanning', 0, 'Planning',"All:All:All")) { //Why did the appointment of $item['courseid'] not work here?
            $items[$i]['editurl'] = xarModURL('courses',
                'admin',
                'editplanned',
                array('planningid' => $item['planningid']));
        } else {
            $items[$i]['editurl'] = '';
        }
        $items[$i]['edittitle'] = xarML('Edit');
        
        if (xarSecurityCheck('EditPlanning', 0, 'Planning', "$item[planningid]:All:$item[courseid]")) {
            $items[$i]['participantsurl'] = xarModURL('courses',
                'admin',
                'participants',
                array('planningid' => $item['planningid']));
        } else {
            $items[$i]['participantsurl'] = '';
        }
        $items[$i]['participants'] = xarModAPIFunc('courses', 
                                                   'user',
                                                   'countparticipants',
                                                    array('planningid' => $item['planningid'])
                                                    );

        if (xarSecurityCheck('ViewCourses', 0, 'Course', "All:All:$item[courseid]")) {
            $items[$i]['displayurl'] = xarModURL('courses',
                'user',
                'display',
                array('courseid' => $item['courseid']));
        } else {
            $items[$i]['displayurl'] = '';
        }
        
        if (xarSecurityCheck('EditPlanning', 0, 'Planning', "All:All:$item[courseid]")) {
            $items[$i]['teachersurl'] = xarModURL('courses',
                'admin',
                'teachers',
                array('planningid' => $item['planningid']));
        } else {
            $items[$i]['teachersurl'] = '';
        }
        $items[$i]['teacherstitle'] = xarML('Teachers');
        
        $course = xarModAPIFunc('courses','user','getcourse',array('courseid' => $item['courseid']));
        $items[$i]['name'] = xarVarPrepForDisplay($course['name']);
    // End for()
    }
    
    // Add the array of items to the template variables
    $data['items'] = $items;
    }
    // Specify some labels for display
    $data['namelabel'] = xarVarPrepForDisplay(xarML('Course Name'));
    $data['numberlabel'] = xarVarPrepForDisplay(xarML('Course Number'));
    $data['startdatelabel'] = xarVarPrepForDisplay(xarML('Startdate'));
    $data['enddatelabel'] = xarVarPrepForDisplay(xarML('Enddate'));
    $data['participantslabel'] = xarVarPrepForDisplay(xarML('Participants'));
    $data['minmaxparticipantslabel'] = xarVarPrepForDisplay(xarML('Min/Max Participants'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarML('Course Options'));
    // Return the template variables defined in this function
    return $data;
    // Note : instead of using the $data variable, you could also specify
    // the different template variables directly in your return statement :

    // return array('items' => ...,
    // 'namelabel' => ...,
    // ... => ...);
}

?>
