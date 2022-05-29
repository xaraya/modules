<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
/**
 * Send an email to a message recipient
 * @author Ryan Walker (ryan@webcommunicate.net)
 * @param int	$id the id of the message
 * @param int	$to_id the uid of the recipient
 * @return true
 */

function messages_userapi_sendmail($args)
{
    extract($args);

    $msgurl = xarController::URL('messages', 'user', 'display', ['id' => $id]);
    $from_name = xarUser::getVar('name');
    $msgdata['info'] = xarUser::getVar('email', $to_id);
    $msgdata['name'] = xarUser::getVar('name', $to_id);

    $data['msgurl'] = $msgurl;
    $data['id'] = $id; // message id
    $data['from_id'] = xarUser::getVar('id');
    $data['from_name'] = $from_name;
    $data['to_id'] = $to_id;
    $data['to_name'] = $msgdata['name'];
    $data['to_email'] = $msgdata['info'];
    $subject = xarTpl::module('messages', 'user', 'email-subject', $data);
    $body = xarTpl::module('messages', 'user', 'email-body', $data);
    $msgdata['subject'] = $subject;
    $msgdata['message']  = $body;

    $sendmail = xarMod::apiFunc('mail', 'admin', 'sendmail', $msgdata);
    return true;
}
