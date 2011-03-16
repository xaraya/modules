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
 * @param int	$to the uid of the recipient 
 * @return true
 */

function messages_userapi_sendmail($args) {

	$strip_tags = true;
	$mailusetpls = false;

	extract($args);

	$msgurl = xarModURL('messages','user','display',array('id' => $id)); 
	$from = xarUserGetVar('name');
	$msgdata['info'] = xarUserGetVar('email',$to);
	$msgdata['name'] = xarUserGetVar('name',$to); 

	$data['msgurl'] = $msgurl;
	$data['id'] = $id; // message id
	$data['from_id'] = xarUserGetVar('id');
	$data['from_name'] = $from;
	$data['to_id'] = $to;
	$data['to_name'] = $msgdata['name'];
	$data['to_email'] = $msgdata['info'];
	$subject = xarTplModule('messages','user','email-subject', $data);
	$body = xarTplModule('messages','user','email-body', $data);
	if ($strip_tags) {
		$subject = strip_tags($subject);
		$body = strip_tags($body);
	} 
	$msgdata['subject'] = $subject;
	$msgdata['message'] = $body;
	$msgdata['usetemplates'] = $mailusetpls;

	$sendmail = xarMod::apiFunc('mail','admin','sendmail', $msgdata);
	return true;

}
?>
