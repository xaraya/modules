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
 * 
 * view a list of courses that the current user is attached to
 *
 * @author Michel V.
 */
function courses_user_viewmycourses()
{
    // Security check
    if (!xarSecurityCheck('ViewCourses', 0)) {
        return $data['error'] = xarML('You must be a registered user to view courses...');
    }
    // Get startparameter
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
    $data['startdate_label'] = xarVarPrepForDisplay(xarML('Start Date'));
    $data['enddate_label'] = xarVarPrepForDisplay(xarML('End Date'));
    $data['shortdesc_label'] = xarVarPrepForDisplay(xarML('Short Course Description'));
    $data['longdesc_label'] = xarVarPrepForDisplay(xarML('Course Description:'));
    $data['pager'] = '';

    // Lets get the UID of the current user to check for overridden defaults
    $uid = xarUserGetVar('uid');
    // The API function is called.  The arguments to the function are passed in
    // as their own arguments array.
    // Security check 1 - the getall() function only returns items for which the
    // the user has at least OVERVIEW access.
    $items = xarModAPIFunc('courses',
        'user',
        'getall_enrolled',
        array('startnum' => $startnum,
              'numitems' => xarModGetUserVar('courses','itemsperpage',$uid))
        );
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // TODO: check for conflicts between transformation hook output and xarVarPrepForDisplay
    // Loop through each item and display it.
    foreach ($items as $item) {

        if (xarSecurityCheck('ReadPlanning', 0, 'Item', "$item[name]:All:$item[planningid]")) {
            $item['link'] = xarModURL('courses',
                'user',
                'displayplanned',
                array('planningid' => $item['planningid']));
            // Security check 2 - else only display the item name (or whatever is
            // appropriate for your module)
        } else {
            $item['link'] = '';
        }
        // Clean up the item text before display
        $item['name'] = xarVarPrepForDisplay($item['name']);
		$item['startdate'] = xarVarPrepForDisplay($item['startdate']);
        //$item['shortdesc'] = xarVarPrepHTMLDisplay($item['shortdesc']);
        // Add this item to the list of items to be displayed
        $data['items'][] = $item;
    }
    // Pager
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('courses', 'user', 'countitems'),
        xarModURL('courses', 'user', 'view', array('startnum' => '%%')),
        xarModGetUserVar('courses', 'itemsperpage', $uid));
    // Specify some other variables for use in the function template
    // Same as above.  We are changing the name of the page to raise
    // better search engine compatibility.
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('My Courses')));
    // Return the template variables defined in this function
    return $data;
    // Note : instead of using the $data variable, you could also specify
    // the different template variables directly in your return statement :

    // return array('menu' => ...,
    // 'items' => ...,
    // 'pager' => ...,
    // ... => ...);
}

?>
