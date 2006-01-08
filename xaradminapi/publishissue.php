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
    if (!xarVarFetch('id', 'int:1:', $id)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    // get issue
    $issue = xarModAPIFunc('ebulletin', 'user', 'getissue', array('id' => $id));
    if (empty($issue) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('AddeBulletin', 1, 'Publication', "$issue[pubname]:$issue[id]")) return;

    // get publication
    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $issue['pid']));
    if (empty($pub) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // get list of recipients
    $subscribers = xarModAPIFunc('ebulletin', 'user', 'getsubscriberemails',
        array('pid' => $issue['pid'])
    );
    if (empty($subscribers) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    $subscriberscount = count($subscribers);

    // assemble the email
    $mail = $pub;

    // from
    $mail['from'] = $pub['from'];
    if (!empty($pub['fromname'])) $mail['fromname'] = $pub['fromname'];

    // to
    $mail['info'] = $pub['to'];
    if (!empty($pub['toname'])) $mail['name'] = $pub['toname'];

    // reply-to
    if (!empty($pub['replyto'])) {
        $mail['replyto'] = $pub['replyto'];
        if (!empty($pub['replytoname'])) $mail['replytoname'] = $pub['replytoname'];
    }

    // subscribers (bcc)
    if (!empty($subscribers)) $mail['bccrecipients'] = $subscribers;

    // subject
    $mail['subject'] = $issue['subject'];

    // body
    $mail['message'] = strip_tags($issue['body_txt']);
    $mail['htmlmessage'] = $issue['body_html'];

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

    // set "published" flag
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $issuestable = $xartable['ebulletin_issues'];

    $query = "UPDATE $issuestable
            SET xar_published = ?
            WHERE xar_id = ?";
    $bindvars = array(1, $id);
    $result = $dbconn->Execute($query, $bindvars);

    if (!$result) return;

    // set status message and return to viewissues page
    xarSessionSetVar('statusmsg', xarML('Issue was successfully published!'));
    xarResponseRedirect(xarModURL('ebulletin', 'admin', 'viewissues'));

    // success
    return true;
}

?>