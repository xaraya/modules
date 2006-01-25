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
 * publish issue
 *
 * @param  $ 'id' the id of the item to be publishd
 * @param  $ 'confirm' confirm that this item can be publishd
 */
function ebulletin_adminapi_publishissue($args)
{
    extract($args);

    // get HTTP vars
    if (!xarVarFetch('onetime', 'int:0:', $onetime, 0, XARVAR_NOT_REQUIRED)) return;

    if ($onetime) {
        if (!xarVarFetch('pid', 'int:1:', $pid)) return;
        if (!xarVarFetch('subject', 'str:1:', $subject, '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('html_body', 'str:1:', $html_body, '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('html_txt', 'str:1:', $html_txt, '', XARVAR_NOT_REQUIRED)) return;
    } else {
        if (!xarVarFetch('id', 'int:1:', $id)) return;
    }

    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('test', 'int:0:', $test, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('to', 'str:1:', $to, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('toname', 'str:0:', $toname, '', XARVAR_NOT_REQUIRED)) return;

    // validate test inputs
    if ($test) {
        if (empty($to)) {
            $to = xarModGetVar('mail', 'AdminEmail');
            if (empty($toname)) $toname = xarModGetVar('mail', 'AdminName');
        }
    }

    // get issue
    if (!$onetime) {
        // get issue
        $issue = xarModAPIFunc('ebulletin', 'user', 'getissue', array('id' => $id));
        if (empty($issue) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

        $pid = $issue['pid'];
    }

    // get publication
    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $pid));
    if (empty($pub) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('AddeBulletin', 1, 'Publication', "$pub[name]:$pub[id]")) return;

    // assemble the email
    $mail = $pub;

    // from
    $mail['from'] = $pub['from'];
    if (!empty($pub['fromname'])) $mail['fromname'] = $pub['fromname'];

    // reply-to
    if (!empty($pub['replyto'])) {
        $mail['replyto'] = $pub['replyto'];
        if (!empty($pub['replytoname'])) $mail['replytoname'] = $pub['replytoname'];
    }

    // get list of recipients
    if ($test) {

        // to
        $mail['info'] = $to;
        if (!empty($toname)) $mail['name'] = $toname;

    } else {

        $subscribers = xarModAPIFunc('ebulletin', 'user', 'getsubscriberemails',
            array('pid' => $pid)
        );
        if (empty($subscribers) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
        $subscriberscount = count($subscribers);

        // to
        $mail['info'] = $pub['to'];
        if (!empty($pub['toname'])) $mail['name'] = $pub['toname'];

        // subscribers (bcc)
        if (!empty($subscribers)) $mail['bccrecipients'] = $subscribers;
    }

    if (!$onetime) {
        $subject = $issue['subject'];
        $body_txt = $issue['body_txt'];
        $body_html = $issue['body_html'];
    }

    // subject
    $mail['subject'] = $subject;

    // body
    $mail['message'] = strip_tags($body_txt);
    $mail['htmlmessage'] = $body_html;

    // set other mail params
    $mail['usetemplates'] = false;

    // turn off certain mail params to keep the mail module from interfering
    $old_htmlheadfoot = xarModGetVar('mail', 'htmluseheadfoot');
    $old_txtheadfoot = xarModGetVar('mail', 'txtuseheadfoot');
    if (!empty($old_htmlheadfoot)) xarModSetVar('mail', 'htmluseheadfoot', 0);
    if (!empty($old_txtheadfoot)) xarModSetVar('mail', 'txtuseheadfoot', 0);

    // send mail
    if (empty($htmlmessage)) {
        if (!xarModAPIFunc('mail', 'admin', 'sendhtmlmail', $mail)) return;
    } else {
        if (!xarModAPIFunc('mail', 'admin', 'sendmail', $mail)) return;
    }

    // restore old mail params
    if (!is_null($old_htmlheadfoot)) {
        xarModSetVar('mail', 'htmluseheadfoot', $old_htmlheadfoot);
    }
    if (!is_null($old_txtheadfoot)) {
        xarModSetVar('mail', 'txtuseheadfoot', $old_txtheadfoot);
    }

    // set "published" flag if not a test
    if (!$test && !$onetime) {
        $dbconn = xarDBGetConn();
        $xartable = xarDBGetTables();
        $issuestable = $xartable['ebulletin_issues'];

        $query = "UPDATE $issuestable
                SET xar_published = ?
                WHERE xar_id = ?";
        $bindvars = array(1, $id);
        $result = $dbconn->Execute($query, $bindvars);

        if (!$result) return;
    }

    // success
    return true;
}

?>