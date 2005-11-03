<?php
/**
 * Courses main administration function
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
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
 *
 */
function courses_admin_main()
{
    // Security check
    if (!xarSecurityCheck('EditCourses')) return;
    // The admin system looks for a var to be set to skip the introduction
    // page altogether.
    if (xarModGetVar('adminpanels', 'overview') == 0) {
        $data = xarModAPIFunc('courses', 'admin', 'menu');
        // Specify some other variables used in the blocklayout template
        $data['welcome'] = xarML('Welcome to the administration part of this Courses module...');
        // Return the template variables defined in this function
        return $data;
    } else {
        // Return to main function
        xarResponseRedirect(xarModURL('courses', 'admin', 'view'));
    }
    // success
    return true;
}

?>
