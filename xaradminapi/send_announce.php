<?php
/**
* Send a one-time announcement to a subscriber list
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
 * Send a one-time message to a subscriber list
 *
 * @param  $ 'id' the id of the item to be publishd
 * @param  $ 'confirm' confirm that this item can be publishd
 */
function ebulletin_adminapi_send_announce($args)
{
    extract($args);

    if (!xarVarFetch('pid', 'int:1:', $pid)) return;
    if (!xarVarFetch('subject', 'str:1:', $subject, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('html_body', 'str:1:', $html_body, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('html_txt', 'str:1:', $html_txt, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    // get publication
    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $pid));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

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

    // subject
    $mail['subject'] = $subject;

    // body
    $mail['message'] = $body_txt;
    if ($pub['html']) $mail['htmlmessage'] = $body_html;

    // set other mail params
    $mail['usetemplates'] = false;

    // get list of subscribers
    $subscribers = xarModAPIFunc('ebulletin', 'user', 'getsubscriberemails',
        array('pid' => $pid)
    );
    if (empty($subscribers) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // turn off certain mail params to keep the mail module from interfering
    $old_htmlheadfoot = xarModGetVar('mail', 'htmluseheadfoot');
    $old_txtheadfoot = xarModGetVar('mail', 'txtuseheadfoot');
    if (!empty($old_htmlheadfoot)) xarModSetVar('mail', 'htmluseheadfoot', 0);
    if (!empty($old_txtheadfoot)) xarModSetVar('mail', 'txtuseheadfoot', 0);

    // send message for each subscriber
    foreach ($subscribers as $to => $toname) {
        // set the recipient
        $mail['info'] = $to;
        if (!empty($toname)) $mail['name'] = $toname;

        // send mail
        if (empty($mail['htmlmessage'])) {
            if (!xarModAPIFunc('mail', 'admin', 'sendmail', $mail)) return;
        } else {
            if (!xarModAPIFunc('mail', 'admin', 'sendhtmlmail', $mail)) return;
        }
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