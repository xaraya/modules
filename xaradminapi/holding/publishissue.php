<?php
/**
* Publish an issue
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * publish an issue
 *
 * @author the eBulletin module development team
 * @param  $args ['iid'] ID of the item
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function ebulletin_adminapi_publishissue($args)
{
    extract($args);

    if (!isset($iid) || !is_numeric($iid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'pub ID', 'admin', 'publishissue', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $issue = xarModAPIFunc('ebulletin', 'user', 'getissue',
                           array('iid' => $iid));
    // Check for exceptions
    if (!isset($issue) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('pid' => $issue['pid']));

    if (!xarSecurityCheck('AdmineBulletin', 1, 'Publication', "$issue[name]:$issue[pid]")) {
        return;
    }

print_r($pub);
die();
    // Set args for sendmail
    $mailargs =  array('recipients'    => $recipients,
                       'bccrecipients' => '',
                       'name'          => $publication['title'],
                       'subject'       => $subject,
                       'message'       => $issueText,
                       'htmlmessage'   => $issueHTML,
                       'from'          => $publication['ownerEmail'],
                       'fromname'      => $publication['ownerName'],
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
                                'sendhtmlmail',
                                $mailargs);

        // Free any errors that occur.  We don't want mail to
        // set an error because as this will stop processing
        // the newsletter to all recipients.
        xarErrorFree();
    }



    $issue['module'] = 'ebulletin';
    $issue['itemid'] = $iid;
    xarModCallHooks('item', 'publish', $iid, $issue);

    // Let the calling process know that we have finished successfully
    return true;
}

?>
