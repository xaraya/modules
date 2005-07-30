<?php
/**
 * File: $Id:
 * 
 * Standard function to view courses and their planning
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
 * view courses
 */
function courses_admin_view()
{
    // Get Vars
    xarVarFetch('itemtype', 'int', $itemtype,  3, XARVAR_NOT_REQUIRED);
    //Helpdesk: xarVarFetch('startnum', 'int', $data['startnum'],  NULL, XARVAR_NOT_REQUIRED);

    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('courses', 'admin', 'menu');

    $data['items'] = array();
    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.

    // Note that this function includes another user API function.  The
    // function returns a simple count of the total number of items in the item
    // table so that the pager function can do its job properly
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('courses', 'user', 'countitems'),
        xarModURL('courses', 'admin', 'view', array('startnum' => '%%')),
        xarModGetVar('courses', 'itemsperpage'));

    $data['itemsperpage'] = xarModGetVar('courses','itemsperpage');
    $data['itemtype'] = $itemtype;
    $data['startnum'] = $startnum;
    // The Generic Menu
    $data['menu']      = xarModFunc('courses','admin','menu');
    $data['menutitle'] = xarVarPrepForDisplay(xarML('View the hooked dynamic data options'));

    if (empty($data['itemtype'])){
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'item type', 'admin', 'view', 'courses');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }
    if (!xarSecurityCheck('EditCourses')) return;

    // Specify some labels for display
    //$data['namelabel'] = xarVarPrepForDisplay(xarML('Course Name'));
    //$data['numberlabel'] = xarVarPrepForDisplay(xarML('Course Number'));
    //$data['optionslabel'] = xarVarPrepForDisplay(xarML('Course Options'));
    // Return the template variables defined in this function
    return $data;
}

?>
