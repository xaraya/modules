<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * Modify newsletter configuration
 *
 * @public
 * @author Richard Cave
 * @returns array
 * @return $data
 */
function newsletter_admin_modifynewsletter()
{
    // Security check
    if(!xarSecurityCheck('AdminNewsletter')) return;

    // Get the admin edit menu
    $data['menu'] = xarModFunc('newsletter', 'admin', 'configmenu');

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // Specify some labels and values for display
    $data['modifybutton'] = xarVarPrepForDisplay(xarML('Update Config'));

    // Get publisher name and subscription information
    $data['publishername']    = xarModGetVar('newsletter', 'publishername');
    $data['information']      = xarModGetVar('newsletter', 'information');
    $data['templateHTML']     = xarModGetVar('newsletter', 'templateHTML');
    $data['templateText']     = xarModGetVar('newsletter', 'templateText');
    $data['itemsperpage']     = xarModGetVar('newsletter', 'itemsperpage');
    if (!$data['itemsperpage']) {
        $data['itemsperpage'] = 10;
    }
    $data['subscriptionsperpage'] = xarModGetVar('newsletter', 'subscriptionsperpage');
    if (!$data['subscriptionsperpage']) {
        $data['subscriptionsperpage'] = 25;
    }
    $data['categorysort']     = xarModGetVar('newsletter', 'categorysort');
    $data['previewbrowser']   = xarModGetVar('newsletter', 'previewbrowser');

    // Return the template variables defined in this function
    return $data;
}

?>
