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
 * Update Newsletter configuration
 *
 * @public
 * @author Richard Cave
 * @param 'publishername' the name of the company or user that is the publisher
 * @param 'information' the text provided in the information block
 * @param 'itemsperpage' the number of items to display per page 
 * @param 'subscriptionsperpage' the number of subscriptions to display per page 
 * @returns bool
 * @return true on success, false on failure
 */
function newsletter_admin_updatenewsletter()
{
    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for updating #(1) configuration', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get parameters from input
    if (!xarVarFetch('publishername', 'str:1:', $publishername, '')) return;
    if (!xarVarFetch('information', 'str:1:', $information, '')) return;
    if (!xarVarFetch('templateHTML', 'str:1:', $templateHTML, '')) return;
    if (!xarVarFetch('templateText', 'str:1:', $templateText, '')) return;
    if (!xarVarFetch('categorysort', 'int:0:1:', $categorysort, 0)) return;
    if (!xarVarFetch('itemsperpage', 'int:0:', $itemsperpage, 10)) return;
    if (!xarVarFetch('subscriptionsperpage', 'int:0:', $subscriptionsperpage, 25)) return;
    if (!xarVarFetch('previewbrowser', 'int:0:1:', $previewbrowser, 0)) return;

    if (!empty($publishername)) {
        xarModSetVar('newsletter', 'publishername', $publishername);
    }

    if (!empty($information)) {
        xarModSetVar('newsletter', 'information', $information);
    }

    if (!empty($templateHTML)) {
        xarModSetVar('newsletter', 'templateHTML', $templateHTML);
    }

    if (!empty($templateText)) {
        xarModSetVar('newsletter', 'templateText', $templateText);
    }

    xarModSetVar('newsletter', 'categorysort', $categorysort);

    if (!empty($itemsperpage)) {
        xarModSetVar('newsletter', 'itemsperpage', $itemsperpage); 
    }

    if (!empty($subscriptionsperpage)) {
        xarModSetVar('newsletter', 'subscriptionsperpage', $subscriptionsperpage); 
    }

    xarModSetVar('newsletter', 'previewbrowser', $previewbrowser);

    // Redirect
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'modifynewsletter'));
}

?>
