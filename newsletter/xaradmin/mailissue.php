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

    // Get the publication for display
    $publication = xarModAPIFunc('newsletter',
                                 'user',
                                 'getpublicationfordisplay',
                                 array('publicationId' => $issue['pid']));

    if (!$publication)
        return; // throw back
    
    // Get the admin menu
    $menu = xarModAPIFunc('newsletter', 'admin', 'menu');

    // create template array
    $templateVarArray = array(
        'menu' => $menu,
        'publication' => $publication,
        'issue' => $issue);

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

    // If there is not text file template, then assume 
    // that we're using the HTML template
    if (!file_exists($sourceFileNameText)) {
        // The text only issue will need some beautification 
        // before it can be emailed
        // First, strip out the html tags
        $issueText = strip_tags($issueHTML);
        // Next, strip out &nbsp, etc
        $issueText = html_entity_decode($issueText);
        // Next, strip out leading and trailing white spaces from the file
        $textArray = explode("\n", $issueText);
        for ($idx = 0; $idx < count($textArray); $idx++)
            $textArray[$idx] = trim($textArray[$idx]);
        $issueText = implode("\n", $textArray);
    } else {
        // Call blocklayout with the template to parse it and generate HTML
        $issueText = xarTplFile($sourceFileNameText, $templateVarArray);
        // Just in case...
        $issueText = strip_tags($issueText);
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
                $issueCounts['errors']++;
                // show error for this user
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

                // Mail the issue to the user
                $result = xarModAPIFunc('newsletter',
                                        'admin',
                                        'mailissue',
                                        array('publication' => $publication,
                                              'issue' => $issue,
                                              'subscription' => $userData,
                                              'issueText' => $issueText,
                                              'issueHTML' => $issueHTML));

                // Update issue counts
                $issueCounts['total']++;
 
                // If mail failed, set mailerror to true
                if (!$result) {
                    $userData['mailerror'] = true;
                    $issueCounts['errors']++;
                }
            }

            // Add the subscription
            $templateVarArray['subscriptions'][] = $userData;
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

            // Mail the issue to the user
            $result = xarModAPIFunc('newsletter',
                                    'admin',
                                    'mailissue',
                                    array('publication' => $publication,
                                          'issue' => $issue,
                                          'subscription' => $userData,
                                          'issueText' => $issueText,
                                          'issueHTML' => $issueHTML));

            // Update issue counts
            $issueCounts['total']++;

            // If mail failed, set mailerror to true
            if (!$result) {
                $userData['mailerror'] = true;
                $issueCounts['errors']++;
            }

            // Add the subscription
            $templateVarArray['subscriptions'][] = $userData;
        }
    }

    $templateVarArray['issueCounts'] = $issueCounts;

    // Return the template variables defined in this function
    return xarTplModule('newsletter','admin','mailissue',$templateVarArray);
}


?>
