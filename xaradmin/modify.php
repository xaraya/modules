<?php
/**
 * Modify an item
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Content Module
 * @link http://www.xaraya.com/index.php/release/eid/1118
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * modify an item
 *
 * This function shows a form in which the user can modify the item
 *
 * @param id itemid The id of the dynamic data item to modify
 */
function content_admin_modify()
{
    if(!xarVarFetch('itemid',       'id',    $itemid,   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('view',    'str',   $data['view'], '',       XARVAR_NOT_REQUIRED)) return;

    // Check if we still have no id of the item to modify.
    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'modify', 'content');
        throw new Exception($msg);
    }

	$data['itemid'] = $itemid;

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');

	// Get the object name
	$contentobject = DataObjectMaster::getObject(array('name' => 'content'));
	$check = $contentobject->getItem(array('itemid' => $itemid));
	$ctype = $contentobject->properties['content_type']->getValue();
	if (empty($check)) { 
		$msg = 'There is no content item with an itemid of ' . $itemid;
		return xarTplModule('base','message','notfound',array('msg' => $msg));
	}

	$instance = $itemid.':'.$ctype.':'.xarUserGetVar('id');
	if (!xarSecurityCheck('EditContent',0,'Item',$instance)) {
		return;
	}
	
	$data['ctype'] = $ctype;
	//$data['pathval'] = ''; 

    // Get the object we'll be working with
    $object = DataObjectMaster::getObject(array('name' => $ctype));
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
            return xarTplModule('content','admin','modify', $data);        
        } elseif (isset($data['preview'])) {
            // Show a preview, same thing as the above essentially
            return xarTplModule('content','admin','modify', $data);        
        } else {
            // Good data: update the item
			
			$properties = array_keys($data['object']->getProperties());

			if (in_array('item_path', $properties)) {
				$item_path = $data['object']->properties['item_path']->getValue();  
				$contentobject = DataObjectMaster::getObject(array('name' => 'content'));
				$contentobject->getItem(array('itemid' => $itemid));
				$contentobject->properties['item_path']->setValue($item_path);
				$contentobject->updateItem(array('itemid' => $itemid));
			}

			// never an empty publication_date
			
			if (in_array('publication_date', $properties)) {
				$pubdate = $data['object']->properties['publication_date']->getValue();
				if ($pubdate == -1) { 
					$previous = $data['object'];
					$previous->getItem(array('itemid' => $itemid));
					$pubdate = $previous->properties['publication_date']->value;
					$data['object']->properties['publication_date']->setValue($pubdate);
				}
			}
			if (in_array('expiration_date', $properties)) {
				$expdate = $object->properties['expiration_date']->getValue();
				if ($expdate == -1) {
					$data['object']->properties['expiration_date']->setValue(2145938400);
				}
			}
			if (in_array('date_modified', $properties)) {
				$data['object']->properties['date_modified']->setValue(time());
			}

            $data['object']->updateItem(array('itemid' => $itemid));

			/*if (isset($path_error)) {
				$args['itemid'] = $itemid;
				$args['path_error'] = $path_error;
				xarResponse::redirect(xarModURL('content','admin','modify', $args));
				return true;
			}*/

			if (!empty($data['view'])) {
				if (xarModAlias::resolve($ctype) == 'content') {
					xarResponse::redirect(xarModURL('content','user','display', array('itemid'=>$itemid, 'ctype' => $ctype)));
				} else { 
					xarResponse::redirect(xarModURL('content','user','display', array('itemid'=>$itemid)));
				}
			} else {
				xarResponse::redirect(xarModURL('content','admin','modify', array('itemid'=>$itemid)));
			}
            return true;
        }
    } else {
        $data['object']->getItem(array('itemid' => $itemid));
    }

    return $data;
}

?>