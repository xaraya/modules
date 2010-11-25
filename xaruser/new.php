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
 * @author Ryan Walker
 */

sys::import('modules.messages.xarincludes.defines');

function messages_user_new() {
 
    if (!xarSecurityCheck('AddMessages')) return;

	if (!xarVarFetch('replyto', 'int', $replyto,   0, XARVAR_NOT_REQUIRED)) return; 
	$reply = ($replyto > 0) ? true : false;
	$data['reply'] = $reply;  
	$data['replyto'] = $replyto;  

	if (!xarVarFetch('send',    'str',   $send, '',       XARVAR_NOT_REQUIRED)) return; 
	if (!xarVarFetch('draft',    'str',   $draft, '',       XARVAR_NOT_REQUIRED)) return; 
	if (!xarVarFetch('saveandedit',    'str',   $saveandedit, '',       XARVAR_NOT_REQUIRED)) return;
	if(!xarVarFetch('to',       'id',    $data['to'],   NULL, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('opt',       'bool',    $data['opt'],   false, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('id',       'id',    $id,   NULL, XARVAR_NOT_REQUIRED)) {return;}

	$send = (!empty($send)) ? true : false;
	$draft = (!empty($draft)) ? true : false;
	$saveandedit = (!empty($saveandedit)) ? true : false;

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
	$data['folder'] = 'new';

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

	if ($send || $draft || $saveandedit) {

		// Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        } 

		$isvalid = $object->checkInput();

		if ($reply) { // we really only need this if we're saving a draft
			$object->properties['replyto']->setValue($replyto); 
		} else { 
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

		$to = $object->properties['to']->value;

		// admin setting
		if ($send && xarModVars::get('messages','sendemail')) {
			// user setting
			if (xarModItemVars::get('messages', "user_sendemail", $to)) {
				xarMod::apiFunc('messages','user','sendmail',array('id' => $id, 'to' => $to));
			}
		}

		$uid = xarUserGetVar('id');

		// Send the autoreply if one is enabled by the admin and by the recipient
		if ($send && xarModVars::get('messages','allowautoreply')) { 
			$autoreply = '';
			if (xarModItemVars::get('messages', "enable_autoreply", $to)) {
				$autoreply = xarModItemVars::get('messages', "autoreply", $to);
			}
			if (!empty($autoreply)) { 
				$autoreplyobj = DataObjectMaster::getObject(array('name' => 'messages_messages'));
				$autoreplyobj->properties['author_status']->setValue(MESSAGES_STATUS_UNREAD);
				$autoreplyobj->properties['from']->setValue(xarUserGetVar('uname',$to));
				$autoreplyobj->properties['to']->setValue($uid);
				$data['from_name'] = xarUserGetVar('name',$to);
				$subject = xarTplModule('messages','user','autoreply-subject', $data); 
				$data['autoreply'] = $autoreply;
				$autoreply = xarTplModule('messages','user','autoreply-body', $data);
				// useful for eliminating html template comments
				if (xarModVars::get('messages','strip_tags')) {
					$subject = strip_tags($subject);
					$autoreply = strip_tags($autoreply);
				}
				$autoreplyobj->properties['subject']->setValue($subject);
				$autoreplyobj->properties['body']->setValue($autoreply);
				$itemid = $autoreplyobj->createItem(); 
			}
		}

		if ($saveandedit) {
			xarResponse::redirect(xarModURL('messages','user','modify', array('id' => $id)));
			return true;
		}

		if (xarModVars::get('messages', 'allowusersendredirect')) {
			$redirect = xarModItemVars::get('messages', 'user_send_redirect', $uid);
		} else {
			$redirect = xarModVars::get('messages', 'send_redirect');
		} 
		$tabs = array(1 => 'inbox', 2 => 'sent', 3 => 'drafts', 4 => 'new');
		$redirect = $tabs[$redirect];
		
		if ($redirect == 'new') {
			xarResponse::redirect(xarModURL('messages','user','new'));
		} else {
			xarResponse::redirect(xarModURL('messages','user','view', array('folder' => $redirect)));
		}
		return true;
	}

    return $data;
}

?>
