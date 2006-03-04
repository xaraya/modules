<?php
/**
 * Courses main administration function
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
 * the main administration function
 * Doesn't do much at the moment
 * @return bool true and a redirect to the viewcourses function
 */
function courses_admin_main()
{
    // Security check
    if (!xarSecurityCheck('EditCourses')) return;
    // Return to main function
    xarResponseRedirect(xarModURL('courses', 'admin', 'viewcourses'));
    // success
    return true;
}

?>
