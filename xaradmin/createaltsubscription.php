<?php
/**
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
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
 * @param 'validate' validate email address (0 = false, 1 = true)
 * @return bool true on success, false on failure
 */
function newsletter_admin_createaltsubscription()
{
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for creating new #(1) item', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get parameters from the input
    if (!xarVarFetch('name', 'str:1:', $name, '')) return;

    if (!xarVarFetch('email', 'str:1:', $email)) {
        xarErrorFree();
        $msg = xarML('You must provide an email address.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (!xarVarFetch('pids', 'array:1:', $pids)) {
        xarErrorFree();
        $msg = xarML('You must select at least one publication.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (!xarVarFetch('htmlmail', 'int:0:1:', $htmlmail, 0)) return;
    if (!xarVarFetch('validate', 'int:0:1:', $validate, 0)) return;

    // Trim the name and email - make sure their are no blanks before or after
    $name = trim($name);
    $email = trim($email);

    // Check if validating email
    if ($validate) {
        $valid = newsletter_admin__checkemail($email);
    } else {
        // No validation - assume this email address is correct
        $valid = true;
    }
    $role = xarModApiFunc('roles','user','get',array('email'=>$email));
    if (empty($role['uid'])) {
        $subscriptiontype = 'alt';
    } else {
        $subscriptiontype = 'role id';
    }
    if($valid) {
        foreach ($pids as $pid) {
            // Call create subscription function API
            if (empty($role['uid'])) {
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
            } else {
                // Call create subscription function API
                $subscriptionId = xarModAPIFunc('newsletter',
                                     'admin',
                                     'createsubscription',
                                      array('uid' => $role['uid'],
                                            'pid' => $pid,
                                            'htmlmail' => $htmlmail));

                if ((!$subscriptionId) && (xarCurrentErrorType() != XAR_NO_EXCEPTION)) {
                    return; // throw back
                }

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

/**
 * Check if an email address is valid
 *
 * @private
 * @author Richard Cave
 * @param string 'email' the email address to test
 * @return bool true on success, false on failure
 */
function newsletter_admin__checkemail($email)
{
    // Check if the $email email address in valid format
    // Regular expression from margc's super email validation script
    if(eregi("^[a-z0-9\._-]+".
             "@{1}".
             "([a-z0-9]{1}[a-z0-9-]*[a-z0-9]{1}\.{1})+".
             "([a-z]+\.){0,1}".
             "([a-z]+){1}$", $email)) {

        // Validate the domain
        //list($username,$domaintld) = split("@",$email);
        //if (getmxrr($domaintld,$mxrecords)) {
        return true;
        //}
    }

    return false;
}

?>
