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
 * Create a new Newsletter disclaimer
 *
 * @public
 * @author Richard Cave
 * @param 'title' the title of the disclaimer
 * @param 'disclaimer' the text of the disclaimer
 * @returns bool
 * @return true on success, false on failure
 */
function newsletter_admin_createdisclaimer()
{
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for creating new #(1) item', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get parameters from the input
    if (!xarVarFetch('title', 'str:1:', $title)) { 
        xarErrorFree();
        $msg = xarML('You must provide a title for the disclaimer.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (!xarVarFetch('disclaimer', 'str:1:', $disclaimer)) {
        xarErrorFree();
        $msg = xarML('You must provide the disclaimer.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Call create disclaimer function API
    $disclaimerId = xarModAPIFunc('newsletter',
                           'admin',
                           'createdisclaimer',
                            array('title' => $title,
                                  'disclaimer' => $disclaimer));

    // Check return value
    if (!isset($disclaimerId) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Success
    xarSessionSetVar('statusmsg', xarML('Disclaimer Created'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'viewdisclaimer'));

    // Return
    return true;
}

?>
