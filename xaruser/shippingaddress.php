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
        xarController::redirect(xarModURL('shop','user','main'));
        return true;
    }

    if(!xarVarFetch('proceed', 'str', $proceed, NULL, XARVAR_NOT_REQUIRED)) {return;} 
    if(!xarVarFetch('shipto', 'str', $shipto, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('remove', 'str', $remove, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('next', 'str', $data['next'], NULL, XARVAR_NOT_REQUIRED)) {return;}
	
	sys::import('modules.dynamicdata.class.objects.master');

	// Get the saved shipping addresses, if any exist
    $mylist = DataObjectMaster::getObjectList(array('name' => 'shop_shippingaddresses'));
    $filters = array(
                     'status'    => DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
                    'where' => 'customer eq ' . xarUserGetVar('id'),
                    );
    $shippingaddresses = $mylist->getItems($filters);
    if (count($shippingaddresses) > 0) {
        $data['shippingaddresses'] = $shippingaddresses;
    }
    
    $data['shippingobject'] = DataObjectMaster::getObject(array('name' => 'shop_shippingaddresses'));
    $data['shippingobject']->properties['name']->display_show_salutation = false;
    $data['shippingobject']->properties['name']->display_show_middlename = false;
    $data['shippingobject']->properties['address']->display_rows = 2;
    $data['shippingobject']->properties['address']->display_show_country = false;
    $data['properties'] = $data['shippingobject']->properties;

    if ($shipto) {
        xarSession::setVar('shippingaddress',$shipto);
        if(isset($data['next']) && !empty($data['next'])) {
            $func = $data['next'];
        } else {
            $func = 'paymentmethod';
        }
            xarController::redirect(xarModURL('shop','user',$func));
            return true;
    }

    if ($remove) {
        if ($remove == xarSession::getVar('shippingaddress')) {
            xarSession::delVar('shippingaddress');
        }
        $data['shippingobject']->getItem(array('itemid' => $remove));
        $data['shippingobject']->deleteItem();
        xarController::redirect(xarModURL('shop','user','shippingaddress'));
        return true;
    }

    if ($proceed) {
        $isvalid = $data['shippingobject']->checkInput();
        if (!$isvalid) {
            return xarTplModule('shop','user','shippingaddress',$data);
        }

        // Save the customer data
        $custobject = DataObjectMaster::getObject(array('name' => 'shop_customers'));
        $custobject->getItem(array('itemid' => xarUserGetVar('id')));
        $name = $data['shippingobject']->properties['name']->value;
        $custobject->properties['name']->setValue($name);
        $custobject->updateItem();

        // Save the shipping address
        $itemid= $data['shippingobject']->createItem();
        xarSession::setVar('shippingaddress',$itemid);

        // update the name field in roles to use first and last name instead of email
        $rolesobject = xarCurrentRole();
        $rolesobject->properties['name']->value = $name;
        $rolesobject->updateItem();

        xarController::redirect(xarModURL('shop','user','paymentmethod'));
        return true;

        xarSession::setVar('errors',$errors);
    }

    return $data;

}

?>