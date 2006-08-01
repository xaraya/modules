<?php
/**
 * View a list of courses
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * View a list of courses
 *
 * This is a standard function to provide an overview of all of the courses
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param int startnum Starting number when there are many items to show
 * @param string catid The category id or a string with multiple category ids glued together.
 * @param string sortby Sortby parameter (standard on number)
 * @param string sortorder The Sortorder. Defaults to ASC
 * @return array Information for the template
 */
function courses_user_view()
{
    if (!xarVarFetch('startnum', 'int:1:',        $startnum,  1,        XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('catid',    'str::',         $catid,     NULL,     XARVAR_DONT_SET)) return;
    if (!xarVarFetch('sortby',   'str:1:',        $sortby,    'number', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortorder','enum:DESC:ASC', $sortorder, 'ASC',    XARVAR_NOT_REQUIRED)) return;
    // Security check
    if (!xarSecurityCheck('ViewCourses')) return;

    $data = array();
    // Prepare the array variable that will hold all items for display
    $data['items'] = array();

    // Lets get the UID of the current user to check for overridden defaults
    $uid = xarUserGetVar('uid');
    // The API function is called.
    $items = xarModAPIFunc('courses',
        'user',
        'getall',
        array('startnum' => $startnum,
              'numitems' => xarModGetUserVar('courses','itemsperpage',$uid),
              'sortby' => $sortby,
              'catid' => $catid,
              'sortorder' => $sortorder));
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Loop through each item and display it.
    foreach ($items as $item) {
        $courseid = $item['courseid'];
        // Security. User should be able to see the link via a read mask
        if (xarSecurityCheck('ReadCourses', 0, 'Course', "$courseid:All:All")) {
            $item['link'] = xarModURL('courses',
                'user',
                'display',
                array('courseid' => $item['courseid']));
        } else {
            $item['link'] = '';
        }
        // Clean up the item text before display
        $item['name'] = xarVarPrepForDisplay($item['name']);
        $item['shortdesc'] = xarVarPrepHTMLDisplay($item['shortdesc']);

        // Add the next date this course is planned
        $plandates = xarModAPIFunc('courses', 'user', 'getplandates',
                                   array('courseid' => $courseid, 'startafter' => time())
                                   );
        if (!empty($plandates)) {
            if (empty($plandates[0]['expected'])) {
                $item['upcomingdate'] = $plandates[0]['startdate'];
                $item['expected'] ='';
            } elseif (!empty($plandates[0]['expected'])) {
                $item['expected'] = $plandates[0]['expected'];
            }
        } else {
            $item['upcomingdate'] = '';
        }
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
                                             'catid' => $catid,
                                             'sortorder' =>$sortorder));
    } else {
        $data['snamelink'] = '';
    }
    if ($sortby != 'shortdesc' ) {
        $data['sdesclink'] = xarModURL('courses',
                                       'user',
                                       'view',
                                       array('startnum' => 1,
                                             'sortby' => 'shortdesc',
                                             'catid' => $catid,
                                             'sortorder' =>$sortorder));
    } else {
        $data['sdesclink'] = '';
    }
    if ($sortby != 'number' ) {
        $data['snumberlink'] = xarModURL('courses',
                                       'user',
                                       'view',
                                       array('startnum' => 1,
                                             'sortby' => 'number',
                                             'catid' => $catid,
                                             'sortorder' =>$sortorder));
    } else {
        $data['snumberlink'] = '';
    }

    // Pager
    $data['pager'] = '';
    $data['pager'] = xarTplGetPager($startnum,
                    xarModAPIFunc('courses', 'user', 'countitems', array('catid' => $catid)),
                    xarModURL('courses', 'user', 'view', array('startnum' => '%%','sortby' => $sortby, 'catid' => $catid,'sortorder' => $sortorder)),
                    xarModGetUserVar('courses', 'itemsperpage', $uid));
    $data['ShowShortDescchecked'] = xarModGetVar('courses', 'ShowShortDesc') ? 'checked="checked"' : '';
    // Changing the name of the page
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('View Courses')));
    return $data;
}

?>
