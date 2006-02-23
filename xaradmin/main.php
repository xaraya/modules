<?php
/**
 * The main administration function
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
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
 * @author ITSP Module Development Team
 * @return array Template information
 */
function itsp_admin_main()
{
    if (!xarSecurityCheck('EditITSP')) return;
    /* The admin system looks for a var to be set to skip the introduction
     * page altogether.  This allows you to add sparse documentation about the
     * module, and allow the site admins to turn it on and off as they see fit.
     */
    if (xarModGetVar('adminpanels', 'overview') == 0) {
        $data = xarModAPIFunc('itsp', 'admin', 'menu');
        return $data;
    } else {
        /* If the Overview documentation is turned off, then we just return the view page,
         * or whatever function seems to be the most fitting.
         */
        xarResponseRedirect(xarModURL('itsp', 'admin', 'view'));
    }
    /* success so return true */
    return true;
}
?>