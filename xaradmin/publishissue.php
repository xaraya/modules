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
 * Publish an Newsletter issue
 *
 * @public
 * @param 'issueId' the id of the issue to publish
 * @author Richard Cave
 * @returns xarTplModule('mailissue')
 * @return redirect to 'mailissue'
 */
function newsletter_admin_publishissue()
{
    // Security check
    if(!xarSecurityCheck('AdminNewsletter')) return;

    // Get parameters from input
    if (!xarVarFetch('confirm', 'int:0:1', $confirm, 0)) return;
    if (!xarVarFetch('issueId', 'id', $issueId)) return;

    // Get the admin edit menu
    $data = array();
    $data['menu'] = xarModFunc('newsletter', 'admin', 'editmenu');

    // Get the issue for display
    $issue = xarModAPIFunc('newsletter',
                           'user',
                           'getissue',
                           array('id' => $issueId));

    // Check for exceptions
    if (!isset($issue) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back

    // Get the publication for display
    $publication = xarModAPIFunc('newsletter',
                                 'user',
                                 'getpublication',
                                 array('id' => $issue['pid']));

    // Check for exceptions
    if (!isset($publication) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back
    
    // Determine if issue was already published
    if ($issue['datePublished']['timestamp'] != 0) {
        // If this issue was already published, then display a warning
        // to the publisher and indicate that they must clear the
        // publication date of the issue to re-publish
        $data['publicationtitle'] = xarVarPrepForDisplay($publication['title']);
        $data['issuetitle'] = xarVarPrepForDisplay($issue['title']);
        $data['editurl'] = xarModURL('newsletter',
                                     'admin',
                                     'modifyissue',
                                     array('id' => $issue['id']));
        $data['published'] = true;
        $data['datePublished'] = $issue['datePublished'];
        return $data;
    }

    // Check for confirmation.
    if (!$confirm) {
        // Specify for which story you want confirmation
        $data['issueId'] = $issueId;
        $data['confirmbutton'] = xarML('Confirm');
        $data['publicationtitle'] = xarVarPrepForDisplay($publication['title']);
        $data['issuetitle'] = xarVarPrepForDisplay($issue['title']);
        $data['published'] = false;

        // Get subscription count
        $data['count'] = xarModAPIFunc('newsletter',
                                       'admin',
                                       'countsubscriptions',
                                       array('id' => $issue['pid']));

        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();

        // Return the template variables defined in this function
        return $data;
    }

    // If we get here it means that the user has confirmed the action
    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for publishing #(1) story #(2)',
                    'Newsletter', xarVarPrepForDisplay($id));
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Redirect
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'mailissue', array('issueId' => $issueId)));
}


?>
