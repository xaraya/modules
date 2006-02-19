<?php
/**
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
 */
/**
 * Mail an Newsletter issue
 *
 * @public
 * @param 'issueId' the id of the issue to publish
 * @author Richard Cave
 * @return array xarTplModule('mailissue') redirect to 'mailissue'
 */
function newsletter_admin_mailissue()
{
    // Security check
    if(!xarSecurityCheck('AdminNewsletter')) return;

    // Get parameters from input
    if (!xarVarFetch('issueId', 'id', $issueId)) {
        xarErrorFree();
        $msg = xarML('You must choose an issue to publish.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    if (!xarVarFetch('func', 'str', $data['page'],  'main', XARVAR_NOT_REQUIRED)) return;

    // Get the issue for display
    $issue = xarModAPIFunc('newsletter',
                           'user',
                           'getissuefordisplay',
                           array('issueId' => $issueId));

    // Check for exceptions
    if (!isset($issue) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back

    // Determine if issue was already published
    if ($issue['datePublished']['timestamp'] != 0) {
        // If this issue was already published, then throw error and return
        xarErrorFree();
        $msg = xarML('This issue has already been published.  You must edit the issue and remove the date published to publish again.');
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Add field to track source of issue.  This will be set to
    // either 'web' or 'email'.  This can be used to optionally
    // track hits of issue through AWStats, etc.
    $issue['source'] = 'email';

    // Get the publication for display
    $publication = xarModAPIFunc('newsletter',
                                 'user',
                                 'getpublicationfordisplay',
                                 array('publicationId' => $issue['pid']));

    if (!$publication)
        return; // throw back

    // Get the admin edit menu
    $menu = xarModApiFunc('newsletter', 'admin', 'editmenu');

    // create template array
    $templateVarArray = array(
        'menu' => $menu,
        'publication' => $publication,
        'issue' => $issue,
        'page' => $data['page'],
        'bulkemail' => xarModGetVar('newsletter', 'bulkemail'),
        'viewsubscriptionurl' => xarModURL('newsletter',
                                           'admin',
                                           'viewsubscription',
                                           array('search' => 'name',
                                                 'publicationId' => $issue['pid'],
                                                 'sortby' => 'name')));


    // Set issue and story date published fields
    $result = xarModAPIFunc('newsletter',
                            'admin',
                            'setdatepublished',
                            array('issue' => $issue,
                                  'date' => time()));

    // Get the publication templates for HTML
    $templateName = $publication['templateHTML'];
    $sourceFileNameHTML = xarModAPIFunc('newsletter',
                                        'user',
                                        'gettemplatefile',
                                        array('filename' => $templateName));

    if (!$sourceFileNameHTML)
        return;

    // Call blocklayout with the template to parse it and generate HTML
    $issueHTML = xarTplFile($sourceFileNameHTML, $templateVarArray);

    // Make sure there is something to mail
    if (empty($issueHTML))
        return;

    // Get the publication templates for text
    $templateName = $publication['templateText'];
    $sourceFileNameText = xarModAPIFunc('newsletter',
                                        'user',
                                        'gettemplatefile',
                                        array('filename' => $templateName));

    // If there is no text file template, then assume
    // that we're using the HTML template
    if (!file_exists($sourceFileNameText)) {
        // We need to strip out <html>, <title>, <body>, etc
        // from the HTML template
        $stripTags = array('html','body','meta','link','head');
        foreach ($stripTags as $tag) {
            $issueText = preg_replace("/<\/?" . $tag . "(.|\s)*?>/","",$issueHTML);
        }
        // Stripping opening and closing tags AND what's in between
        $stripTagsAndContent = array('title');
        foreach ($stripTagsAndContent as $tag) {
            $issueText = preg_replace("/<" . $tag . ">(.|\s)*?<\/" . $tag . ">/","",$issueHTML);
        }
    } else {
        // Call blocklayout with the template to parse and generate text file
        $issueText = xarTplFile($sourceFileNameText, $templateVarArray);

        // FIX ME!
        // Ugly hack until the white space issue is resolved in block layout
        $issueText = preg_replace( '!<br.*>!iU', "\n", $issueText );

        // Just in case...
        $issueText = strip_tags($issueText);
    }

    // Send as either bulk email to all subscribers or single email
    // to each individual subscriber
    if (xarModGetVar('newsletter', 'bulkemail')) {
        $emailResultArray = newsletter__bulk_email(array('publication' => $publication,
                                                         'issue' => $issue,
                                                         'issueText' => $issueText,
                                                         'issueHTML' => $issueHTML));
    } else {
        $emailResultArray = newsletter__single_email(array('publication' => $publication,
                                                           'issue' => $issue,
                                                           'issueText' => $issueText,
                                                           'issueHTML' => $issueHTML));
    }

    if (!$emailResultArray) {
        return;
    }


    // Merge emailResultArray and templateVarArray
    $templateResultArray = array_merge($templateVarArray, $emailResultArray);

    // Return the template variables defined in this function
    return xarTplModule('newsletter', 'admin', 'mailissue', $templateResultArray);
}


/**
 * Send an email to each individual newsletter subscriber
 *
 * @private
 * @author Richard Cave
 * @param $publication publication of the issue
 * @param $issue issue to email
 * @param $issueText body of issue in text format
 * @param $issueHTML body of issue in HTML format
 * @return array $emailResultArray
 */
function newsletter__single_email($args)
{
    // Extract args
    extract($args);

    // Check args
    if (!isset($publication) || !isset($issue) || !isset($issueText) || !isset($issueHTML)) {
        return;
    }

    // Get configuration setting for mailing newsletter to active users
    $activeusers = xarModGetVar('newsletter','activeusers');

    // Initialize issue count totals and errors
    $issueCounts = array('total' => 0,
                         'errors' => 0);

    // Get subscription list - don't send uid
    $subscriptions = xarModAPIFunc('newsletter',
                                   'admin',
                                   'searchsubscription',
                                    array('search' => 'publication',
                                          'pid' => $issue['pid'],
                                          'startnum' => 1,
                                          'numitems' => -1));

    if (!empty($subscriptions)) {
        foreach ($subscriptions as $subscription) {
            // Check if subscription is an active user
            $goodtogo = false;
            if ($activeusers) {
                // Only send newsletter to active users
                if ($subscription['state'] == 3) {
                    $goodtogo = true;
                }
            } else {
                // We don't care about active users so set flag to true
                $goodtogo = true;
            }

            // Are we good to go?
            if ($goodtogo) {
                // Set email address for html or text email
                if ($subscription['htmlmail']) {
                    // Mail the html issue to the subscription base
                    $result = xarModAPIFunc('newsletter',
                                            'admin',
                                            'mailissue',
                                            array('publication'  => $publication,
                                                  'issue'        => $issue,
                                                  'recipients'   => array($subscription['email'] => $subscription['name']),
                                                  'issueText'    => $issueText,
                                                  'issueHTML'    => $issueHTML,
                                                  'type'         => 'html'));
                } else {
                    // Mail the text issue to the subscription base
                    $result = xarModAPIFunc('newsletter',
                                            'admin',
                                            'mailissue',
                                            array('publication'  => $publication,
                                                  'issue'        => $issue,
                                                  'recipients'   => array($subscription['email'] => $subscription['name']),
                                                  'issueText'    => $issueText,
                                                  'issueHTML'    => $issueHTML,
                                                  'type'         => 'text'));
                }

                // If mail failed, set mailerror to true
                if (!$result) {
                    $issueCounts['errors']++;
                }

                // Update issue counts
                $issueCounts['total']++;
            }
        }
    }

    $emailResultArray['issueCounts'] = $issueCounts;

    return $emailResultArray;
}


/**
 * Send a single email to every newsletter subscriber
 *
 * @private
 * @author Richard Cave
 * @param $publication publication of the issue
 * @param $issue issue to email
 * @param $issueText body of issue in text format
 * @param $issueHTML body of issue in HTML format
 * @return array $emailResultArray
 */
function newsletter__bulk_email($args)
{
    // Extract args
    extract($args);

    // Check args
    if (!isset($publication) || !isset($issue) || !isset($issueText) || !isset($issueHTML)) {
        return;
    }

    // Get configuration setting for mailing newsletter to active users
    $activeusers = xarModGetVar('newsletter','activeusers');

    // Initialize arrays
    $recipients = array();
    $htmlrecipients = array();
    $textrecipients = array();

    // Initialize issue count totals and errors
    $issueCounts = array('total' => 0,
                         'errors' => 0);

    // Get subscription list - don't send uid
    $subscriptions = xarModAPIFunc('newsletter',
                                   'admin',
                                   'searchsubscription',
                                    array('search' => 'publication',
                                          'pid' => $issue['pid'],
                                          'startnum' => 1,
                                          'numitems' => -1));

    if (!empty($subscriptions)) {
        foreach ($subscriptions as $subscription) {
            // Check if subscription is an active user
            $goodtogo = false;
            if ($activeusers) {
                // Only send newsletter to active users
                if ($subscription['state'] == 3) {
                    $goodtogo = true;
                }
            } else {
                // We don't care about active users so set flag to true
                $goodtogo = true;
            }

            // Are we good to go?
            if ($goodtogo) {
                // Set email address for html or text email
                if ($subscription['htmlmail']) {
                    $htmlrecipients[$subscription['email']] = $subscription['name'];
                } else {
                    $textrecipients[$subscription['email']] = $subscription['name'];
                }

                // Update issue counts
                $issueCounts['total']++;
            }
        }
    }

    // Get fromname and fromemail information
    if (empty($issue['fromname']) || empty($issue['fromemail'])) {
        // Get publication information
        $pubinfo = xarModAPIFunc('newsletter',
                                 'user',
                                 'getpublication',
                                 array('id' => $issue['pid']));

        // Check for exceptions
        if (!isset($pubinfo) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
            return; // throw back

        // Set name and/or email
        if (empty($fromname)) {
            $issue['fromname'] = $pubinfo['fromname'];
        }
        if (empty($fromemail)) {
            $issue['fromemail'] = $pubinfo['fromemail'];
        }
    }

    // Set recipient to owner email and publication name
    $recipients[$issue['fromemail']] = $issue['fromname'];

    // Mail the html issue to the subscription base
    if (!empty($htmlrecipients)) {
        $result = xarModAPIFunc('newsletter',
                                'admin',
                                'mailissue',
                                array('publication'   => $publication,
                                      'issue'         => $issue,
                                      'recipients'    => $recipients,
                                      'bccrecipients' => $htmlrecipients,
                                      'issueText'     => $issueText,
                                      'issueHTML'     => $issueHTML,
                                      'type'          => 'html'));
    }

    // Mail the text issue to the subscription base
    if (!empty($textrecipients)) {
        $result = xarModAPIFunc('newsletter',
                                'admin',
                                'mailissue',
                                array('publication'   => $publication,
                                      'issue'         => $issue,
                                      'recipients'    => $recipients,
                                      'bccrecipients' => $textrecipients,
                                      'issueText'     => $issueText,
                                      'issueHTML'     => $issueHTML,
                                      'type'          => 'text'));
    }

    // Set issue counts
    $emailResultArray['issueCounts'] = $issueCounts;

    return $emailResultArray;
}

?>
