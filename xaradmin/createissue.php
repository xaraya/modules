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
 * Create a new Newsletter issue
 *
 * @public
 * @author Richard Cave
 * @param 'publicationId' publication id of the issue
 * @param 'ownerId' owner id of the issue
 * @param 'title' title of the issue
 * @param 'editorNote' editor note for the issue
 * @param 'stories' array of stories in the issue
 * @param 'external' flag if issue is internal/external (1 = true, 0 = false)
 * @param 'fromname' issue email from name (overrides publication from name)
 * @param 'fromemail' issue email from address (overrides publication from email)
 * @returns bool
 * @return true on success, false on failure
 */
function newsletter_admin_createissue()
{
    // Confirm authorization key 
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for creating new #(1) item', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get parameters from the input
    if (!xarVarFetch('publicationId', 'id', $publicationId)) { 
        xarErrorFree();
        $msg = xarML('You must select a publication.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (!xarVarFetch('ownerId', 'id', $ownerId)) {
        xarErrorFree();
        $msg = xarML('You must select an owner name.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (!xarVarFetch('title', 'str:1:', $title, '')) return;
    if (!xarVarFetch('editorNote', 'str:1:', $editorNote, '')) return;
    if (!xarVarFetch('external', 'int:0:1:', $external, 0)) return;
    if (!xarVarFetch('fromname', 'str:1:', $fromname, '')) return;
    if (!xarVarFetch('fromemail', 'str:1:', $fromemail, '')) return;
    
    // If the fromname or fromemail fields are empty, then retrieve the information
    // from the publication
    if (empty($fromname) || empty($fromemail)) {
        // Get publication information
        $pubinfo = xarModAPIFunc('newsletter',
                                 'user',
                                 'getpublication',
                                 array('id' => $publicationId));

        // Check for exceptions
        if (!isset($pubinfo) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
            return; // throw back

        // Set name and/or email
        if (empty($fromname)) {
            $fromname = $pubinfo['fromname'];
        }
        if (empty($fromemail)) {
            $fromemail = $pubinfo['fromemail'];
        }
    }

    // Add new disclaimer if field isn't empty
    // Call create owner function API
    $issueId = xarModAPIFunc('newsletter',
                             'admin',
                             'createissue',
                             array('publicationId' => $publicationId,
                                   'ownerId' => $ownerId,
                                   'title' => $title,
                                   'editorNote' => $editorNote,
                                   'external' => $external,
                                   'tstmpDatePublished' => 0,  // not published, so set to 0
                                   'fromname' => $fromname,
                                   'fromemail' => $fromemail));

    // Check return value
    if (!isset($issueId) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Success
    xarSessionSetVar('statusmsg', xarML('Issue Created'));
    
    // Create template array
    $templateVarArray = array(
        'publicationId' => $publicationId,
        'ownerId' => $ownerId,
        'issueId' => $issueId);

    // Redirect to create a story for the issue
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'newstory', $templateVarArray));

    // Return
    return true;
}

?>
