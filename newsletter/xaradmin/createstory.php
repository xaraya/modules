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
 * Create a new Newsletter story
 *
 * @public
 * @author Richard Cave
 * @param 'ownerId' the id of the story owner (uid in roles)
 * @param 'publicationId' the publication id of the story
 * @param 'issueId' the issue id of the story
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
 * @param 'registerLink' does the link require registration to view?(0=no, 1=yes) 
 * @param 'linkExpiration' override of default publication link expiration 
 * @param 'commentary' commentary on the story content
 * @param 'commentarySource' source of the commentary from dropdown list
 * @param 'newCommentarySource' new source of the commentary
 * @returns bool
 * @return true on success, false on failure
 */
function newsletter_admin_createstory()
{
    // Get parameters from the input
    if (!xarVarFetch('ownerId', 'id', $ownerId)) {
        xarExceptionFree();
        $msg = xarML('You must select an owner name.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (!xarVarFetch('publicationId', 'int:0:', $publicationId, 0)) return;
    if (!xarVarFetch('issueId', 'int:0:', $issueId, 0)) return;
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
    if (!xarVarFetch('newCommentarySource', 'str:1:', $newCommentarySource, '')) return;
    // Get submit button
    if (!xarVarFetch('submit', 'str:1:', $submitbutton, '')) return;

    // Add new commentary source if field isn't empty
    if (!empty($newCommentarySource)) {
        // Get the list of commentary sources from module var
        $commsource = xarModGetVar('newsletter', 'commentarysource');
        if (!empty($commsource)) {
            if (!is_array($commsource = @unserialize($commsource))) {
                $commsource = array();
            }
        } else {
            $commsource = array();
        }

        // Check if publication is in commentary source array
        $foundSource = false;
        if (isset($commsource[$publicationId])) {
            // See if source has already been added to array
            foreach ($commsource[$publicationId] as $pubsource) {
                if ($pubsource['source'] == $newCommentarySource) {
                    $foundSource = true;
                    break;
                }
            }
            if (!$foundSource) {
                $commsource[$publicationId][] = array('source' => $newCommentarySource);
                // Set commentary source
                $commentarySource = $newCommentarySource;
            }
        } else {
            $commsource[$publicationId][] = array('source' => $newCommentarySource);
            // Set commentary source
            $commentarySource = $newCommentarySource;
        }

        if (!$foundSource) {
            // Set module var
            $commsource = serialize($commsource);
            xarModSetVar('newsletter', 'commentarysource', $commsource);
        }
    }

    // Check and format dates - dates are stored as UNIX timestamp
    if (empty($storyDateMon) || empty($storyDateDay) || empty($storyDateYear)) {
        $tstmpStoryDate =  0;
    } else {
        $tstmpStoryDate = mktime(0,0,0,$storyDateMon,$storyDateDay,$storyDateYear);
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

    // Set DatePublished to 0 for a new story
    $tstmpDatePublished =  0;

    // Call create story function API
    $storyId = xarModAPIFunc('newsletter',
                             'admin',
                             'createstory',
                              array('ownerId' => $ownerId,
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
                                    'tstmpDatePublished' => $tstmpDatePublished));

    // Check return value
    if (!isset($storyId) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // A new story has been added to an issue, so sort the stories
    if ($issueId && $storyId) {
        // Add this story to topic
        $topic = xarModAPIFunc('newsletter',
                               'admin',
                               'createtopic',
                               array('issueId' => $issueId,
                                     'storyId' => $storyId,
                                     'cid' => $categoryId,
                                     'storyOrder' => 0));

        // Check return value
        if (!isset($topic) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
            return; // throw back
        }

        // Sort the stories
        if (!xarModAPIFunc('newsletter',
                           'admin',
                           'updateissuetopics',
                           array('issueId' => $issueId))) {
            return; // throw back
        }
    }

    // Success
    xarSessionSetVar('statusmsg', xarML('Story Created'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    $templateVarArray = array('publicationId' => $publicationId,
                              'issueId' => $issueId,
                              'ownerId' => $ownerId);
                              
    // If creating another story
    if ($submitbutton == 'Add Another Story') {
        xarResponseRedirect(xarModURL('newsletter', 'admin', 'newstory', $templateVarArray));
    } else {
        // Go to view issue
        xarResponseRedirect(xarModURL('newsletter', 'admin', 'viewissue', $templateVarArray));
    }
}

?>
