<?php
/**
 * Courses main user function
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
 * the main user function
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments.  As such it can be used for a number
 * of things, but most commonly it either just shows the module menu and
 * returns or calls whatever the module designer feels should be the default
 * function (often this is the view() function)
 * @author MichelV <michelv@xarayahosting.nl>
 * @return array Empty array.
 */
function courses_user_main()
{
    // Security check
    if (!xarSecurityCheck('ViewCourses')) return;
    /* redirect to the main user page
    xarResponseRedirect(xarModURL('courses', 'user', 'view'));*/
    return array();
}
?>
