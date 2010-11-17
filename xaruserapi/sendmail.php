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
 * @param object	$object the messages_messages object
 * @return true
 */

function messages_userapi_sendmail($args) {

	extract($args);

	$msgurl = xarModURL('messages','user','display',array('id' => $id));
	$to = $object->properties['to']->value;
	$from = xarUserGetVar('name');
	$msgdata['info'] = xarUserGetVar('email',$to);
	$msgdata['name'] = xarUserGetVar('name',$to); 
	
	/* See if we have templates at var/messaging/messages.
	We're looking for newmessage-subject.xt and newmessage-message.xt.
	Note: In getmessagestrings, we must find both the subject and message template or we can't use either. */
	try {
		$tpl_args['template'] = 'newmessage';
		$tpl_data = xarMod::apiFunc('mail','admin','getmessagestrings',$tpl_args);
		} catch (Exception $e) {
		}
	if (isset($tpl_data)) {
		$tpl_data['to_name'] = xarUserGetVar('name',$to);
		$tpl_data['from_name'] = $from;
		$tpl_data['htmlmessage'] = ''; //avoid error in mail_admin_replace
		$replace = xarMod::apiFunc('mail','admin','replace', $tpl_data);
		$msgdata = array_merge($msgdata, $replace);
	} else {
		$msgdata['subject'] = $msgdata['name'] . ': New message from ' . $from;
		$msgdata['message'] = 'You have a new message from ' . $from . '.';
		$msgdata['message'] .= 'View your message: ' . $msgurl;
	}   

	$sendmail = xarMod::apiFunc('mail','admin','sendmail', $msgdata);
	return true;

}
?>
