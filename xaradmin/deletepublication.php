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
 * Delete an Newsletter publication
 *
 * @public
 * @author Richard Cave
 * @param 'id' the id of the publication to be deleted
 * @param 'confirm' confirm that this publication can be deleted
 * @param 'issues' remove or reassign issues/stories for this publication
 * @param 'newpid' if reassign, the new id of the publication
 * @returns array
 * @return $data
 */
function newsletter_admin_deletepublication($args)
{
    // Extract args
    extract ($args);

    // Security check
    if(!xarSecurityCheck('DeleteNewsletter')) return;

    // Get parameters from input
    if (!xarVarFetch('id', 'id', $id, 0)) return;
    if (!xarVarFetch('confirm', 'int:0:1', $confirm, 0)) return;
    if (!xarVarFetch('issues', 'str:1:', $issues, 'remove')) return;
    if (!xarVarFetch('newpid', 'int:0:', $newpid, 0)) return;

    // The user API function is called
    $publication = xarModAPIFunc('newsletter',
                                 'user',
                                 'getpublication',
                                 array('id' => $id));

    // Check for exceptions
    if (!isset($publication) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Check for confirmation.
    if (!$confirm) {

        // Get the admin menu
        $data = xarModAPIFunc('newsletter', 'admin', 'menu');

        // Get a list of all publications
        $data['publications'] = xarModAPIFunc('newsletter',
                                              'user',
                                              'get',
                                               array('phase' => 'publication',
                                                     'sortby' => 'title'));
        
        // Check for exceptions
        if (!isset($data['publications']) && xarCurrentErrorType() != XAR_NO_EXCEPTION)  {
            return; // throw back
        }
    
        // Specify for which publication you want confirmation
        $data['id'] = $id;
        $data['confirmbutton'] = xarML('Confirm');

        // Data to display in the template
        $data['namevalue'] = xarVarPrepForDisplay($publication['title']);

        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();

        // Return the template variables defined in this function
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for deleting #(1) publication #(2)',
                    'Newsletter', xarVarPrepForDisplay($id));
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // The API function is called
    if (!xarModAPIFunc('newsletter',
                       'admin',
                       'deletepublication',
                       array('id' => $id,
                             'issues' => $issues,
                             'newpid' => $newpid))) {
        return; // throw back
    }

    xarSessionSetVar('statusmsg', xarML('Item Deleted'));

    // Redirect
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'viewpublication'));
}

?>
