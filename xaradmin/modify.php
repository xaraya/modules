<?php
/**
 * Modify an item
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage downloads
 * @link http://www.xaraya.com/index.php/release/19741.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * modify an item
 *
 * This function shows a form in which the user can modify the item
 *
 * @param id itemid The id of the dynamic data item to modify
 */
function downloads_admin_modify()
{
    if(!xarVarFetch('itemid',       'id',    $itemid,   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return;
	if(!xarVarFetch('saveedit',     'isset', $saveedit,   NULL, XARVAR_DONT_SET)) {return;}

    // Check if we still have no id of the item to modify.
    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'modify', 'downloads');
        throw new Exception($msg);
    }

	$data['itemid'] = $itemid;

	sys::import('modules.dynamicdata.class.properties.master');    sys::import('modules.dynamicdata.class.objects.master');

    // Get the object we'll be working with
    $object = DataObjectMaster::getObject(array('name' => 'downloads'));
	$data['object'] = $object; // save for later

	$properties = $object->getProperties();
	$data['locpropid'] = $properties['location']->id;
	
	$object->getItem(array('itemid' => $itemid));
	$data['filename'] = $object->properties['filename']->value;
	if (strstr($data['filename'],'.')) {
		$parts = explode('.',$data['filename']);
		$ext = end($parts);
	} else {
		$ext = '';
	}
	$instance = $itemid.':'.$ext.':'.xarUserGetVar('id');
	if (!xarSecurityCheck('EditDownloads',0,'Record',$instance)) {
		return;
	}
	$data['location'] = $object->properties['location']->value;

	$object->properties['filename']->initialization_basedirectory = $data['location'];

	$data['label'] = $object->label;

    if ($data['confirm']) {

        // Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }       
 
        // Get the data from the form
        $isvalid = $object->checkInput();

        if (!$isvalid) {
            return xarTplModule('downloads','admin','modify', $data);              
        } else {
			$filename = $object->properties['filename']->getValue();
			if (strstr($filename,'.')) {
				$parts = explode('.',$filename);
				$ext = end($parts);
			} else {
				$ext = '';
			}

			$instance = $itemid.':'.$ext.':'.xarUserGetVar('id');
			if (!xarSecurityCheck('EditDownloads',0,'Record',$instance)) {
				return;
			}

			$object->properties['filetype']->setValue($ext);
            $object->updateItem(array('itemid' => $itemid));

			if (isset($saveedit)) {
				xarResponse::redirect(xarModURL('downloads', 'admin', 'modify',
									  array('itemid' => $itemid)));
				return true;
			}

			xarResponse::redirect(xarModURL('downloads','admin','view'));
            return true;
        }
    } else { 
        $object->getItem(array('itemid' => $itemid));
    }

    return xarTplModule('downloads','admin','modify', $data);  
}

?>