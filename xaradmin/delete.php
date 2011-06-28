<?php
/**
 * Delete an item
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage amazonfps
 * @link http://xaraya.com/index.php/release/eid/1169
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * delete an item
 * @param 'itemid' the id of the item to be deleted
 * @param 'confirm' confirm that this item can be deleted
 */
function amazonfps_admin_delete()
{    
    
    if (!xarSecurityCheck('DeleteAmazonFPS',1)) {
        return;
    }

    if (!xarVarFetch('itemid' ,     'int',    $itemid, '' ,          XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return;

    // Show an error when the itemid is still not set
    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'delete', 'contributions');
        throw new Exception($msg);
    }

    $data['itemid'] = $itemid;

    sys::import('modules.dynamicdata.class.objects.master');

    $name = 'amazonfps_payments';
    $data['name'] = $name;

    // Get the object name
    $object = DataObjectMaster::getObject(array('name' => $name));
    $object->getItem(array('itemid' => $itemid));

    $data['object'] = $object;
    
    if ($data['confirm']) {

        // Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        

        $object->deleteItem(array('itemid' => $itemid));
        
        // Jump to the next page
        xarResponse::redirect(xarModURL('amazonfps','admin','view'));
        return true;
    }
    return $data;
}

?>