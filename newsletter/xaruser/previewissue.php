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
 * Preview an issue before publication
 * 
 * @public
 * @author Richard Cave
 * @param 'issueId' the id of the issue to preview
 * @returns string
 * @return $issueHTML
 */
function newsletter_user_previewissue($args)
{
    // Extract args
    extract ($args);

    // Security check
    if(!xarSecurityCheck('ReadNewsletter')) return;

    // Get parameters from the input
    if (!isset($issueId) &&  !xarVarFetch('issueId', 'id', $issueId)) {
        xarExceptionFree();
        $msg = xarML('You must choose an issue to preview.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get the user menu
    $data = xarModAPIFunc('newsletter', 'user', 'menu');

    // Get issue for display
    $issue = xarModAPIFunc('newsletter',
                           'user',
                           'getissuefordisplay',
                           array('issueId' => $issueId));

    if (!$issue)
        return; // throw back

    // Get publication for display
    $publication = xarModAPIFunc('newsletter',
                                 'user',
                                 'getpublicationfordisplay',
                                 array('publicationId' => $issue['pid'],
                                       'phase' => 'publication'));

    if (!$publication)
        return; // throw back

    // Set publication id and issue id
    $data['publicationid'] = $issue['pid'];
    $data['issueid'] = $issueId; 

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // create template array
    $templateVarArray = array('data' => $data,
        'publication' => $publication,
        'issue' => $issue);

    // Get the publication template
    $templateName = $publication['templateHTML'];

    $sourceFileName = xarModAPIFunc('newsletter',
                                    'user',
                                    'gettemplatefile',
                                    array('filename' => $templateName));

    if (!$sourceFileName)
        return;      
        
    // Call blocklayout with the template to parse it and generate HTML
    $issueHTML = xarTplFile($sourceFileName,$templateVarArray);

    // Check if preview is in new browser window
    if (xarModGetVar('newsletter', 'previewbrowser')) {
        // We're going to open a new browser window and display the
        // issue in that browser window.  So print $issueHTML and 
        // then die as we don't want any further processing to happen
        print $issueHTML;
        die();
    } else {
        // We need to strip out <html>, <title>, <body>, etc before
        // the issue is displayed in the same browser window
        $stripTags = array('html','body','meta','link','head');
        foreach ($stripTags as $tag) {
            $issueHTML = preg_replace("/<\/?" . $tag . "(.|\s)*?>/","",$issueHTML);
        }
        // Stripping opening and closing tags AND what's in between
        $stripTagsAndContent = array('title');
        foreach ($stripTagsAndContent as $tag) {
            $issueHTML = preg_replace("/<" . $tag . ">(.|\s)*?<\/" . $tag . ">/","",$issueHTML);
        }
        return $issueHTML;
    }
}

?>
