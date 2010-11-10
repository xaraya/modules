<?php
/**
 * Modify an item
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
 * @link http://xaraya.com/index.php/release/14.html
 */
/**
 * modify an item
 *
 * This function shows a form in which the user can modify the item
 *
 * @param id itemid The id of the dynamic data item to modify
 */
function comments_admin_modify()
{
    if(!xarVarFetch('id',       'id',    $id,   NULL, XARVAR_DONT_SET)) {return;}
	if(!xarVarFetch('objecturl',       'str',    $objecturl,   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('view',    'str',   $data['view'], '',       XARVAR_NOT_REQUIRED)) return;
 
    // Check if we still have no id of the item to modify.
    if (empty($id)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'modify', 'comments');
        throw new Exception($msg);
    }

	$data['id'] = $id;

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');

	// Get the object name
	$commentsobject = DataObjectMaster::getObject(array('name' => 'comments'));
	$check = $commentsobject->getItem(array('itemid' => $id));
	if (empty($check)) { 
		$msg = 'There is no comment with an itemid of ' . $id;
		return xarTplModule('base','message','notfound',array('msg' => $msg));
	}

	if (!xarSecurityCheck('EditComments',0)) {
		return;
	}
	
	$data['pathval'] = '';

    // Get the object we'll be working with
    $object = DataObjectMaster::getObject(array('name' => 'comments'));
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
            return xarTplModule('comments','admin','modify', $data);        
        } elseif (isset($data['preview'])) {
            // Show a preview, same thing as the above essentially
            return xarTplModule('comments','admin','modify', $data);        
        } else {
            // Good data: update the item 

            $data['object']->updateItem(array('itemid' => $id));

			$values = $data['object']->getFieldValues();

			if (!empty($data['view'])) {
				xarResponse::redirect($values['objecturl']);
			} else {
				xarResponse::redirect(xarModURL('comments','admin','modify', array('id'=>$id)));
			}
            return true;
        }
    } else {
        $data['object']->getItem(array('itemid' => $id));
    }

    return $data;
}

?>