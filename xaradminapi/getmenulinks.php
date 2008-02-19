<?php
/**
 * Utility function to pass menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2005-2008 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Courses module development team
 * @return array containing the menulinks for the main menu items.
 */
function courses_adminapi_getmenulinks()
{
    if (xarSecurityCheck('EditCourses', 0)) {
        $menulinks[] = Array('url' => xarModURL('courses',
                'admin',
                'viewcourses'),
            'title' => xarML('View all courses that have been added.'),
            'label' => xarML('View Courses'));
    }
    if (xarSecurityCheck('ReadCourses', 0)) {
        $menulinks[] = Array('url' => xarModURL('courses',
                'admin',
                'viewallplanned'),
            'title' => xarML('View all planned courses.'),
            'label' => xarML('Planning'));
    }
    if (xarSecurityCheck('AdminCourses', 0)) {
        $menulinks[] = Array('url' => xarModURL('courses',
                'admin',
                'viewtypes'),
            'title' => xarML('View the courses types and create new courses from them'),
            'label' => xarML('Course types'));
        $menulinks[] = Array('url' => xarModURL('courses',
                'admin',
                'view'),
            'title' => xarML('Modify the courses parameters'),
            'label' => xarML('Course parameters'));
        $menulinks[] = Array('url' => xarModURL('courses',
                'admin',
                'modifyconfig'),
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'));
    }
    if (xarSecurityCheck('EditCourses', 0)) {
        $menulinks[] = Array('url' => xarModURL('courses',
                'admin',
                'overview'),
            'title' => xarML('View the module overview page.'),
            'label' => xarML('Overview'));
    }
    // If we return nothing, then we need to tell PHP this, in order to avoid an ugly
    // E_ALL error.
    if (empty($menulinks)) {
        $menulinks = '';
    }
    return $menulinks;
}

?>
