<?php
/**
 * Modify a content type
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
 * modify a content type
 *
 * This function shows a form in which the user can modify the item
 *
 * @param id itemid The id of the dynamic data item to modify
 */
function content_admin_modifycontenttype()
{
	if(!xarVarFetch('ctype', 'str', $ctype, NULL, XARVAR_DONT_SET)) {return;}
	if(!xarVarFetch('isalias', 'checkbox', $isalias, false, XARVAR_NOT_REQUIRED)) {return;}
    
	// Check if the user can Edit in the content module, and then specifically for this item.
    // We pass the itemid to the SecurityCheck
    if (!xarSecurityCheck('AdminContent',1)) return;

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');
	sys::import('modules.dynamicdata.class.properties.master');

	// Get the objectid
	$list = DataObjectMaster::getObjectList(array('name' => 'objects'));

	$filters = array(
		'where' => 'name eq \'' . $ctype . '\''
	);
	$items = $list->getItems($filters);
	$item = end($items);
	$objectid = $item['objectid'];

	$object = DataObjectMaster::getObject(array('name' => 'content_types'));
	$object->getItem(array('itemid'=>$objectid));

	$ctobject = DataObjectMaster::getObject(array('name' => 'objects'));
	$ctobject->getItem(array('itemid' => $objectid));
	
	$data['object'] = $ctobject;
	$data['ctype'] = $ctype;
	$data['objectid'] = $objectid;
	$data['resolvealias'] = xarModAlias::resolve($ctype);
	$data['isalias'] = (xarModAlias::resolve($ctype) == 'content'); 

    // Get the object we'll be working with
    // $object = DataObjectMaster::getObject(array('name' => $name));
	// $data['object'] = $object;
   
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

    if ($data['confirm']) {

        // Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        

        // Get the data from the form
        $isvalid = $ctobject->checkInput();

        if (!$isvalid) {
            return xarTplModule('content','admin','modify', $data);        
        } elseif (isset($data['preview'])) {
            // Show a preview, same thing as the above essentially
            return xarTplModule('content','admin','modify', $data);        
        } else {
            // Good data: update the item
 
			if ($isalias) {
				if ($data['resolvealias'] == $ctype) {
					xarModAlias::set($ctype, 'content');
				}  
			} else {
				xarModAlias::delete($ctype, 'content');
			}

			$label = $ctobject->properties['label']->getValue();
            $ctobject->updateItem(array('itemid' => $objectid));

			$object->properties['label']->setValue($label);
			$object->updateItem(array('itemid' => $objectid));
			xarResponse::redirect(xarModURL('content','admin','modifycontenttype',array('ctype'=>$ctype, 'confirm' => true)));
            return true;
        }
    } else { 
        $ctobject->getItem(array('itemid' => $objectid));
    }

    return $data;
}

?>