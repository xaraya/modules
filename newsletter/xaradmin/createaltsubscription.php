<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://xavier.schwabfoundation.org
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * Create a new Newsletter subscription
 *
 * @public
 * @author Richard Cave
 * @param 'name' the name of the new subscription
 * @param 'email' the email address of the new subscription
 * @param 'pids' the publication ids
 * @param 'htmlmail' send mail html or text (0 = text, 1 = html)
 * @returns bool
 * @return true on success, false on failure
 */
function newsletter_admin_createaltsubscription()
{
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for creating new #(1) item', 'Newsletter');
        xarExceptionSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get parameters from the input
    if (!xarVarFetch('name', 'str:1:', $name, '')) return;

    if (!xarVarFetch('email', 'str:1:', $email)) {
        xarExceptionFree();
        $msg = xarML('You must provide an email address.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (!xarVarFetch('pids', 'array:1:', $pids)) {
        xarExceptionFree();
        $msg = xarML('You must select at least one publication.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (!xarVarFetch('htmlmail', 'int:0:1:', $htmlmail, 0)) return;

    // Create the syntactical validation regular expression
    $regexp = "^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$";

    // Presume that the email is invalid
    $valid = 0;

    // Validate the syntax
    if (eregi($regexp, $email))
    {
        list($username,$domaintld) = split("@",$email);
        // Validate the domain
        if (getmxrr($domaintld,$mxrecords))
            $valid = 1;
    } else {
        $valid = 0;
    }

    if ($valid) {
        foreach ($pids as $pid) {
            // Call create subscription function API
            $item =xarModAPIFunc('newsletter',
                                 'admin',
                                 'createaltsubscription',
                                  array('name' => $name,
                                        'email' => $email,
                                        'pid' => $pid,
                                        'htmlmail' => $htmlmail));

            if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
                return; // throw back
            }

            xarSessionSetVar('statusmsg', xarML('Subscription Created'));
        }

        // Redirect the user
        xarResponseRedirect(xarModURL('newsletter', 'admin', 'newaltsubscription'));

    } else {
        // Get the admin subscription menu
        $data['menu'] = xarModFunc('newsletter', 'admin', 'subscriptionmenu');

        $data['subscribebutton'] = xarVarPrepForDisplay(xarML('Add Subscription'));

        // Set parameters for template
        $data['invalid'] = true;
        $data['name'] = $name;
        $data['email'] = $email;
    
        // Get all the publications
        $startnum = 1;
        $publications = xarModAPIFunc('newsletter',
                                      'user',
                                      'get',
                                      array('startnum' => $startnum,
                                            'numitems' => xarModGetVar('newsletter',
                                                                      'itemsperpage'),
                                            'phase' => 'publication',
                                            'sortby' => 'title'));

        // Check for exceptions
        if (!isset($publications) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
            return; // throw back

        // Add the array of items to the template variables
        $data['publications'] = $publications;

        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();

        // Return the template variables
        return $data;
    }
}

?>
