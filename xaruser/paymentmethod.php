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
 *  Select existing payment method or create new one to use for this transaction
 */
function shop_user_paymentmethod() {

	// Redirects at the start of the user functions are just a way to make sure someone isn't where they don't need to be
	if (!xarUserIsLoggedIn()) {
		xarResponse::Redirect(xarModURL('shop','user','viewcart'));
		return;
	}
	if (!isset($_SESSION['shop']) || empty($_SESSION['shop'])) {
		xarResponse::Redirect(xarModURL('shop','user','main'));
		return;
	}

	if(!xarVarFetch('proceedsaved', 'str', $proceedsaved, NULL, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('proceednew', 'str', $proceednew, NULL, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('paymentmethod', 'str', $paymentmethod, NULL, XARVAR_NOT_REQUIRED)) {return;}

	unset($_SESSION['errors']);

	$cust = xarMod::APIFunc('shop','user','customerinfo'); 
	$data['cust'] = $cust; 

	sys::import('modules.dynamicdata.class.objects.master');
	sys::import('modules.dynamicdata.class.properties.master');

	$shippingobject = DataObjectMaster::getObject(array('name' => 'shop_shippingaddresses'));
	$shippingobject->getItem(array('itemid' => $_SESSION['shippingaddress']));
	$shippingvals = $shippingobject->getFieldValues();
	$data['shippingvals'] = $shippingvals;

	// Get the saved payment methods, if any exist
	$mylist = DataObjectMaster::getObjectList(array('name' => 'shop_paymentmethods'));
	$filters = array(
					 'status'    => DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
					'where' => 'customer eq ' . xarUserGetVar('id'),
					);
	$paymentmethods = $mylist->getItems($filters);
	if (count($paymentmethods) > 0) {
		$data['paymentmethods'] = $paymentmethods;
	}

	$myfields = array('first_name', 'last_name', 'street_addr', 'city_addr', 'state_addr', 'postal_code', 'card_type','card_num', 'cvv2', 'exp_date');
	$data['myfields'] = $myfields;

	$paymentobject = DataObjectMaster::getObject(array('name' => 'shop_paymentmethods'));
	$properties = $paymentobject->getProperties();
	$data['properties'] = $properties;

	foreach ($myfields as $field) {
		$propids[$field] = 'dd_' . $properties[$field]->id;  
	}
	$data['propids'] = $propids;

	// If we're using a saved payment method...
	if ($proceedsaved) {
		
		$_SESSION['paymentmethod'] = $paymentmethod;
		xarResponse::Redirect(xarModURL('shop','user','order')); 

	} elseif ($proceednew) {  // We're not using a saved payment method...

		foreach ($myfields as $field) {
			$isvalid = $paymentobject->properties[$field]->checkInput();
			
			if (!$isvalid) {
				print $field; exit;
				$_SESSION['errors'][$field] = true;
			} else {
				unset($_SESSION['errors'][$field]); // In case we previously submitted invalid input in this field
			}

			${$field} = $paymentobject->properties[$field]->getValue();
			$values[$field] = ${$field};
			$values['customer'] = xarUserGetVar('id');

			if ($field != 'card_num') {
				/*Save values to $_SESSION['checkout'] in case we need to re-display the form in user-paymentmethod.xt, but don't re-display the card number*/ 
				$_SESSION['payment'][$field] = ${$field};
			}

		} // end foreach

		if (isset($exp_date)) {
			$exp_month = substr($exp_date,0,2);
			$exp_year = substr($exp_date,2,4);
			$reverse_date = $exp_year . $exp_month;
			$minimum_date = date('ym',time());
			if ($minimum_date > $reverse_date) {
				$_SESSION['errors']['exp_date'] = true;
			}
		} 

		if (!empty($_SESSION['errors'])) {
			var_dump($_SESSION['errors']);
			exit;
			xarResponse::Redirect(xarModURL('shop','user','paymentmethod').'#errors');
		} else {
			$paymentobject->setFieldValues($values,1);
			$_SESSION['paymentmethod'] = $paymentobject->createItem();
			xarResponse::Redirect(xarModURL('shop','user','order')); 
		}
		
	}

	return $data;

}

?>