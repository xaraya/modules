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
 * Delete an Newsletter story
 *
 * @public
 * @author Richard Cave
 * @param 'id' the id of the story to be deleted
 * @param 'confirm' confirm that this story can be deleted
 * @returns array
 * @return $data
 */
function newsletter_admin_deletestory($args)
{
    // Extract args
    extract ($args);

    // Security check
    if(!xarSecurityCheck('DeleteNewsletter')) return;

    // Get parameters from input
    if (!xarVarFetch('id', 'id', $id, 0)) return;
    if (!xarVarFetch('confirm', 'int:0:1', $confirm, 0)) return;

    // The user API function is called
    $story = xarModAPIFunc('newsletter',
                           'user',
                           'getstory',
                           array('id' => $id));

    // Check for exceptions
    if (!isset($story) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Check for confirmation.
    if (!$confirm) {

        // Get the admin menu
        $data = xarModAPIFunc('newsletter', 'admin', 'menu');

        // Specify for which story you want confirmation
        $data['id'] = $id;
        $data['confirmbutton'] = xarML('Confirm');

        $data['namevalue'] = xarVarPrepForDisplay($story['title']);

        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();

        // Return the template variables defined in this function
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for deleting #(1) story #(2)',
                    'Newsletter', xarVarPrepForDisplay($id));
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Find out which issue this is in
    $topic = xarModAPIFunc('newsletter',
                           'user',
                           'gettopicbystory',
                           array('storyId' => $id));
    
    // Check for exceptions
    if (!isset($topic) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // The API function is called
    if (!xarModAPIFunc('newsletter',
                       'admin',
                       'deletestory',
                       array('id' => $id))) {
        return; // throw back
    }

    xarSessionSetVar('statusmsg', xarML('Item Deleted'));

    // Redirect
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'viewstory', array('issueId' => $topic['issueId'])));
}

?>
