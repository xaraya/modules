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
 * Update an Newsletter issue 
 *
 * @public
 * @author Richard Cave
 * @param 'id' the id of the item to be modified
 * @param 'publication' publication id of the issue
 * @param 'title' title of the issue
 * @param 'ownerId' owner if of the issue
 * @param 'external' flag if issue is internal/external (1 = true, 0 = false)
 * @param 'editorNote' editor note for the issue
 * @param 'datePublishedMon' the month the issue was published
 * @param 'datePublishedDay' the day the issue was published
 * @param 'datePublishedYear' the year the issue was published
 * @returns bool
 * @return true on success, false on failure
 */
function newsletter_admin_updateissue()
{
    // Confirm authorization code
    if (!xarSecConfirmAuthKey()) {
       $msg = xarML('Invalid authorization key for updating #(1) item #(2) in function #(3)',
                    'Newsletter', xarVarPrepForDisplay($id), 'newsletter_admin_updateissue');
        xarExceptionSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Set datePublished array
    $datePublished = array();

    // Get parameters from the input
    if (!xarVarFetch('id', 'id', $id)) return;

    if (!xarVarFetch('publication', 'id', $publication)) {
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
    if (!xarVarFetch('datePublishedMon', 'int:0:', $datePublishedMon, 0)) return;
    if (!xarVarFetch('datePublishedDay', 'int:0:', $datePublishedDay, 0)) return;
    if (!xarVarFetch('datePublishedYear', 'int:0:', $datePublishedYear, 0)) return;

    // Check and format datePublished - dates are stored as UNIX timestamp
    if ($datePublishedMon == 0 || $datePublishedDay == 0 || $datePublishedYear == 0) { 
            $tstmpDatePublished =  0;
    } else {
        $tstmpDatePublished = mktime(0,0,0,$datePublishedMon,$datePublishedDay,$datePublishedYear);
    }
    
    // Get current issue attributes
    $oldissue = xarModAPIFunc('newsletter',
                              'user',
                              'getissue',
                              array('id' => $id));

    // Check for exceptions
    if (!isset($oldissue) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Update the issue
    if(!xarModAPIFunc('newsletter',
                      'admin',
                      'updateissue',
                      array('id' => $id,
                            'publicationId' => $publication,
                            'title' => $title,
                            'ownerId' => $ownerId,
                            'external' => $external,
                            'editorNote' => $editorNote,
                            'tstmpDatePublished' => $tstmpDatePublished))) {
        return; // throw back
    }

    // Check if issue was pubished and now unpublished
    if (($oldissue['datePublished']['timestamp'] != 0) && ($tstmpDatePublished == 0)) {
        // Unpublish all of the associated stories
        $topics = xarModAPIFunc('newsletter',
                                'user',
                                'get',
                                array('issueId' => $id,  
                                      'phase' => 'topic'));
        
        // Check for exceptions
        if (!isset($topics) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
            return; // throw back

        // Loop through and unpublish all stories
        foreach ($topics as $topic) {
            // Unpublish the story
            if(!xarModAPIFunc('newsletter',
                              'admin',
                              'unpublishstory',
                              array('id' => $topic['storyId']))) {
                return; // throw back
            }
        }
    } else if (($oldissue['datePublished']['timestamp'] == 0) && ($tstmpDatePublished != 0)) {
        // Publish all of the associated stories
        $topics = xarModAPIFunc('newsletter',
                                'user',
                                'get',
                                array('issueId' => $id,  
                                      'phase' => 'topic'));
        
        // Check for exceptions
        if (!isset($topics) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
            return; // throw back

        // Loop through and publish all stories
        foreach ($topics as $topic) {
            // Publish the story
            if(!xarModAPIFunc('newsletter',
                              'admin',
                              'publishstory',
                              array('id' => $topic['storyId'],
                                    'datePublished' => $tstmpDatePublished))) {
                return; // throw back
            }
        }
    }

    
            
    xarSessionSetVar('statusmsg', xarML('Newsletter Story Update'));

    // Redirect
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'viewissue'));

    // Return
    return true;
}

?>
