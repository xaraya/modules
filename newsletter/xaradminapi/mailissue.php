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
 * Mail the Newsletter issue
 *
 * @public
 * @param $args an array of arguments (if called by other modules)
 * @param $args['publication'] publication of the issue to mail
 * @param $args['issue'] issue to mail 
 * @param $args['subscription'] subscription that is getting issue 
 * @param $args['issueText'] text version of the issue 
 * @param $args['issueHTML'] HTML version of the issue 
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

    if (!isset($publication))
        $invalid[] = 'publication';
    if (!isset($issue))
        $invalid[] = 'issue';
    if (!isset($subscription))
        $invalid[] = 'subscription';
    if (!isset($issueText))
        $invalid[] = 'issueText';
    if (!isset($issueHTML))
        $invalid[] = 'issueHTML';

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'mailissue', 'Newsletter');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Set info - email address we are sending to
    $info = $subscription['email'];

    if (empty($info))
        return; // throw back

    // Set name of email recipient
    $name = $subscription['name'];

    // Set subject of email to publication and issue title
    $subject = $publication['title'] . " : " . $issue['title'];

    // Set args for sendmail
    $mailargs =  array('info'       => $info,
                      'name'        => $name,
                      'subject'     => $subject,
                      'message'     => $issueText,
                      'htmlmessage' => $issueHTML,
                      'from'        => $publication['ownerEmail'],
                      'fromname'    => $publication['ownerName']);

    // Check if this user requested a text only message
    if ($subscription['htmlmail']) {
        // Send the mail using the mail module
        $result = xarModAPIFunc('mail',
                                'admin',
                                'sendhtmlmail',
                                $mailargs);
    } else {
        // Send the mail using the mail module
        $result = xarModAPIFunc('mail',
                                'admin',
                                'sendmail',
                                $mailargs);
    }

    if (!$result)
        return; // throw back

    return true;
}

?>
