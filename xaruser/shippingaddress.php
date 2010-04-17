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
 *  New account info (ship address)
 */
function shop_user_shippingaddress() 
{

    // Redirects at the start of the user functions are just a way to make sure someone isn't where they don't need to be
    $shop = xarSession::getVar('shop');
    if (!xarUserIsLoggedIn() || empty($shop)) {
        xarResponse::redirect(xarModURL('shop','user','main'));
        return;
    }

    if(!xarVarFetch('proceed', 'str', $proceed, NULL, XARVAR_NOT_REQUIRED)) {return;} 
    if(!xarVarFetch('shipto', 'str', $shipto, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('skipto', 'str', $data['skipto'], NULL, XARVAR_NOT_REQUIRED)) {return;}

    sys::import('modules.dynamicdata.class.objects.master');
    sys::import('modules.dynamicdata.class.properties.master');

    // Get the saved payment methods, if any exist
    $mylist = DataObjectMaster::getObjectList(array('name' => 'shop_shippingaddresses'));
    $filters = array(
                     'status'    => DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
                    'where' => 'customer eq ' . xarUserGetVar('id'),
                    );
    $shippingaddresses = $mylist->getItems($filters);
    if (count($shippingaddresses) > 0) {
        $data['shippingaddresses'] = $shippingaddresses;
    }

    $myfields = array('first_name', 'last_name', 'street_addr', 'city_addr', 'state_addr', 'postal_code');
    $data['myfields'] = $myfields;

    $shippingobject = DataObjectMaster::getObject(array('name' => 'shop_shippingaddresses'));
    $properties = $shippingobject->properties;
    $data['properties'] = $properties;

    if (isset($shipto)) {
        xarSession::setVar('shippingaddress',$shipto);
        if(isset($data['skipto'])) {
            $func = $data['skipto'];
        } else {
            $func = 'paymentmethod';
        }
            xarResponse::redirect(xarModURL('shop','user',$func));
    }

    if ($proceed) {
    
        $errors = xarSession::getVar('errors');
        foreach ($myfields as $field) {
            $isvalid = $shippingobject->properties[$field]->checkInput();

            if (!$isvalid) {
                $errors[$field] = true;
            } else {
                unset($errors[$field]); // In case we previously submitted invalid input in this field
            }

            ${$field} = $shippingobject->properties[$field]->getValue();
        }

        if (!empty($errors)) {
            xarResponse::redirect(xarModURL('shop','user','shippingaddress').'#errors');
            return;
        }

        // Save the customer data
        $custobject = DataObjectMaster::getObject(array('name' => 'shop_customers'));
        $custobject->getItem(array('itemid' => xarUserGetVar('id')));
        $custobject->properties['first_name']->setValue($first_name);
        $custobject->properties['last_name']->setValue($last_name);
        $custobject->updateItem();

        // Save the shipping address
        $shippingobject->properties['customer']->setValue(xarUserGetVar('id'));
        $shippingobject->properties['first_name']->setValue($first_name);
        $shippingobject->properties['last_name']->setValue($last_name);
        $shippingobject->properties['street_addr']->setValue($street_addr);
        $shippingobject->properties['city_addr']->setValue($city_addr);
        $shippingobject->properties['postal_code']->setValue($postal_code);
        $shippingobject->properties['state_addr']->setValue($state_addr);
        xarSession::setVar('shippingaddress',$shippingobject->createItem());

        // update the name field in roles to use first and last name instead of email
        $rolesobject = DataObjectMaster::getObject(array('name' => 'roles_users'));
        $rolesobject->getItem(array('itemid' => xarUserGetVar('id')));
        $rolesobject->properties['name']->setValue($first_name . ' ' . $last_name);
        $rolesobject->updateItem();

        xarResponse::redirect(xarModURL('shop','user','paymentmethod'));

        xarSession::setVar('errors',$errors);
    }

    return $data;

}

?>