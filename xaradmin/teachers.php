`<?php
/**
 * File: $Id:
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author Courses module development team 
 */
 
/**
 * view teachers for one planned course
 * @param ['planningid'] ID of the planned course
 * @param ['startnum']
 */
function courses_admin_teachers()
{

    if (!xarVarFetch('startnum', 'int:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('planningid', 'int:1:', $planningid)) return;
    // Initialise the $data variable
    $data = xarModAPIFunc('courses', 'admin', 'menu');
    // Initialise the variable that will hold the items, so that the template
    // doesn't need to be adapted in case of errors
    $data['items'] = array();

    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('courses', 'user', 'countitems'), //TODO make count function
        xarModURL('courses', 'admin', 'teachers', array('startnum' => '%%')),
        xarModGetVar('courses', 'itemsperpage'));
    
    // Security check
    if (!xarSecurityCheck('EditPlanning')) return;

    $items = xarModAPIFunc('courses',
        'admin',
        'getallteachers',
        array('startnum' => $startnum,
              'numitems' => xarModGetVar('courses','itemsperpage'),
              'planningid' => $planningid
              ));
    // Check for exceptions
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
//What is needed here?

        if (xarSecurityCheck('EditPlanning', 0, 'Item', "All:All:All")) {
            $items[$i]['changeurl'] = xarModURL('courses',
                'admin',
                'changeteacher',
                array('tid' => $item['tid']));
        } else {
            $items[$i]['changeurl'] = '';
        }

        $items[$i]['changetitle'] = xarML('Change');
        // Change for type of teacher
 //       $items[$i]['statusname'] = xarModAPIFunc('courses', 'user', 'getstatus',
 //                                    array('status' => $item['status']));
        $items[$i]['selected']='';
        
        if (xarSecurityCheck('AdminPlanning', 0, 'Item', "$planningid:All:All")) {
            $items[$i]['deleteurl'] = xarModURL('courses',
                'admin',
                'deleteteacher',
                array('tid' => $item['tid'],
                'planningid' => $planningid));
        } else {
            $items[$i]['deleteurl'] = '';
        }
        $items[$i]['deletetitle'] = xarML('Remove teacher');
    }
    
    // Add the array of items to the template variables
    $data['items'] = $items;
    $data['planningid'] = $planningid;
    // Specify some labels for display
    $data['namelabel'] = xarVarPrepForDisplay(xarML('Teacher Name'));
    $data['emaillabel'] = xarVarPrepForDisplay(xarML('E-mail address'));
    $data['typelabel'] = xarVarPrepForDisplay(xarML('Type'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarML('Options'));
    $data['changelabel'] = xarVarPrepForDisplay(xarML('Change status'));
    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add teacher'));
    // Return the template variables defined in this function
    return $data;
}

?>
