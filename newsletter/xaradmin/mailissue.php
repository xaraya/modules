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
 * Mail an Newsletter issue
 *
 * @public
 * @param 'issueId' the id of the issue to publish
 * @author Richard Cave
 * @returns xarTplModule('mailissue')
 * @return redirect to 'mailissue'
 */
function newsletter_admin_mailissue()
{
    // Security check
    if(!xarSecurityCheck('AdminNewsletter')) return;

    // Get parameters from input
    if (!xarVarFetch('issueId', 'id', $issueId)) {
        xarExceptionFree();
        $msg = xarML('You must choose an issue to publish.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

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
        xarExceptionFree();
        $msg = xarML('This issue has already been published.  You must edit the issue and remove the date published to publish again.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get the publication for display
    $publication = xarModAPIFunc('newsletter',
                                 'user',
                                 'getpublicationfordisplay',
                                 array('publicationId' => $issue['pid']));

    if (!$publication)
        return; // throw back
    
    // Get the admin edit menu
    $menu = xarModFunc('newsletter', 'admin', 'editmenu');

    // create template array
    $templateVarArray = array(
        'menu' => $menu,
        'publication' => $publication,
        'issue' => $issue,
        'bulkemail' => xarModGetVar('newsletter', 'bulkemail'));

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
 * @return array
 * @returns $emailResultArray
 */
function newsletter__single_email($args)
{
    // Extract args
    extract($args);

    // Check args
    if (!isset($publication) || !isset($issue) || !isset($issueText) || !isset($issueHTML)) {
        return;
    }

    // Initialize userData array
    $userData = array();

    // Initialize issue count totals and errors
    $issueCounts = array('total' => 0,
                         'errors' => 0);

    // Get subscription list - don't send uid
    $subscriptions = xarModAPIFunc('newsletter',
                                   'user',
                                   'get',
                                    array('id' => 0, // doesn't matter
                                          'pid' => $issue['pid'],
                                          'phase' => 'subscription'));

    if (!empty($subscriptions)) {
        foreach ($subscriptions as $subscription) {

            // Get the user's email address from roles
            $userData = xarModAPIFunc('roles',
                                      'user',
                                      'get',
                                      array('uid' => $subscription['uid']));

            if (!isset($userData)) {
                $userData['mailerror'] = true;
                $userData['nameurl'] = '';
                $userData['emailurl'] = '';
                $issueCounts['errors']++; // show error for this user
            } else {
                $userData['mailerror'] = false;

                // Set link to search user by name
                $userData['nameurl'] = xarModURL('newsletter',
                                                 'admin',
                                                 'viewsubscription',
                                                 array('search' => "publication",
                                                       'publicationId' => $issue['pid'],
                                                       'searchname' => $userData['name']));
    
                // Set link to search user by email
                $userData['emailurl'] = xarModURL('newsletter',
                                                  'admin',
                                                  'viewsubscription',
                                                  array('search' => "publication",
                                                        'publicationId' => $issue['pid'],
                                                        'searchname' => $userData['email']));
    
                // Set how the user wants the mail sent - html or text
                $userData['htmlmail'] = $subscription['htmlmail'];

                // Set email address for html or text email
                if ($subscription['htmlmail']) {
                    // Mail the html issue to the subscription base
                    $result = xarModAPIFunc('newsletter',
                                            'admin',
                                            'mailissue',
                                            array('publication'  => $publication,
                                                  'issue'        => $issue,
                                                  'recipients'   => array($userData['email'] => $userData['name']),
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
                                                  'recipients'   => array($userData['email'] => $userData['name']),
                                                  'issueText'    => $issueText,
                                                  'issueHTML'    => $issueHTML,
                                                  'type'         => 'text'));
                }

                // If mail failed, set mailerror to true
                if (!$result) {
                    $userData['mailerror'] = true;
                    $issueCounts['errors']++;
                }

                // Update issue counts
                $issueCounts['total']++;
            }

            // Add the subscription
            $emailResultArray['subscriptions'][] = $userData;
        }
    }

    // Get alternative subscription list - don't send uid
    $altsubscriptions = xarModAPIFunc('newsletter',
                                      'user',
                                      'get',
                                       array('id' => 0, // doesn't matter
                                             'pid' => $issue['pid'],
                                             'phase' => 'altsubscription'));

    if (!empty($altsubscriptions)) {
        foreach ($altsubscriptions as $subscription) {

            // Set user data fields
            $userData['name'] = $subscription['name'];
            $userData['email'] = $subscription['email'];
            $userData['mailerror'] = false;

            // Set link to search user by name
            $userData['nameurl'] = xarModURL('newsletter',
                                             'admin',
                                             'viewsubscription',
                                             array('search' => "publication",
                                                   'publicationId' => $issue['pid'],
                                                   'searchname' => $userData['name']));
    
            // Set link to search user by email
            $userData['emailurl'] = xarModURL('newsletter',
                                              'admin',
                                              'viewsubscription',
                                              array('search' => "publication",
                                                    'publicationId' => $issue['pid'],
                                                    'searchname' => $userData['email']));
    
            // Set how the user wants the mail sent - html or text
            $userData['htmlmail'] = $subscription['htmlmail'];

            // Set email address for html or text email
            if ($subscription['htmlmail']) {
                // Mail the html issue to the subscription base
                $result = xarModAPIFunc('newsletter',
                                        'admin',
                                        'mailissue',
                                        array('publication'  => $publication,
                                              'issue'        => $issue,
                                              'recipients'   => array($userData['email'] => $userData['name']),
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
                                              'recipients'   => array($userData['email'] => $userData['name']),
                                              'issueText'    => $issueText,
                                              'issueHTML'    => $issueHTML,
                                              'type'         => 'text'));
            }

            // If mail failed, set mailerror to true
            if (!$result) {
                $userData['mailerror'] = true;
                $issueCounts['errors']++;
            }

            // Update issue counts
            $issueCounts['total']++;


            // Add the subscription
            $emailResultArray['subscriptions'][] = $userData;
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
 * @return array
 * @returns $emailResultArray
 */
function newsletter__bulk_email($args)
{
    // Extract args
    extract($args);

    // Check args
    if (!isset($publication) || !isset($issue) || !isset($issueText) || !isset($issueHTML)) {
        return;
    }

    // Initialize arrays
    $userData = array();
    $recipients = array();
    $htmlrecipients = array();
    $textrecipients = array();

    // Initialize issue count totals and errors
    $issueCounts = array('total' => 0,
                         'errors' => 0);

    // Get subscription list - don't send uid
    $subscriptions = xarModAPIFunc('newsletter',
                                   'user',
                                   'get',
                                    array('id' => 0, // doesn't matter
                                          'pid' => $issue['pid'],
                                          'phase' => 'subscription'));

    if (!empty($subscriptions)) {
        foreach ($subscriptions as $subscription) {

            // Get the user's email address from roles
            $userData = xarModAPIFunc('roles',
                                      'user',
                                      'get',
                                      array('uid' => $subscription['uid']));

            if (!isset($userData)) {
                $userData['mailerror'] = true;
                $userData['nameurl'] = '';
                $userData['emailurl'] = '';
                $issueCounts['errors']++;
                // show error for this user
            } else {
                $userData['mailerror'] = false;

                // Set email address for html or text email
                if ($subscription['htmlmail']) {
                    $htmlrecipients[$userData['email']] = $userData['name'];
                } else {
                    $textrecipients[$userData['email']] = $userData['name'];
                }

                // Set link to search user by name
                $userData['nameurl'] = xarModURL('newsletter',
                                                 'admin',
                                                 'viewsubscription',
                                                 array('search' => "publication",
                                                       'publicationId' => $issue['pid'],
                                                       'searchname' => $userData['name']));
    
                // Set link to search user by email
                $userData['emailurl'] = xarModURL('newsletter',
                                                  'admin',
                                                  'viewsubscription',
                                                  array('search' => "publication",
                                                        'publicationId' => $issue['pid'],
                                                        'searchname' => $userData['email']));
    
                // Set how the user wants the mail sent - html or text
                $userData['htmlmail'] = $subscription['htmlmail'];

                // Update issue counts
                $issueCounts['total']++;
            }

            // Add the subscription
            $emailResultArray['subscriptions'][] = $userData;
        }
    }

    // Get alternative subscription list - don't send uid
    $altsubscriptions = xarModAPIFunc('newsletter',
                                      'user',
                                      'get',
                                       array('id' => 0, // doesn't matter
                                             'pid' => $issue['pid'],
                                             'phase' => 'altsubscription'));

    if (!empty($altsubscriptions)) {
        foreach ($altsubscriptions as $subscription) {

            // Set user data fields
            $userData['name'] = $subscription['name'];
            $userData['email'] = $subscription['email'];
            $userData['mailerror'] = false;

            // Set email address for html or text email
            if ($subscription['htmlmail']) {
                $htmlrecipients[$userData['email']] = $userData['name'];
            } else {
                $textrecipients[$userData['email']] = $userData['name'];
            }

            // Set link to search user by name
            $userData['nameurl'] = xarModURL('newsletter',
                                             'admin',
                                             'viewsubscription',
                                             array('search' => "publication",
                                                   'publicationId' => $issue['pid'],
                                                   'searchname' => $userData['name']));
    
            // Set link to search user by email
            $userData['emailurl'] = xarModURL('newsletter',
                                              'admin',
                                              'viewsubscription',
                                              array('search' => "publication",
                                                    'publicationId' => $issue['pid'],
                                                    'searchname' => $userData['email']));
    
            // Update issue counts
            $issueCounts['total']++;

            // Set how the user wants the mail sent - html or text
            $userData['htmlmail'] = $subscription['htmlmail'];

            // Add the subscription
            $emailResultArray['subscriptions'][] = $userData;
        }
    }

    // Set recipient to owner email and publication name
    $recipients[$publication['ownerEmail']] = $publication['title'] . ' Newsletter';

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
