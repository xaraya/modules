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

sys::import('modules.messages.xarincludes.defines');

function messages_user_new() {
 
    if (!xarSecurityCheck('AddMessages')) return;

	if (!xarVarFetch('replyto', 'int', $replyto,   0, XARVAR_NOT_REQUIRED)) return; 
	$reply = ($replyto > 0) ? true : false;
	$data['reply'] = $reply; 
	$data['replyto'] = $replyto; 
	$data['to'] = '';

	if (!xarVarFetch('send',    'str',   $send, '',       XARVAR_NOT_REQUIRED)) return; 
	if (!xarVarFetch('draft',    'str',   $draft, '',       XARVAR_NOT_REQUIRED)) return; 
	if(!xarVarFetch('id',       'id',    $id,   NULL, XARVAR_NOT_REQUIRED)) {return;}

	$send = (!empty($send)) ? true : false;
	$draft = (!empty($draft)) ? true : false;

    $object = DataObjectMaster::getObject(array('name' => 'messages_messages'));
	$data['object'] = $object;

    $data['post_url']       = xarModURL('messages','user','new');

	xarTplSetPageTitle(xarML('Post Message'));
    $data['input_title']    = xarML('Compose Message');

	if ($draft) { // where to send people next
		$folder = 'drafts';
	} else {
		$folder = 'inbox';
	}

	if ($send) {
		$time = $object->properties['time']->value;
		$object->properties['author_status']->setValue(MESSAGES_STATUS_UNREAD);
	} else {
		$object->properties['author_status']->setValue(MESSAGES_STATUS_DRAFT);
	}

	if ($reply) {
		$reply = DataObjectMaster::getObject(array('name' => 'messages_messages'));
		$reply->getItem(array('itemid' => $replyto)); // get the message we're replying to
		$data['to'] = $reply->properties['from']->value; // get the user we're replying to
		$data['display'] = $reply;
		xarTplSetPageTitle(xarML('Reply to Message'));
		$data['input_title']    = xarML('Reply to Message');
	}

	if ($send || $draft) {

		$isvalid = $object->checkInput();

		if ($reply) { // we really only need this if we're saving a draft
			$object->properties['replyto']->setValue($replyto); 
		} else {
			$data['to'] = NULL;
			$object->properties['replyto']->setValue(0);
		}
		
		$object->properties['from']->setValue(xarUserGetVar('uname'));

		if(!$isvalid){     
			return xarTplModule('messages','user','new',$data);
		}

		$object->properties['recipient_status']->setValue(MESSAGES_STATUS_UNREAD);
		 
		if ($send) {
			$object->properties['author_status']->setValue(MESSAGES_STATUS_UNREAD);
		} else {
			$object->properties['author_status']->setValue(MESSAGES_STATUS_DRAFT);
		}
			
		$id = $object->createItem();

		if (xarModVars::get('messages','sendemail')) {
			$msgurl = xarModURL('messages','user','display',array('id' => $id));
			$to = $data['object']->properties['to']->value;
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
		}

		xarResponse::redirect(xarModURL('messages','user','view', array('folder' => $folder)));
		return true;
	}

    return $data;
}

?>
