<?php
/*
 * File: $Id: $
 *
 * SiteTools Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by Jo Dalle Nogare
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage SiteTools module
 * @author jojodee <http://xaraya.athomeandabout.com >
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
        xarResponseRedirect(xarModURL('sitetools', 'admin', 'optimize'));
    }
    // success
    return true;
}
?>
