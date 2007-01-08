<?php
/**
 * Utility function to pass individual menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
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
 * @author MichelV <michelv@xarayahosting.nl>
 * @return array containing the menulinks for the main menu items.
 */
function courses_userapi_getmenulinks()
{
    // First we need to do a security check to ensure that we only return menu items
    // that we are suppose to see.
    if (xarSecurityCheck('ViewCourses', 0)) {
        $menulinks[] = Array('url'   => xarModURL('courses', 'user', 'view'),
                             'title' => xarML('Displays all courses'),
                             'label' => xarML('Courses'));
    }

    if (xarSecurityCheck('ViewCourses', 0)) {
        $menulinks[] = Array('url' => xarModURL('search','user'),
                             'title' => xarML('Search for a course'),
                             'label' => xarML('Search'));
    }
    if (xarSecurityCheck('ReadCourses', 0)) {
        $menulinks[] = Array('url' => xarModURL('roles','user', 'account', array('moduleload' => 'courses')),
                             'title' => xarML('Displays courses that I am enrolled in or act as a teacher'),
                             'label' => xarML('My courses'));
    }

    // If we return nothing, then we need to tell PHP this, in order to avoid an ugly
    // E_ALL error.
    if (empty($menulinks)) {
        $menulinks = '';
    }
    return $menulinks;
}

?>
