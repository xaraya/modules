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
 * Display a past Newsletter issue
 *
 * @public
 * @author Richard Cave
 * @param 'issueId' the story id 
 * @returns string
 * @return $issueHTML
 */
function newsletter_user_displayarchive()
{
    // Get parameters from the input
    if (!xarVarFetch('issueId', 'id', $issueId)) {
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
                                 array('publicationId' => $issue['pid']));

    if (!$publication)
        return; // throw back

    // Set template strings
    $data['displaytitle'] = xarVarPrepForDisplay(xarML('View Past Issue'));

    // create template array
    $templateVarArray = array(
        'data' => $data,
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
    
    // Return the HTML
    return $issueHTML;
}

?>
