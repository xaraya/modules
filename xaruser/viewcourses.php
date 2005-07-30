<?php
/**
 * File: $Id:
 * 
 * View a list of items
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
 * view a list of courses
 * This is a standard function to provide an overview of all of the items
 * available from the module.
 * @author MichelV
 * @access Public
 */
function courses_user_viewcourses()
{
    // Security check
    if (!xarSecurityCheck('ViewCourses', 0)) {
        return $data['error'] = xarML('You must be a registered user to view courses...');
    }
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('courses', 'user', 'menu');
    // Prepare the variable that will hold some status message if necessary
    $data['status'] = '';
    // Prepare the array variable that will hold all items for display
    $data['items'] = array();
    // Specify some other variables for use in the function template
    $data['name_label'] = xarVarPrepForDisplay(xarML('Course Name'));
    $data['number_label'] = xarVarPrepForDisplay(xarML('Course Number'));
    $data['hours_label'] = xarVarPrepForDisplay(xarML('Course Hours'));
    $data['ceu_label'] = xarVarPrepForDisplay(xarML('Course Credit Hours'));
    $data['startdate_label'] = xarVarPrepForDisplay(xarML('Course Start Date'));
    $data['enddate_label'] = xarVarPrepForDisplay(xarML('Course End Date'));
    $data['shortdesc_label'] = xarVarPrepForDisplay(xarML('Short Course Description'));
    $data['longdesc_label'] = xarVarPrepForDisplay(xarML('Course Description:'));
    $data['pager'] = '';

    $uid = xarUserGetVar('uid');
    $items = xarModAPIFunc('courses',
        'user',
        'getall',
        array('startnum' => $startnum,
              'numitems' => xarModGetUserVar('courses','itemsperpage',$uid))
        );
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Loop through each item and display it.
    foreach ($items as $item) {
        $name = $item['name'];
        $courseid = $item['courseid'];
        if (xarSecurityCheck('ReadCourses', 0, 'Course', "$name:All:$courseid")) {
            $item['link'] = xarModURL('courses',
                'user',
                'display',
                array('courseid' => $item['courseid']));
            // Security check 2 - else only display the item name (or whatever is
            // appropriate for your module)
        } else {
            $item['link'] = '';
        }
        // Clean up the item text before display
        $item['name'] = xarVarPrepForDisplay($item['name']);
        $item['shortdesc'] = xarVarPrepHTMLDisplay($item['shortdesc']);
        // Add this item to the list of items to be displayed
        $data['items'][] = $item;
    }
    // Pager
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('courses', 'user', 'countitems'),
        xarModURL('courses', 'user', 'view', array('startnum' => '%%')),
        xarModGetUserVar('courses', 'itemsperpage', $uid));
    // Same as above.  We are changing the name of the page to raise
    // better search engine compatibility.
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('View Courses')));
    // Return the template variables defined in this function
    return $data;
}

?>
