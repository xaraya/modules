<?php
/**
 * Display the user menu hook
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses development team.
 */
/**
 * display the user menu hook
 *
 * This is a standard function to provide a link in the "Your Account Page"
 *
 * @param string $phase is the which part of the loop you are on
 * @param int startnum
 * @return array with data for template
 */
function courses_user_usermenu($args)
{
    // Security check
    if (!xarSecurityCheck('ViewCourses',0)) return '';
    extract($args);

    if (!xarVarFetch('phase',    'str:1:100', $phase,    'menu', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startnum', 'int:1:',    $startnum, '1', XARVAR_NOT_REQUIRED)) return;

    // Now we need to get the course information that the user is enrolled in so we can
    // pass the information to the template

    switch (strtolower($phase)) {
        case 'menu':
            // We need to define the icon that will go into the page.
            $icon = 'modules/courses/xarimages/preferences.gif';
            $data = xarTplModule('courses','user', 'usermenu_icon',
                array('icon' => $icon,
                      'usermenu_form_url' => xarModURL('courses', 'user', 'usermenu', array('phase' => 'form'))
                     ));
            break;

        case 'form':

            // Its good practice for the user menu to be personalized.  In order to do so, we
            // need to get some information about the user.
            $uname = xarUserGetVar('name');
            $uid = xarUserGetVar('uid');
            $data1 = '';
        //    $data['items'] = array();
        //    $data['pager'] = '';
            $items = xarModAPIFunc('courses',
                 'user',
                 'getall_enrolled',
                 array('startnum' => $startnum,
                    'numitems' => xarModGetUserVar('courses',
                    'itemsperpage', $uid)));
                   if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

            // Count teaching activities: TODO Where is the error?
            //$numteaching = xarModAPIFunc('courses', 'userapi', 'countteaching',
            //                          array('uid' => xarUserGetVar('uid')));

         // Transform display
         // TODO define SecCheck
             foreach ($items as $item) {
                if (xarSecurityCheck('ReadCourses', 0, 'Course', "All:All:All")) {
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
                $item['courseid'] = $item['courseid'];
                $item['planningid'] = $item['planningid'];
                $item['startdate'] = $item['startdate'];
                $item['statusname'] = xarModAPIFunc('courses', 'user', 'getstatus',
                                      array('status' => $item['studstatus']));

                // Add this item to the list of items to be displayed
                $data1['items'][] = $item;
            }
            // Get all teaching activities
            $titems = xarModAPIFunc('courses',
                 'user',
                 'getall_teaching',
                 array('startnum' => $startnum,
                       'numitems' => xarModGetUserVar('courses',
                       'itemsperpage', $uid)));
                   if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

             // Transform display
             // TODO define SecCheck
             foreach ($titems as $item) {
                if (xarSecurityCheck('ReadCourses', 0, 'Course', "All:All:All")) {
                    $item['tlink'] = xarModURL('courses',
                        'user',
                        'displayplanned',
                        array('planningid' => $item['planningid']));
                    // Security check 2 - else only display the item name (or whatever is
                    // appropriate for your module)
                } else {
                    $item['tlink'] = '';
                }
                // Clean up the item text before display
                $item['tname'] = xarVarPrepForDisplay($item['name']);
                $item['tcourseid'] = $item['courseid'];
                $item['tplanningid'] = $item['planningid'];
                $item['tstartdate'] = xarVarPrepForDisplay($item['startdate']);
                //$item['tstatusname'] = xarModAPIFunc('courses', 'user', 'getstatus',
                //                      array('status' => $item['studstatus']));

                // Add this item to the list of items to be displayed
                $data1['titems'][] = $item;
             }
            // We also need to set the SecAuthKey, in order to stop hackers from setting user
            // vars off site.
            $authid = xarSecGenAuthKey('courses');
            $value = xarModGetUserVar('courses', 'itemsperpage', $uid);
            $data = xarTplModule('courses', 'user', 'usermenu_form', array('authid' => $authid,
                    'uname' => $uname,
                    'uid' => $uid,
                    'value' => $value,
                    'data1' => $data1));

            break;

        case 'update':
            // First we need to get the data back from the template in order to process it.

            if (!xarVarFetch('uid', 'int:1:', $uid)) return;
            if (!xarVarFetch('itemsperpage', 'str:1:100', $itemsperpage, '20', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('name', 'str:1:100', $name, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('coursename', 'str:1:100', $coursename, '', XARVAR_NOT_REQUIRED)) return;
            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;

            xarModSetUserVar('courses', 'itemsperpage', $itemsperpage, $uid);
            // Redirect back to the account page.
            xarResponseRedirect(xarModURL('roles', 'user', 'account', array('moduleload' => 'courses')));

            break;
    }
    return $data;
}

?>