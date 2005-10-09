<?php
/**
 * The main administration function
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Lists Module
 */

/**
 * The main administration function
 *
 * This function is the default function, and is called whenever the
 * module is initiated without defining arguments.  As such it can
 * be used for a number of things, but most commonly it either just
 * shows the module menu and returns or calls whatever the module
 * designer feels should be the default function (often this is the
 * view() function)
 *
 * @author Jason Judge
 * @author Lists Module Development Team
 * @TODO MichelV: <1> Security
 */
function lists_admin_main()
{ 

    //if (!xarSecurityCheck('EditExample')) return;
    /* The admin system looks for a var to be set to skip the introduction
     * page altogether.  This allows you to add sparse documentation about the
     * module, and allow the site admins to turn it on and off as they see fit.
     */
    if (xarModGetVar('adminpanels', 'overview') == 0) {
        // Return to main function
        $data = array();
         return $data;
        /* Initialise the $data variable that will hold the data to be used in
         * the blocklayout template, and get the common menu configuration
        $data = xarModAPIFunc('lists', 'admin', 'menu');
        return $data;
         */
    } else {
        /* If the Overview documentation is turned off, then we just return the view page,
         * or whatever function seems to be the most fitting.
         */
        xarResponseRedirect(xarModURL('lists', 'admin', 'main'));
    }
    /* success so return true */
    return true;
}
?>