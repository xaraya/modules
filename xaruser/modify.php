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

function messages_user_modify() {

	if (!xarVarFetch('send',    'str',   $send, '',       XARVAR_NOT_REQUIRED)) return; 
	if (!xarVarFetch('draft',    'str',   $draft, '',       XARVAR_NOT_REQUIRED)) return; 
	if(!xarVarFetch('id',       'id',    $id,   NULL, XARVAR_NOT_REQUIRED)) {return;}

	$send = (!empty($send)) ? true : false;
	$draft = (!empty($draft)) ? true : false;

    // Check if we still have no id of the item to modify.
    if (empty($id)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'id', 'user', 'modify', 'messages');
        throw new Exception($msg);
    }

	$data['id'] = $id;

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');

	// Get the object name
	$object = DataObjectMaster::getObject(array('name' => 'messages_messages'));
	$object->getItem(array('itemid' => $id)); 
	$replyto = $object->properties['replyto']->value;

	$data['reply'] = ($replyto > 0) ? true : false;

	$data['object'] = $object;

	if (!xarSecurityCheck('Editmessages',0)) {
		return;
	}

	$data['label'] = $object->label;

    if ($send || $draft) {

        // Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        

        // Get the data from the form
        $isvalid = $object->checkInput();

        if (!$isvalid) { 
            return xarTplModule('messages','user','modify', $data);          
        } else {
            // Good data: update the item

			if ($send) {
				$object->properties['author_status']->setValue(MESSAGES_STATUS_UNREAD);
			}

            $object->updateItem(array('itemid' => $id));

			xarResponse::redirect(xarModURL('messages','user','modify', array('id'=>$id))); 

            return true;
        }
    } 

    return $data;
}

?>