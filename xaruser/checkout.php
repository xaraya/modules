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
 *  Checkout
 */
function shop_user_checkout() {

    sys::import('modules.dynamicdata.class.objects.master');
	sys::import('modules.dynamicdata.class.properties.master');

	$total = 0;

	if (isset($_SESSION['products'])) {
		$data['products'] = $_SESSION['products'];
		$data['total'] =	$_SESSION['total'];
	}

	$transobject = DataObjectMaster::getObject(array('name' => 'shop_transactions'));
	$data['transproperties'] = $transobject->getProperties();

	$myfields = array('first_name', 'last_name', 'street_addr', 'city_addr', 'state_addr', 'postal_code', 'card_type','card_num', 'cvv2', 'exp_date');
	$data['myfields'] = $myfields; 

	$_SESSION['did_checkout'] = true; //to make sure we don't skip the checkout phase

	$rolesobject = DataObjectMaster::getObject(array('name' => 'roles_users'));
	$data['properties'] = $rolesobject->properties;

	$isvalid = $rolesobject->properties['email']->checkInput();
	$isvalid2 = $rolesobject->properties['password']->checkInput();

	if (!$isvalid || !$isvalid2 || xarUserIsLoggedIn()) {
		// Bad data: redisplay the form with the data we picked up and with error messages
		return xarTplModule('shop','user','checkout', $data);               
	} else {

		if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }     

		// Create the role and the customer object and then log in
		$email = $rolesobject->properties['email']->getValue();
		$password = $rolesobject->properties['password']->getValue();

		$rolesobject->properties['name']->setValue($email);
		$rolesobject->properties['email']->setValue($email);
		$rolesobject->properties['uname']->setValue($email);
		$rolesobject->properties['password']->setValue($password);
		$rolesobject->properties['state']->setValue(3);
		//$authmodule = (int)xarMod::getID('shop');
		//$rolesobject->properties['authmodule']->setValue($authmodule);
		$uid = $rolesobject->createItem();

		$custobject = DataObjectMaster::getObject(array('name' => 'shop_customers'));
		$custobject->createItem(array('id' => $uid));

	   xarMod::APIFunc('authsystem','user','login',array('uname' => $email, 'pass' => $password));
		
	}


	return $data;

}

?>