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
 * Update an Newsletter story
 *
 * @public
 * @author Richard Cave
 * @param 'id' the id of the story to be updated
 * @param 'ownerId' the id of the story owner (uid in roles)
 * @param 'publicationId' the publication of the story
 * @param 'categoryId' the category id of the story
 * @param 'title' the title of the story
 * @param 'source' the source for the content of the story
 * @param 'content' the content of the story
 * @param 'priority' - not used currently
 * @param 'storyDateMon' the month the story was published
 * @param 'storyDateDay' the day the story was published
 * @param 'storyDateYear' the year the story was published
 * @param 'altDate' alternative text date field if no publication date of story
 * @param 'fullTextLink' the full text link to the story
 * @param 'registerLink' does the full text link require registration to view?(0=no, 1=yes) 
 * @param 'linkExpiration' override of default publication link expiration 
 * @param 'commentary' commentary on the story content
 * @param 'commentarySource' source of the commentary
 * @param 'newCommentarySource' new source of the commentary
 * @param 'datePublishedMon' the month the story was published
 * @param 'datePublishedDay' the day the story was published
 * @param 'datePublishedYear' the year the story was published
 * @returns bool
 * @return true on success, false on failure
 */
function newsletter_admin_updatestory()
{
    // Confirm authorization code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for updating #(1) item #(2) in function #(3)',
                    'Newsletter', xarVarPrepForDisplay($id), 'newsletter_admin_updatestory');
        xarExceptionSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get parameters from the input
    if (!xarVarFetch('id', 'id', $id)) return;

    if (!xarVarFetch('ownerId', 'id', $ownerId)) {
        xarExceptionFree();
        $msg = xarML('You must select an owner name.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (!xarVarFetch('publicationId', 'int:0:', $publicationId, 0)) return;
    if (!xarVarFetch('categoryId', 'id', $categoryId, 0)) return;

    if (!xarVarFetch('title', 'str:1:', $title)) {
        xarExceptionFree();
        $msg = xarML('You must enter a title for the story.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (!xarVarFetch('source', 'str:1:', $source, '')) return;

    if (!xarVarFetch('content', 'str:1:', $content)) {
        xarExceptionFree();
        $msg = xarML('You must provide content for the story.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (!xarVarFetch('priority', 'int:0:1:', $priority, 0)) return;
    if (!xarVarFetch('storyDateMon', 'str:1:', $storyDateMon, '')) return;
    if (!xarVarFetch('storyDateDay', 'str:1:', $storyDateDay, '')) return;
    if (!xarVarFetch('storyDateYear', 'str:1:', $storyDateYear, '')) return;
    if (!xarVarFetch('altDate', 'str:1:', $altDate, '')) return;
    if (!xarVarFetch('fullTextLink', 'str:1:', $fullTextLink, '')) return;
    if (!xarVarFetch('registerLink', 'int:0:1:', $registerLink, 0)) return;
    if (!xarVarFetch('linkExpiration', 'int:0:', $linkExpiration, -1)) return;
    if (!xarVarFetch('commentary', 'str:1:', $commentary, '')) return;
    if (!xarVarFetch('commentarySource', 'str:1:', $commentarySource, '')) return;
    if (!xarVarFetch('datePublishedMon', 'str:1:', $datePublishedMon, '')) return;
    if (!xarVarFetch('datePublishedDay', 'str:1:', $datePublishedDay, '')) return;
    if (!xarVarFetch('datePublishedYear', 'str:1:', $datePublishedYear, '')) return;

    // Check and format storyDate - dates are stored as UNIX timestamp
    if (empty($storyDateMon) || empty($storyDateDay) || empty($storyDateYear)) {
        $tstmpStoryDate =  0;
    } else {
        $tstmpStoryDate = mktime(0,0,0,$storyDateMon,$storyDateDay,$storyDateYear);
    }

    // Check and format datePublished - dates are stored as UNIX timestamp
    if (empty($datePublishedMon) || empty($datePublishedDay) || empty($datePublishedYear)) { 
            $tstmpDatePublished =  0;
    } else {
        $tstmpDatePublished = mktime(0,0,0,$datePublishedMon,$datePublishedDay,$datePublishedYear);
    }
            
    // Check if no link expiration was entered
    if ($linkExpiration < 0 ) {
        // Get publication link expiration
        if ($publicationId != 0) {
            $pubItem = xarModAPIFunc('newsletter',
                                     'user',
                                     'getpublication',
                                     array('id' => $publicationId));

            // Check for exceptions
            if (!isset($pubItem) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
                return; // throw back

            $linkExpiration = $pubItem['linkExpiration'];
        } else {
            // Default to never expire
            $linkExpiration = 0;
        }
    } 

    // Call API function
    if(!xarModAPIFunc('newsletter',
                      'admin',
                      'updatestory',
                      array('id' => $id,
                            'ownerId' => $ownerId,
                            'publicationId' => $publicationId,
                            'categoryId' => $categoryId,
                            'title' => $title,
                            'source' => $source,
                            'content' => $content,
                            'priority' => $priority,
                            'tstmpStoryDate' => $tstmpStoryDate,
                            'altDate' => $altDate,
                            'fullTextLink' => $fullTextLink,
                            'registerLink' => $registerLink,
                            'linkExpiration' => $linkExpiration,
                            'commentary' => $commentary,
                            'commentarySource' => $commentarySource,
                            'tstmpDatePublished' => $tstmpDatePublished))) {
        return; // throw back
    }

    // Find out which issue this is in
    $topic = xarModAPIFunc('newsletter',
                           'user',
                           'gettopicbystory',
                           array('storyId' => $id));
    
    // Check for exceptions
    if (!isset($topic) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    xarSessionSetVar('statusmsg', xarML('Newsletter Story Update'));

    // Redirect
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'viewstory', array('issueId' => $topic['issueId'])));
}

?>
