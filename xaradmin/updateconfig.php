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
 * Update Newsletter admin configuration
 *
 * @public
 * @author Richard Cave
 * @param 'bulkemail' send a single email to every newsletter subscriber 
 * @param 'shorturls' short URL support
 * @param 'activeusers' send email only to roles that are active
 * @returns bool
 * @return true on success, false on failure
 */
function newsletter_admin_updateconfig()
{
    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for updating #(1) configuration', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get parameters from input
    if (!xarVarFetch('bulkemail', 'checkbox', $bulkemail, true, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('activeusers', 'checkbox', $activeusers, true, XARVAR_NOT_REQUIRED)) return;

    // Update module variables
    xarModSetVar('newsletter', 'bulkemail', $bulkemail);
    xarModSetVar('newsletter', 'SupportShortURLs', $shorturls);
    xarModSetVar('newsletter', 'activeusers', $activeusers);

    // Redirect
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'modifyconfig'));
}

?>
