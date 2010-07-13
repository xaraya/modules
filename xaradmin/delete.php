<?php
/**
 * Delete an item
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Downloads Module
 * @link http://www.xaraya.com/index.php/release/eid/1152
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * delete an item
 * @param 'itemid' the id of the item to be deleted
 * @param 'confirm' confirm that this item can be deleted
 */
function downloads_admin_delete()
{

    if (!xarVarFetch('itemid' ,     'int',    $itemid, '' ,          XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return;

    // Show an error when the itemid is still not set
    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'delete', 'downloads');
        throw new Exception($msg);
    }

	$data['itemid'] = $itemid;

    sys::import('modules.dynamicdata.class.objects.master');

	// Get the object name
	$object = DataObjectMaster::getObject(array('name' => 'downloads'));
	$object->getItem(array('itemid' => $itemid));
	
	//$instance = $itemid.':'.'downloads'.':'.xarUserGetVar('id');
    if (!xarSecurityCheck('DeleteDownloads',1)) {
		return;
	}

    // Get the object we'll be working with
    $object = DataObjectMaster::getObject(array('name' => 'downloads'));
    $object->getItem(array('itemid' => $itemid));

	$data['object'] = $object;
    
    if ($data['confirm']) {

        // Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        

		// delete the item in the object for this downloads type
		$object->deleteItem(array('itemid' => $itemid));
        
        // Jump to the next page
        xarResponse::redirect(xarModURL('downloads','admin','view'));
        return true;
    }
    return $data;
}

?>