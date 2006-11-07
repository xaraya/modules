<?php
/**
 * The main administration function
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage PHPlot Module
 * @link http://xaraya.com/index.php/release/818.html
 * @author PHPlot Module Development Team
 */
/**
 * The main administration function
 *
 * This function is the default function, and is called whenever the
 * module is initiated without defining arguments. As such it can
 * be used for a number of things, but most commonly it either just
 * shows the module menu and returns or calls whatever the module
 * designer feels should be the default function (often this is the
 * view() function)
 *
 * @author PHPlot Module Development Team
 * @access public
 * @return Specify your return type here
 */
function phplot_admin_main()
{
    /* Security check
     */
    if (!xarSecurityCheck('EditPHPlot')) return;
        /* Initialise the $data variable that will hold the data to be used in
         * the blocklayout template, and get the common menu configuration - it
         * helps if all of the module pages have a standard menu at the top to
         * support easy navigation
         */
        $data = xarModAPIFunc('phplot', 'admin', 'menu');

        /* If no main function as such, just return the view page,
         * or whatever function seems to be the most fitting for this module.
         */
        xarResponseRedirect(xarModURL('phplot', 'admin', 'modifyconfig'));

    /* success so return true */
    return true;
}
?>