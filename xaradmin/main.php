<?php
/**
 * File: $Id:
 * 
 * Example main administration function
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
 * the main administration function
 *
 */
function courses_admin_main()
{
    // Security check
    if (!xarSecurityCheck('EditCourses')) return;
    // The admin system looks for a var to be set to skip the introduction
    // page altogether.  This allows you to add sparse documentation about the
    // module, and allow the site admins to turn it on and off as they see fit.
    if (xarModGetVar('adminpanels', 'overview') == 0) {
        // If you want to go directly to some default function, instead of
        // having a separate main function, you can simply call it here, and
        // use the same template for admin-main.xard as for admin-view.xard
        // return xarModFunc('example','admin','view');
        // Initialise the $data variable that will hold the data to be used in
        // the blocklayout template, and get the common menu configuration - it
        // helps if all of the module pages have a standard menu at the top to
        // support easy navigation
        $data = xarModAPIFunc('courses', 'admin', 'menu');
        // Specify some other variables used in the blocklayout template
        $data['welcome'] = xarML('Welcome to the administration part of this Courses module...');
        // Return the template variables defined in this function
        return $data;
        // Note : instead of using the $data variable, you could also specify
        // the different template variables directly in your return statement :

        // return array('menutitle' => ...,
        // 'welcome' => ...,
        // ... => ...);
    } else {
        // If docs are turned off, then we just return the view page, or whatever
        // function seems to be the most fitting.
        xarResponseRedirect(xarModURL('courses', 'admin', 'view'));
    }
    // success
    return true;
}

?>
