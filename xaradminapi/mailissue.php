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
 * Mail the Newsletter issue
 *
 * @public
 * @param $args an array of arguments (if called by other modules)
 * @param $args['publication'] publication of the issue to mail
 * @param $args['issue'] issue to mail 
 * @param $args['recipients'] recipients that are getting issue 
 * @param $args['bccrecipients'] bccrecipients that are getting issue 
 * @param $args['issueText'] text version of the issue 
 * @param $args['issueHTML'] HTML version of the issue 
 * @param $args['type'] version of the issue to send ('html' or 'text')
 * @author Richard Cave
 * @returns bool
 * @return true on success, false on failure
 */
function newsletter_adminapi_mailissue($args)
{
    // Extract args
    extract($args);

     // Argument check
    $invalid = array();

    if (!isset($publication)) {
        $invalid[] = 'publication';
    }
    if (!isset($issue)) {
        $invalid[] = 'issue';
    }
    if (!isset($recipients) && !isset($bccrecipients)) {
        $invalid[] = 'recipients';
    }
    if (!isset($issueText)) {
        $invalid[] = 'issueText';
    } 
    if (!isset($issueHTML)) {
        $invalid[] = 'issueHTML';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'mailissue', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Check arguments
    if (!isset($recipients)) {
        $recipients = array();
    }

    if (!isset($bccrecipients)) {
        $bccrecipients = array();
    }
    
    // Check if fromname or fromemail is set in the issue.  If not, then check
    // the publication.  If that is not set then default to publication owner.
    if (empty($issue['fromname']) || empty($issue['fromemail'])) {
        // Check if publication fromname and fromemail is set
        if (empty($publication['fromname']) || empty($publication['fromemail'])) {
            // Get publication owner information
            $fromname = $publication['ownerEmail'];
            $fromemail = $publication['ownerName'];
        } else {
            // Set to publication fromname and fromemail
            $fromname = $publication['fromname'];
            $fromemail = $publication['fromemail'];
        }
    } else {
        // Set to issue fromname and fromemail
        $fromname = $issue['fromname'];
        $fromemail = $issue['fromemail'];
    }

    // Set the subject/title of the email
    switch($publication['subject']) {
        case 1:
        // Set to publication title
        $subject = $publication['title'];
        break;

        case 2:
        // Set to publication and issue title
        $subject = $publication['title'] . " : " . $issue['title'];
        break;

        default:
        // Set to issue title
        $subject = $issue['title'];
        break;
    }

    // Set args for sendmail
    $mailargs =  array('recipients'    => $recipients,
                       'bccrecipients' => $bccrecipients,
                       'name'          => $publication['title'],
                       'subject'       => $subject,
                       'message'       => $issueText,
                       'htmlmessage'   => $issueHTML,
                       'from'          => $fromemail,
                       'fromname'      => $fromname,
                       'usetemplates'  => false);

    // Check type of email to send
    if ($type == 'html') {
        // Send the mail as HTML using the mail module
        $result = xarModAPIFunc('mail',
                                'admin',
                                'sendhtmlmail',
                                $mailargs);

        // Free any errors that occur.  We don't want mail to 
        // set an error because as this will stop processing
        // the newsletter to all recipients.
        xarErrorFree();
    } else {
        // Send the mail as text using the mail module
        $result = xarModAPIFunc('mail',
                                'admin',
                                'sendmail',
                                $mailargs);

        // Free any errors that occur.  We don't want mail to 
        // set an error because as this will stop processing
        // the newsletter to all recipients.
        xarErrorFree();
    }

    if (!$result)
        return; // throw back

    return true;
}

?>
