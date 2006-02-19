<?php
/**
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Newsletter module
 * @author Richard Cave <rcave@xaraya.com>
 */
/**
 * Preview an issue before publication
 *
 * @public
 * @author Richard Cave
 * @param id 'issueId' the id of the issue to preview
 * @return string $issueHTML
 */
function newsletter_admin_previewissue($args)
{
    // Extract args
    extract ($args);

    // Security check
    if(!xarSecurityCheck('ReadNewsletter')) return;

    // Get parameters from the input
    if (!isset($issueId) &&  !xarVarFetch('issueId', 'id', $issueId)) {
        xarErrorFree();
        $msg = xarML('You must choose an issue to preview.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get the user menu
    $data = xarModAPIFunc('newsletter', 'user', 'menu');

    // Get issue for display
    $issue = xarModAPIFunc('newsletter',
                           'user',
                           'getissuefordisplay',
                           array('issueId' => $issueId));

    if (!$issue) {
        return; // throw back
    }

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

    // Add field to track source of issue.  This will be set to
    // either 'web' or 'email'.  This can be used to optionally
    // track hits of issue through AWStats, etc.
    $issue['source'] = 'web';

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

    // Check if a file was returned
    if (!$sourceFileName) {
        // Try to get the text template
        $templateName = $publication['templateText'];

        $sourceFileName = xarModAPIFunc('newsletter',
                                        'user',
                                        'gettemplatefile',
                                        array('filename' => $templateName));

        if (!$sourceFileName) {
            return;
        }
    }

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
