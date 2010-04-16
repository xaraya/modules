<?php
  /**
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage shop Module
 * @link http://www.xaraya.com/index.php/release/eid/1031
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *  Modify a customer
 */
function shop_admin_modifycustomer()
{
    if(!xarVarFetch('itemid',       'id',    $data['itemid'],   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return; 

    $objectname = 'shop_customers';
    $data['objectname'] = $objectname;

    // Check if we still have no id of the item to modify.
    if (empty($data['itemid'])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'modify', 'shop');
        throw new Exception($msg);
    }

    if (!xarSecurityCheck('AdminShop',1,'Item',$data['itemid'])) return;

    sys::import('modules.dynamicdata.class.objects.master');

    $object = DataObjectMaster::getObject(array('name' => $objectname));
    $data['object'] = $object;
    $data['label'] = $object->label;
    $object->getItem(array('itemid' => $data['itemid']));   
    
    $values = $object->getFieldValues();
    foreach ($values as $name => $value) {
        $data[$name] = xarVarPrepForDisplay($value);
    }

    $rolesobject = DataObjectMaster::getObject(array('name' => 'roles_users'));
    $rolesobject->getItem(array('itemid' => $data['itemid']));

    if ($data['confirm']) {

        // Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        

        // Get the data from the form
        $isvalid = $object->checkInput();

        if (!$isvalid) {
            // Bad data: redisplay the form with the data we picked up and with error messages
            return xarTplModule('shop','admin','modifycustomer', $data);        
        } elseif (isset($data['preview'])) {
            // Show a preview, same thing as the above essentially
            return xarTplModule('shop','admin','modifycustomer', $data);        
        } else {

            $first_name = $object->properties['first_name']->getValue();
            $last_name = $object->properties['last_name']->getValue();

            $rolesobject->properties['name']->setValue($first_name . ' ' . $last_name);
            $rolesobject->updateItem();
            
            $object->updateItem();

            // Jump to the next page 
            xarResponse::redirect(xarModURL('shop','admin','modifycustomer',array('itemid' => $data['itemid'])));
            return $data;
        }
    } else {
        // Get that specific item of the object
        $object->getItem(array('itemid' => $data['itemid']));
    }
 
    // Return the template variables defined in this function
    return $data;
}

?>