`<?php
/**
 * View Participants for a course
 *
 * @package modules
 * @copyright (C) 2003-2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * View participants for one planned course
 *
 * Display the participants and be able to change their status in one movement
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @access public
 * @param int planningid ID of the planned course
 * @param int startnum
 * @return array
 */
function courses_admin_participants()
{
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('planningid', 'id',   $planningid)) return;
    // Initialise the $data variable
    $data = xarModAPIFunc('courses', 'admin', 'menu');
    // Initialise the variable that will hold the items, so that the template
    // doesn't need to be adapted in case of errors
    $data['items'] = array();

    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('courses', 'user', 'countparticipants', array('planningid'=>$planningid)),
        xarModURL('courses', 'admin', 'participants', array('startnum' => '%%', 'planningid'=>$planningid)),
        xarModGetVar('courses', 'itemsperpage'));

    // Security check
    if (!xarSecurityCheck('EditCourses', 0, 'Course', "All:$planningid:All")) return;

    $items = xarModAPIFunc('courses',
        'admin',
        'getallparticipants',
        array('startnum' => $startnum,
              'numitems' => xarModGetVar('courses','itemsperpage'),
              'planningid' => $planningid
              ));
    // Check for exceptions
//    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];

        if (xarSecurityCheck('EditCourses', 0, 'Course', "All:$planningid:All")) {
            $items[$i]['changestatusurl'] = xarModURL('courses',
                'admin',
                'changestatus',
                array('sid' => $item['sid']));
        } else {
            $items[$i]['changestatusurl'] = '';
        }
        $items[$i]['changestatustitle'] = xarML('Change Status');
        $items[$i]['statusname'] = xarModAPIFunc('courses', 'user', 'getstatus',
                                      array('status' => $item['status']));
        $items[$i]['selected']='';

        if (xarSecurityCheck('EditCourses', 0, 'Course', "All:$planningid:All")) {
            $items[$i]['deleteurl'] = xarModURL('courses',
                'admin',
                'deleteparticipant',
                array('sid' => $item['sid'],
                'planningid' => $planningid));
        } else {
            $items[$i]['deleteurl'] = '';
        }
        $items[$i]['deletetitle'] = xarML('Remove participant');
    }

    $data['status'] = xarModAPIFunc('courses', 'user', 'gets',
                                      array('itemtype' => 1004));

    // Add the array of items to the template variables
    $data['items'] = $items;
    $data['planningid'] = $planningid;
    $data['coursename'] = xarModApiFunc('courses','user','getcoursename',array('planningid'=> $planningid));
    // Return the template variables defined in this function
    return $data;
}

?>
