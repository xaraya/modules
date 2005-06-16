<?php
/**
 * File: $Id:
 * 
 * Utility function to pass individual menu items to the main menu
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
 * @author Michel V. 
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function courses_userapi_getmenulinks()
{
    // First we need to do a security check to ensure that we only return menu items
    // that we are suppose to see.
    if (xarSecurityCheck('ViewCourses', 0)) {
        $menulinks[] = Array('url' => xarModURL('courses',
                'user',
                'view'),
            // In order to display the tool tips and label in any language,
            // we must encapsulate the calls in the xarML in the API.
            'title' => xarML('Displays all courses for view'),
            'label' => xarML('Display'));
    }
    if (xarSecurityCheck('Editplanning', 0)) {
        $menulinks[] = Array('url' => xarModURL('courses',
                'admin',
                'updateplanning'),
            // In order to display the tool tips and label in any language,
            // we must encapsulate the calls in the xarML in the API.
            'title' => xarML('Displays all planned courses for editing'),
            'label' => xarML('Edit planned'));
    }
    if (xarSecurityCheck('Viewplanning', 0)) {
        $menulinks[] = Array('url' => xarModURL('roles',
                'user',
                'account', array('moduleload' => 'courses')),
            // In order to display the tool tips and label in any language,
            // we must encapsulate the calls in the xarML in the API.
            'title' => xarML('Displays courses that I am enrolled in or act as a teacher'),
            'label' => xarML('My courses'));
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
