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
 * @subpackage example
 * @author Example module development team 
 */
/**
 * view a list of items
 * This is a standard function to provide an overview of all of the items
 * available from the module.
 */
function courses_user_view()
{
    // Security check
    if (!xarSecurityCheck('ViewCourses', 0)) {
        return $data['error'] = xarML('You must be a rgisters user to view courses..');
    }
	// Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarFetch(), xarVarCleanFromInput()
    // is a degraded function.  xarVarFetch allows the checking of the input
    // variables as well as setting default values if needed.  Getting vars
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    // Initialise the $data variable that will hold the data to be used in
    // the blocklayout template, and get the common menu configuration - it
    // helps if all of the module pages have a standard menu at the top to
    // support easy navigation
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
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('ViewCourses')) return;
    // Lets get the UID of the current user to check for overridden defaults
    $uid = xarUserGetVar('uid');
    // The API function is called.  The arguments to the function are passed in
    // as their own arguments array.
    // Security check 1 - the getall() function only returns items for which the
    // the user has at least OVERVIEW access.
    $items = xarModAPIFunc('courses',
        'user',
        'getall',
        array('startnum' => $startnum,
            'numitems' => xarModGetUserVar('courses',
                'itemsperpage',
                $uid)));
    if (!isset($items) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back

    // TODO: check for conflicts between transformation hook output and xarVarPrepForDisplay
    // Loop through each item and display it.
    foreach ($items as $item) {
        // Let any transformation hooks know that we want to transform some text
        // You'll need to specify the item id, and an array containing all the
        // pieces of text that you want to transform (e.g. for autolinks, wiki,
        // smilies, bbcode, ...).
        // Note : for your module, you might not want to call transformation
        // hooks in this overview list, but only in the display of the details
        // in the display() function.
        // list($item['name']) = xarModCallHooks('item',
        // 'transform',
        // $item['exid'],
        // array($item['name']));
        // Security check 2 - if the user has read access to the item, show a
        // link to display the details of the item
        if (xarSecurityCheck('ReadCourses', 0, 'Item', "$item[name]:All:$item[courseid]")) {
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
    // TODO: how to integrate cat ids in pager (automatically) when needed ???
    // Get the UID so we can see if there are any overridden defaults.
    $uid = xarUserGetVar('uid');
    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.

    // Note that this function includes another user API function.  The
    // function returns a simple count of the total number of items in the item
    // table so that the pager function can do its job properly
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('courses', 'user', 'countitems'),
        xarModURL('courses', 'user', 'view', array('startnum' => '%%')),
        xarModGetUserVar('courses', 'itemsperpage', $uid));
    // Specify some other variables for use in the function template
    // Same as above.  We are changing the name of the page to raise
    // better search engine compatibility.
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('View Courses')));
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
