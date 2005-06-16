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
 * view all courses
 */
function courses_admin_viewcourses()
{
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    // Initialise the $data variable
    $data = xarModAPIFunc('courses', 'admin', 'menu');
    // Initialise the variable that will hold the items, so that the template
    // doesn't need to be adapted in case of errors
    $data['items'] = array();
    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.

    // Note that this function includes another user API function.  The
    // function returns a simple count of the total number of items in the item
    // table so that the pager function can do its job properly
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('courses', 'user', 'countitems'),
        xarModURL('courses', 'admin', 'viewcourses', array('startnum' => '%%')),
        xarModGetVar('courses', 'itemsperpage'));
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('EditCourses')) return;
    // The user API function is called.  This takes the number of items
    // required and the first number in the list of all items, which we
    // obtained from the input and gets us the information on the appropriate
    // items.
    $items = xarModAPIFunc('courses',
        'user',
        'getall',
        array('startnum' => $startnum,
            'numitems' => xarModGetVar('courses',
                'itemsperpage')));
    // Check for exceptions
//    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Check individual permissions for Edit / Delete
    // Note : we could use a foreach ($items as $item) here as well, as
    // shown in xaruser.php, but as an example, we'll adapt the $items array
    // 'in place', and *then* pass the complete items array to $data
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        if (xarSecurityCheck('AddPlanning', 0, 'Item', "All:All:$item[courseid]")) {
            $items[$i]['planurl'] = xarModURL('courses',
                'admin',
                'plancourse',
                array('courseid' => $item['courseid']));
        } else {
            $items[$i]['planurl'] = '';
        }
        $items[$i]['plantitle'] = xarML('Plan');
        if (xarSecurityCheck('EditCourses', 0, 'Item', "$item[name]:All:$item[courseid]")) {
            $items[$i]['editurl'] = xarModURL('courses',
                'admin',
                'modifycourse',
                array('courseid' => $item['courseid']));
        } else {
            $items[$i]['editurl'] = '';
        }
        $items[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteCourses', 0, 'Item', "$item[name]:All:$item[courseid]")) {
            $items[$i]['deleteurl'] = xarModURL('courses',
                'admin',
                'deletecourse',
                array('courseid' => $item['courseid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }
        $items[$i]['deletetitle'] = xarML('Delete');
    }
    // Add the array of items to the template variables
    $data['items'] = $items;
    // Specify some labels for display
    $data['namelabel'] = xarVarPrepForDisplay(xarML('Course Name'));
    $data['numberlabel'] = xarVarPrepForDisplay(xarML('Course Number'));
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
