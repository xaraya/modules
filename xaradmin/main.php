<?php
/*
 * File: $Id: main.php,v 1.2 2003/09/22 14:52:20 jojodee Exp $
 *
 * SiteTools Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteTools module
 * @author jojodee <jojodee@xaraya.com>
*/

/**
 * the main administration function
 * This function is the default function, and is called whenever the
 * module is initiated without defining arguments.
 */
function sitetools_admin_main()
{ 
    // Security check
    if (!xarSecurityCheck('EditSiteTools')) return;
    // The admin system looks for a var to be set to skip the introduction
    // page altogether.  This allows you to add sparse documentation about the
    // module, and allow the site admins to turn it on and off as they see fit.
    if (xarModGetVar('adminpanels', 'overview') == 0) {

        $data = xarModAPIFunc('sitetools', 'admin', 'menu');
        // Specify some other variables used in the blocklayout template
        $data['welcome'] = ''; 
        // Return the template variables defined in this function
        return $data;

    } else {
        // If docs are turned off, then we just return the view page, or whatever
        // function seems to be the most fitting.
        xarResponseRedirect(xarModURL('sitetools', 'admin', 'modifyconfig'));
    }
    // success
    return true;
}
?>
