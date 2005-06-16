<?php
/**
 * File: $Id:
 * 
 * Utility function to pass menu items to the main menu
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
 * utility function pass individual menu items to the main menu
 * 
 * @author the Courses module development team 
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function courses_adminapi_getmenulinks()
{
    // First we need to do a security check to ensure that we only return menu items
    // that we are suppose to see.  It will be important to add for each menu item that
    // you want to filter.  No sense in someone seeing a menu link that they have no access
    // to edit.  Notice that we are checking to see that the user has permissions, and
    // not that he/she doesn't.
    // Security Check
    if (xarSecurityCheck('EditCourses', 0)) {
        $menulinks[] = Array('url' => xarModURL('courses',
                'admin',
                'viewcourses'),
            // In order to display the tool tips and label in any language,
            // we must encapsulate the calls in the xarML in the API.
            'title' => xarML('View all courses that have been added.'),
            'label' => xarML('View Courses'));
    }
    // Security Check
    if (xarSecurityCheck('AddCourses', 0)) {
        $menulinks[] = Array('url' => xarModURL('courses',
                'admin',
                'newcourse'),
            'title' => xarML('Adds a new course to system.'),
            'label' => xarML('Add Course'));
    }
    // Security Check
	/*
    if (xarSecurityCheck('AddPlanning', 0)) {
        $menulinks[] = Array('url' => xarModURL('courses',
                'admin',
                'plancourse'),
            'title' => xarML('Plan a course.'),
            'label' => xarML('Plan Course'));
    }
	*/

    // Security Check
    if (xarSecurityCheck('AdminCourses', 0)) {
        $menulinks[] = Array('url' => xarModURL('courses',
                'admin',
                'view'),
            // In order to display the tool tips and label in any language,
            // we must encapsulate the calls in the xarML in the API.
            'title' => xarML('Modify the courses parameters'),
            'label' => xarML('Course parameters'));
    }



    // Security Check
    if (xarSecurityCheck('AdminCourses', 0)) {
        $menulinks[] = Array('url' => xarModURL('courses',
                'admin',
                'modifyconfig'),
            // In order to display the tool tips and label in any language,
            // we must encapsulate the calls in the xarML in the API.
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'));
    }
    // If we return nothing, then we need to tell PHP this, in order to avoid an ugly
    // E_ALL error.
    if (empty($menulinks)) {
        $menulinks = '';
    }
    // The final thing that we need to do in this function is return the values back
    // to the main menu for display.
    return $menulinks;
}

?>
