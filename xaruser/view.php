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
 */
function courses_user_view()
{
    if (!xarVarFetch('startnum', 'int:1:', $startnum, '1',  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('catid',    'isset',  $catid,    NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('sortby',   'str:1:', $sortby,   'name')) return;

    $data = xarModAPIFunc('courses', 'user', 'menu');
    // Prepare the variable that will hold some status message if necessary
    $data['status'] = '';
    // Prepare the array variable that will hold all items for display
    $data['items'] = array();
    // Specify some other variables for use in the function template
    $data['namelabel'] = xarVarPrepForDisplay(xarML('Course Name'));
    $data['numberlabel'] = xarVarPrepForDisplay(xarML('Course Number'));
    $data['coursetypelabel'] = xarVarPrepForDisplay(xarML('Course Type (Category)'));
    $data['levellabel'] = xarVarPrepForDisplay(xarML('Course Level'));
    $data['creditslabel'] = xarVarPrepForDisplay(xarML('Course Credits'));
    $data['creditsminlabel'] = xarVarPrepForDisplay(xarML('Course Minimum Credits'));
    $data['creditsmaxlabel'] = xarVarPrepForDisplay(xarML('Course Maximum Credits'));
    $data['prereqlabel'] = xarVarPrepForDisplay(xarML('Course Prerequisites'));
    $data['aimlabel'] = xarVarPrepForDisplay(xarML('Course Aim'));
    $data['shortdesclabel'] = xarVarPrepForDisplay(xarML('Short Course Description'));
    $data['methodlabel'] = xarVarPrepForDisplay(xarML('Course Method'));
    $data['intendedcreditslabel'] = xarVarPrepForDisplay(xarML('Course credits'));
    $data['freqlabel'] = xarVarPrepForDisplay(xarML('Course Frequency'));
    $data['startdate_label'] = xarVarPrepForDisplay(xarML('Course Start Date'));
    $data['enddate_label'] = xarVarPrepForDisplay(xarML('Course End Date'));
    $data['shortdesc_label'] = xarVarPrepForDisplay(xarML('Short Course Description'));
    $data['longdesc_label'] = xarVarPrepForDisplay(xarML('Course Description:'));
    $data['pager'] = '';

    // Security check
    if (!xarSecurityCheck('ViewCourses')) return;
    // Lets get the UID of the current user to check for overridden defaults
    $uid = xarUserGetVar('uid');
    // The API function is called.
    $items = xarModAPIFunc('courses',
        'user',
        'getall',
        array('startnum' => $startnum,
              'numitems' => xarModGetUserVar('courses','itemsperpage',$uid),
              'sortby' => $sortby,
              'catid' => $catid));
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Loop through each item and display it.
    foreach ($items as $item) {
         $name=$item['name'];
         $courseid = $item['courseid'];
        if (xarSecurityCheck('ReadCourses', 0, 'Course', "$courseid:All:All")) {
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

    // Create sort by URLs
    if ($sortby != 'name' ) {
        $data['snamelink'] = xarModURL('courses',
                                       'user',
                                       'view',
                                       array('startnum' => 1,
                                             'sortby' => 'name',
                                             'catid' => $catid));
    } else {
        $data['snamelink'] = '';
    }
    if ($sortby != 'shortdesc' ) {
        $data['sdesclink'] = xarModURL('courses',
                                       'user',
                                       'view',
                                       array('startnum' => 1,
                                             'sortby' => 'shortdesc',
                                             'catid' => $catid));
    } else {
        $data['sdesclink'] = '';
    }
    if ($sortby != 'number' ) {
        $data['snumberlink'] = xarModURL('courses',
                                       'user',
                                       'view',
                                       array('startnum' => 1,
                                             'sortby' => 'number',
                                             'catid' => $catid));
    } else {
        $data['snumberlink'] = '';
    }

    // Pager
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('courses', 'user', 'countitems', array('catid' => $catid)),
        xarModURL('courses', 'user', 'view', array('startnum' => '%%','sortby' => $sortby, 'catid' => $catid)),
        xarModGetUserVar('courses', 'itemsperpage', $uid));

    $data['ShowShortDescchecked'] = xarModGetVar('courses', 'ShowShortDesc') ? 'checked="checked"' : '';


    // Changing the name of the page
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('View Courses')));
    return $data;
}

?>
