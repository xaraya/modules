<?php
/**
 * File: $Id:
 * 
 * Display the user menu hook
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author XarayaGeek
 */
/**
 * display the user menu hook
 * This is a standard function to provide a link in the "Your Account Page"
 * 
 * @param  $phase is the which part of the loop you are on
 */
function courses_user_usermenu()
{
    // Security check  - if the user has read access to the menu, show a
    // link to display the details of the item
    if (!xarSecurityCheck('ViewCourses')) return;
    // First, lets find out where we are in our logic.  If the phase
    // variable is set, we will load the correct page in the loop.
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'menu', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('uid', 'isset:', $uid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('coursename', 'str:1:', $coursename, '1', XARVAR_NOT_REQUIRED)) return;

// Now we need to get the course information that the user is enrolled in so we can
	// pass the information to the template
    $uid = xarUserGetVar('uid');
	$data1 = '';
//	$data['items'] = array();
//	$data['pager'] = '';
	$items = xarModAPIFunc('courses',
         'user',
         'getall_enrolled',
         array('startnum' => $startnum,
            'numitems' => xarModGetUserVar('courses',
            'itemsperpage', $uid)));
           if (!isset($items) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back
//var_dump($items);
//$courseid = $items['0'];
//$names = xarModAPIFunc('courses',
//         'user',
//         'getall_names',
//         array('startnum' => $startnum,
//            'numitems' => xarModGetUserVar('courses',
//            'itemsperpage', $courseid)));
//           if (!isset($names) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back
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
        $item['courseid'] = xarVarPrepForDisplay($item['courseid']);
	    // Add this item to the list of items to be displayed
        $data1['items'][] = $item;
    }

    switch (strtolower($phase)) {
        case 'menu':
            // We need to define the icon that will go into the page.
            $icon = 'modules/courses/xarimages/preferences.gif';
            // Now lets send the data to the template which name we choose here.
            $data = xarTplModule('courses', 'user', 'usermenu_icon', array('iconbasic' => $icon));

            break;

        case 'form':

			// Its good practice for the user menu to be personalized.  In order to do so, we
            // need to get some information about the user.
            $uname = xarUserGetVar('name');
            $uid = xarUserGetVar('uid');

            // We also need to set the SecAuthKey, in order to stop hackers from setting user
            // vars off site.
            $authid = xarSecGenAuthKey();
            // Lets get the value that we want to override from the preferences. Notice that we are
            // xarModUserGetMod and not xarModGetVar so we can grab the overridden value.  You do
            // not have to use a user variable for every module var that the module posses, just
            // the variables that you want to override.
            $value = xarModGetUserVar('courses', 'itemsperpage', $uid);
            // if (empty($value)){
            // $value = xarModGetVar('example', 'itemsperpage');
            // }
            // Now lets send the data to the template which name we choose here.
            $data = xarTplModule('courses', 'user', 'usermenu_form', array('authid' => $authid,
                    'uname' => $uname,
                    'uid' => $uid,
                    'value' => $value,
					'data1' => $data1));

            break;

        case 'update':
            // First we need to get the data back from the template in order to process it.
            // The example module is not setting any user vars at this time, but an example
            // might be the number of items to be displayed per page.
            if (!xarVarFetch('uid', 'int:1:', $uid)) return;
            if (!xarVarFetch('itemsperpage', 'str:1:100', $itemsperpage, '20', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('name', 'str:1:100', $name, '', XARVAR_NOT_REQUIRED)) return;
		    if (!xarVarFetch('coursename', 'str:1:100', $coursename, '', XARVAR_NOT_REQUIRED)) return;
            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;

            xarModSetUserVar('courses', 'itemsperpage', $itemsperpage, $uid);
            // Redirect back to the account page.  We could also redirect back to our form page as
            // well by adding the phase variable to the array.
            xarResponseRedirect(xarModURL('roles', 'user', 'account'));

            break;
    }
    // Finally, we need to send our variables to block layout for processing.  Since we are
    // using the data var for processing above, we need to do the same with the return.
    return $data;
}

?>
