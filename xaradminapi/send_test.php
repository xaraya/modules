<?php
/**
* Send a test issue to user-specified recipient
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
 * Send a test issue to user-specified recipient
 *
 * @param  $ 'id' the id of the item to be publishd
 * @param  $ 'confirm' confirm that this item can be publishd
 */
function ebulletin_adminapi_send_test($args)
{
    extract($args);

    // get HTTP vars
    if (!xarVarFetch('id', 'int:1:', $id)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('to', 'str:1:', $to, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('toname', 'str:0:', $toname, '', XARVAR_NOT_REQUIRED)) return;

    // validate test inputs
    if (empty($to)) {
        $to = xarModGetVar('mail', 'AdminEmail');
        if (empty($toname)) $toname = xarModGetVar('mail', 'AdminName');
    }

    // get issue
    $issue = xarModAPIFunc('ebulletin', 'user', 'getissue', array('id' => $id));
    if (empty($issue) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $pid = $issue['pid'];

    // get publication
    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $pid));
    if (empty($pub) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('AddeBulletin', 1, 'Publication', "$pub[name]:$pub[id]")) return;

    // assemble the email
    $mail = array();

    // from
    $mail['from'] = $pub['from'];
    if (!empty($pub['fromname'])) $mail['fromname'] = $pub['fromname'];

    // reply-to
    if (!empty($pub['replyto'])) {
        $mail['replyto'] = $pub['replyto'];
        if (!empty($pub['replytoname'])) $mail['replytoname'] = $pub['replytoname'];
    }

    // set recipient
    $mail['info'] = $to;
    if (!empty($toname)) $mail['name'] = $toname;

    // subject
    $mail['subject'] = $issue['subject'];

    // body
    $mail['message'] = $issue['body_txt'];
    if ($pub['html']) $mail['htmlmessage'] = $issue['body_html'];

    // set other mail params
    $mail['usetemplates'] = false;

    // turn off certain mail params to keep the mail module from interfering
    $old_htmlheadfoot = xarModGetVar('mail', 'htmluseheadfoot');
    $old_txtheadfoot = xarModGetVar('mail', 'txtuseheadfoot');
    if (!empty($old_htmlheadfoot)) xarModSetVar('mail', 'htmluseheadfoot', 0);
    if (!empty($old_txtheadfoot)) xarModSetVar('mail', 'txtuseheadfoot', 0);

    // send mail
    if (empty($mail['htmlmessage'])) {
        if (!xarModAPIFunc('mail', 'admin', 'sendmail', $mail)) return;
    } else {
        if (!xarModAPIFunc('mail', 'admin', 'sendhtmlmail', $mail)) return;
    }

    // restore old mail params
    if (!is_null($old_htmlheadfoot)) {
        xarModSetVar('mail', 'htmluseheadfoot', $old_htmlheadfoot);
    }
    if (!is_null($old_txtheadfoot)) {
        xarModSetVar('mail', 'txtuseheadfoot', $old_txtheadfoot);
    }

    // success
    return true;
}

?>
