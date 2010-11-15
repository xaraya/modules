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

function messages_user_modify()
{
    if(!xarVarFetch('id',       'id',    $id,   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return; 

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

	$draft = $object->properties['author_status']->value;

	if ($draft != 0) { // no reason to modify something that isn't a draft
		return;
	}

	if (!xarSecurityCheck('Editmessages',0)) {
		return;
	}
	
	$data['action'] = 'draft';

	$data['object'] = $object; // save for later

	$data['label'] = $object->label;
   
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

    if ($data['confirm']) {

        // Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        

        // Get the data from the form
        $isvalid = $data['object']->checkInput();

        if (!$isvalid) { 
            return xarTplModule('messages','user','modify', $data);          
        } else {
            // Good data: update the item

            $data['object']->updateItem(array('itemid' => $id));

			xarResponse::redirect(xarModURL('messages','user','modify', array('id'=>$id))); 

            return true;
        }
    } else {
        $data['object']->getItem(array('itemid' => $id));
    }


    return $data;
}

?>