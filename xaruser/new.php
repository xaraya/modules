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
	$data['to'] = '';

	if (!xarVarFetch('send',    'str',   $send, '',       XARVAR_NOT_REQUIRED)) return; 
	if (!xarVarFetch('draft',    'str',   $draft, '',       XARVAR_NOT_REQUIRED)) return; 
	if (!xarVarFetch('saveandedit',    'str',   $saveandedit, '',       XARVAR_NOT_REQUIRED)) return;
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

		if ($send && xarModVars::get('messages','sendemail')) {
			$to = $object->properties['to']->value;
			xarMod::apiFunc('messages','user','sendmail',array('id' => $id, 'to' => $to));
		}

		if ($saveandedit) {
			xarResponse::redirect(xarModURL('messages','user','modify', array('id' => $id)));
			return true;
		}

		xarResponse::redirect(xarModURL('messages','user','view', array('folder' => $folder)));
		return true;
	}

    return $data;
}

?>
