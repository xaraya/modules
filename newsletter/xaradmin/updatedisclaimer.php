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
 * Update an Newsletter disclaimer
 *
 * @public
 * @author Richard Cave
 * @param 'id' the id of the disclaimer to be updated
 * @param 'title' the title of the disclaimer to be updated
 * @param 'disclaimer' the text of the disclaimer to be updated
 * @returns bool
 * @return true on success, false on failure
 */
function newsletter_admin_updatedisclaimer()
{
    // Confirm authorization code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for updating #(1) item #(2) in function #(3)',
                    'Newsletter', xarVarPrepForDisplay($id), 'newsletter_admin_updatedisclaimer');
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get parameters from the input
    if (!xarVarFetch('id', 'id', $id)) return;

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

    // Call API function
    if(!xarModAPIFunc('newsletter',
                      'admin',
                      'updatedisclaimer',
                      array('id' => $id,
                            'title' => $title,
                            'disclaimer' => $disclaimer))) {
        return; // throw back
    }

    xarSessionSetVar('statusmsg', xarML('Newsletter Disclaimer Update'));

    // Redirect
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'viewdisclaimer'));

    // Return
    return true;
}

?>
