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
 * @returns bool
 * @return true on success, false on failure
 */
function newsletter_admin_createissue()
{
    // Confirm authorization key 
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for creating new #(1) item', 'Newsletter');
        xarExceptionSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get parameters from the input
    if (!xarVarFetch('publicationId', 'id', $publicationId)) { 
        xarExceptionFree();
        $msg = xarML('You must select a publication.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (!xarVarFetch('ownerId', 'id', $ownerId)) {
        xarExceptionFree();
        $msg = xarML('You must select an owner name.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (!xarVarFetch('title', 'str:1:', $title, '')) return;
    if (!xarVarFetch('editorNote', 'str:1:', $editorNote, '')) return;
    if (!xarVarFetch('external', 'int:0:1:', $external, 0)) return;
    
    // Call create owner function API
    $issueId = xarModAPIFunc('newsletter',
                             'admin',
                             'createissue',
                             array('publicationId' => $publicationId,
                                   'ownerId' => $ownerId,
                                   'title' => $title,
                                   'editorNote' => $editorNote,
                                   'external' => $external,
                                   'tstmpDatePublished' => 0));  // not published, so set to 0

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
