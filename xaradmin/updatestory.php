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
 * @param 'articleid' articleid to use with story
 * @returns bool
 * @return true on success, false on failure
 */
function newsletter_admin_updatestory()
{
    // Confirm authorization code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for updating #(1) item #(2) in function #(3)',
                    'Newsletter', xarVarPrepForDisplay($id), 'newsletter_admin_updatestory');
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get parameters from the input
    if (!xarVarFetch('id', 'id', $id)) return;
    if (!xarVarFetch('articleid', 'int:0:', $articleid,0)) return;

    
    // make sure they're the owner
    if (!xarVarFetch('ownerId', 'id', $ownerId)) {
        xarErrorFree();
        $formErrorMsg['owner'] .= xarML('You must select an owner name.');
    }

    
    if (!xarVarFetch('publicationId', 'int:0:', $publicationId, 0)) return;
    if (!xarVarFetch('priority', 'int:0:1:', $priority, 0)) return;
    if (!xarVarFetch('storyDateMon', 'int:0:', $storyDateMon, 0)) return;
    if (!xarVarFetch('storyDateDay', 'int:0:', $storyDateDay, 0)) return;
    if (!xarVarFetch('storyDateYear', 'int:0:', $storyDateYear, 0)) return;
    if (!xarVarFetch('altDate', 'str:1:', $altDate, '')) return;
    if (!xarVarFetch('fullTextLink', 'str:1:', $fullTextLink, '')) return;
    if (!xarVarFetch('registerLink', 'int:0:1:', $registerLink, 0)) return;
    if (!xarVarFetch('linkExpiration', 'int:0:', $linkExpiration, -1)) return;
    if (!xarVarFetch('commentary', 'str:1:', $commentary, '')) return;
    if (!xarVarFetch('commentarySource', 'str:1:', $commentarySource, '')) return;
    if (!xarVarFetch('newCommentarySource', 'str:1:', $newCommentarySource, '')) return;
    if (!xarVarFetch('datePublishedMon', 'int:0:', $datePublishedMon, 0)) return;
    if (!xarVarFetch('datePublishedDay', 'int:0:', $datePublishedDay, 0)) return;
    if (!xarVarFetch('datePublishedYear', 'int:0:', $datePublishedYear, 0)) return;
    if (!xarVarFetch('categoryId', 'id', $categoryId, 0)) return;
    if (!xarVarFetch('source', 'str:1:', $source, '')) return;
    if (!xarVarFetch('title', 'str:0:', $title, '')) return;
    if (!xarVarFetch('content', 'str:0:', $content, '')) return;

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

    // If commentary exists, then check that a commentary source 
    // was entered
    if (!empty($commentary) && (empty($commentarySource) && empty($newCommentarySource))) {
        xarErrorFree();
        $formErrorMsg['comment'] = xarML('You must enter a commentary source for the commentary.');
    }

    // see if an error was found above.  if so, put it in the data array and send them back to the form
    if (!empty($formErrorMsg)){
        // get the auth id to pas back into the modify story
        xarVarFetch('authid', 'str:1:', $authid);
        $_sendBackData=array_merge(array('formErrorMsg'=>$formErrorMsg),array('story'=>$_REQUEST));
        
        // go back to modify story
        return xarModFunc('newsletter','admin','modifystory',$_sendBackData);
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

    // Check and format storyDate - dates are stored as UNIX timestamp
    if ($storyDateMon == 0 || $storyDateDay == 0 || $storyDateYear == 0) {
        $tstmpStoryDate =  0;
    } else {
        $tstmpStoryDate = mktime(0,0,0,$storyDateMon,$storyDateDay,$storyDateYear);
    }

    // Check and format datePublished - dates are stored as UNIX timestamp
    if ($datePublishedMon == 0 || $datePublishedDay == 0 || $datePublishedYear == 0) { 
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
                            'articleid' => $articleid,
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

    // Resort the stories
    if (!xarModAPIFunc('newsletter',
                       'admin',
                       'updateissuetopics',
                       array('issueId' => $topic['issueId']))) {
        return; // throw back
    }

    xarSessionSetVar('statusmsg', xarML('Newsletter Story Update'));

    // Redirect
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'viewstory', array('issueId' => $topic['issueId'])));
}

?>
