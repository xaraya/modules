`<?php
/**
 * Standard all planned courses
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
 * View all planned courses
 *
 * @author Michel V.
 * @param startnum
 */
function courses_admin_viewallplanned()
{
    if (!xarVarFetch('startnum', 'int:1:', $startnum,  '1',            XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('catid',    'isset',  $catid,     NULL,           XARVAR_DONT_SET))     return;
    if (!xarVarFetch('sortby',   'str:1:', $sortby,    'planningid',         XARVAR_NOT_REQUIRED)) return; 
    if (!xarVarFetch('sortorder','enum:DESC:ASC:', $sortorder,'DESC',  XARVAR_NOT_REQUIRED)) return;        

    // Initialise the $data variable
    $data = xarModAPIFunc('courses', 'admin', 'menu');
    // Initialise the variable that will hold the items, so that the template
    // doesn't need to be adapted in case of errors
    $data['items'] = array();
    // Call the xarTPL helper function to produce a pager in case
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('courses', 'user', 'countplanned'),
        xarModURL('courses', 'admin', 'viewallplanned', array('startnum' => '%%',
                                                              'catid' => $catid, 
                                                              'sortorder' =>$sortorder, 
                                                              'sortby' =>$sortby)),
        xarModGetVar('courses', 'itemsperpage'));
    // Security check - High level because we are nearly admin here
    if (!xarSecurityCheck('EditCourses')) return;
    
    // Get all planned courses 
    $items = xarModAPIFunc('courses',
                           'user',
                           'getallplanned',
                           array('startnum'  => $startnum,
                                 'numitems'  => xarModGetVar('courses','itemsperpage'),
                                 'sortorder' => $sortorder,
                                 'sortby'    => $sortby
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
        $planningid = $item['planningid'];
		$hideplanning = $item['hideplanning'];
        if (xarSecurityCheck('EditCourses', 0, 'Course',"All:$planningid:All")) { 
            $items[$i]['editurl'] = xarModURL('courses',
                'admin',
                'modifyplanned',
                array('planningid' => $planningid));
        } else {
            $items[$i]['editurl'] = '';
        }
        $items[$i]['edittitle'] = xarML('Edit');
        
        if (xarSecurityCheck('EditCourses', 0, 'Course', "All:$planningid:All")) {
            $items[$i]['participantsurl'] = xarModURL('courses',
                'admin',
                'participants',
                array('planningid' => $planningid));
        } else {
            $items[$i]['participantsurl'] = '';
        }
        $items[$i]['participants'] = xarModAPIFunc('courses', 
                                                   'user',
                                                   'countparticipants',
                                                    array('planningid' => $planningid)
                                                    );

        if (xarSecurityCheck('ViewCourses', 0, 'Course', "All:$planningid:All")) {
            $items[$i]['displayurl'] = xarModURL('courses',
                'user',
                'displayplanned',
                array('planningid' => $planningid));
        } else {
            $items[$i]['displayurl'] = '';
        }
        
        if (xarSecurityCheck('EditCourses', 0, 'Course', "All:$planningid:All")) {
            $items[$i]['teachersurl'] = xarModURL('courses',
                'admin',
                'teachers',
                array('planningid' => $planningid));
        } else {
            $items[$i]['teachersurl'] = '';
        }
        $items[$i]['teacherstitle'] = xarML('Teachers');
        
        $course = xarModAPIFunc('courses','user','get',array('courseid' => $item['courseid']));
        $items[$i]['name'] = xarVarPrepForDisplay($course['name']);
        $items[$i]['startdate'] = xarVarPrepForDisplay(xarLocaleFormatDate($items[$i]['startdate']));
        $items[$i]['enddate'] = xarVarPrepForDisplay(xarLocaleFormatDate($items[$i]['enddate']));
    // End for()
    }
    
    // Add the array of items to the template variables
    $data['items'] = $items;
    }
    // Labels for display
    $data['namelabel'] = xarVarPrepForDisplay(xarML('Course Name'));
    $data['numberlabel'] = xarVarPrepForDisplay(xarML('Course Number'));
    $data['startdatelabel'] = xarVarPrepForDisplay(xarML('Startdate'));
    $data['enddatelabel'] = xarVarPrepForDisplay(xarML('Enddate'));
    $data['participantslabel'] = xarVarPrepForDisplay(xarML('Participants'));
    $data['minmaxparticipantslabel'] = xarVarPrepForDisplay(xarML('Min/Max Participants'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarML('Course Options'));
    // Return the template variables defined in this function
    return $data;
}

?>
