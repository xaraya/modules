<?php
/**
 * Delete an item
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
 * delete an item
 * @param 'itemid' the id of the item to be deleted
 * @param 'confirm' confirm that this item can be deleted
 */
function content_admin_deletecontenttype()
{

    if (!xarVarFetch('ctype',       'str:1',  $ctype,    'content',     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemid' ,     'int',    $itemid , '' ,          XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',    'bool',   $confirm, false,       XARVAR_NOT_REQUIRED)) return;

	$data['content_type'] = $ctype;

    // Show an error when the itemid is still not set
    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'delete', 'content');
        throw new Exception($msg);
    }

	$data['itemid'] = $itemid;

    if (!xarSecurityCheck('DeleteContentTypes',1,'ContentType',$ctype)) return;

    sys::import('modules.dynamicdata.class.objects.master');
	sys::import('modules.dynamicdata.class.properties.master');

	// Get the object label for the template
	$object = DataObjectMaster::getObject(array('name' => $ctype));
	if($object) {
		$data['label'] = $object->label;
	} else {
		$data['label'] = '';
	}
	
    $ctobject = DataObjectMaster::getObject(array('name' => 'content_types'));
	$ctobject->getItem(array('itemid' => $itemid));  
	
	// Warn the user about how many content items we are deleting
	$items = DataObjectMaster::getObjectList(array('objectid' => $itemid));
	$data['count'] = $items->countItems();
    
    if ($confirm) {

        // Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        

		// Let's delete the object for this content type
		$object = DataObjectMaster::getObject(array('name' => 'objects'));
		$object->deleteItem(array('itemid' => $itemid));

		// Now delete the content type (e.g. the item in the 'content_types' DataObject)
		$ctobject->deleteItem(array('itemid' => $itemid));

		// Delete all content items for this content type
		$contentobject = DataObjectMaster::getObject(array('name' => 'content'));
		$list = DataObjectMaster::getObjectList(array('name' => 'content'));
		$filters = array(
			'where' => 'content_type eq \'' . $ctype .'\''
		);
		$items = $list->getItems($filters);
		foreach ($items as $item) {
			$itemid = $item['itemid'];
			$contentobject->deleteItem(array('itemid' => $itemid));
		}
        
        // Jump to the next page
        xarResponse::redirect(xarModURL('content','admin','viewcontenttypes'));
        return true;
    }
    return $data;
}

?>