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
 * @subpackage example
 * @author Example module development team 
 */
/**
 * utility function pass individual menu items to the main menu
 * 
 * @author the Example module development team 
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function courses_userapi_getmenulinks()
{
    // First we need to do a security check to ensure that we only return menu items
    // that we are suppose to see.
    if (xarSecurityCheck('ViewCourses', 0)) {
        // The main menu will look for this array and return it for a tree view of the module
        // We are just looking for three items in the array, the url, which we need to use the
        // xarModURL function, the title of the link, which will display a tool tip for the
        // module url, in order to keep the label short, and finally the exact label for the
        // function that we are displaying.
        $menulinks[] = Array('url' => xarModURL('courses',
                'user',
                'view'),
            // In order to display the tool tips and label in any language,
            // we must encapsulate the calls in the xarML in the API.
            'title' => xarML('Displays all courses for view'),
            'label' => xarML('Display'));
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
