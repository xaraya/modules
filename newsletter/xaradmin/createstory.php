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
 * @param 'articleid' articleid to use with story
 * @returns bool
 * @return true on success, false on failure
 */
function newsletter_admin_createstory()
{

    
    // Get parameters from the input
    if (!xarVarFetch('ownerId', 'id', $ownerId)) {
        xarErrorFree();
        $formErrorMsg['owner'] .= xarML('You must select an owner name.');
    }

    if (!xarVarFetch('publicationId', 'int:0:', $publicationId, 0)) return;
    if (!xarVarFetch('issueId', 'int:0:', $issueId, 0)) return;
    if (!xarVarFetch('categoryId', 'id', $categoryId, 0)) return;
    if (!xarVarFetch('source', 'str:1:', $source, '')) return;
    if (!xarVarFetch('articleid', 'int:0', $articleid, 0)) return;
    if (!xarVarFetch('title', 'str', $title, '')) return;
    if (!xarVarFetch('content', 'str', $content, '')) return;

    // they must enter a title, unless they have selected an article
    if (empty($title) && ($articleid==0)) {
        xarErrorFree();
        $formErrorMsg['title'] = xarML('You must enter a title for the story');
        if (xarModIsAvailable('articles')){
             $formErrorMsg['title'] .= xarML(' or select an article to use.');
        }
    }

    // they must enter content, unless they have selected an article
    if (empty($content) && ($articleid==0)) {
        xarErrorFree();
        $formErrorMsg['content'] = xarML('You must provide content for the story ');
        if (xarModIsAvailable('articles')){
             $formErrorMsg['content'] .= xarML('or select an article to use.');
        }
    }

    // If the title is empty, then set the title from the article
    if (empty($title)) {
        $_article  = current(xarModAPIFunc('articles',
                                           'user',
                                           'getAll',
                                           array('aids'=>array($articleid),
                                                 'extra'=>array('dynamicdata'))));
        $title = $_article['title'];
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
    if (!xarVarFetch('submitValue', 'str:1:', $submitbutton, '')) return;

    // If commentary exists, then check that a commentary source was entered
    if (!empty($commentary) && (empty($commentarySource) && empty($newCommentarySource))) {
        xarErrorFree();
        $formErrorMsg['comment'] = xarML('You must enter a commentary source for the commentary.');
    }

    // see if an error was found above.  if so, put it in the data array and send them back to the form
    if (!empty($formErrorMsg)){
        $_sendBackData = array_merge(array('formErrorMsg'=>$formErrorMsg),$_REQUEST);
        // go back to create story
//        return xarModFunc('newsletter','admin','newstory',array('formErrorMsg'=>$formErrorMsg,'content'=>$content));

        return xarModFunc('newsletter','admin','newstory',$_sendBackData);
    }
    
    // Add new commentary source if field isn't empty
    if (!empty($newCommentarySource)) {
            $newcommsource = xarModAPIFunc('newsletter',
                                    'admin',
                                    'newcommentarysource',
                                    array('publicationId' => $publicationId,
                                          'newCommentarySource' => $newCommentarySource));
            
            if (!empty($newcommsource)) {
                $commentarySource = $newcommsource;
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
                                    'tstmpDatePublished' => $tstmpDatePublished,
                                    'articleid' => $articleid));

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
                              'ownerId' => $ownerId,
                              'categoryId' => $categoryId);
                              
    // If creating another story
    if ($submitbutton == 'Add Another Story') {
        xarResponseRedirect(xarModURL('newsletter', 'admin', 'newstory', $templateVarArray));
    } else {
        // Go to view issue
        xarResponseRedirect(xarModURL('newsletter', 'admin', 'viewissue', $templateVarArray));
    }
}

?>
