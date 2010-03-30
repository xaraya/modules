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
 *  Complete the order.  If all goes well, we'll submit the transaction to the payment gateway, save our own transaction record, and update customer info
 */
function shop_user_complete() {

	if(!xarVarFetch('savedmethodsubmit', 'str', $savedmethodsubmit, NULL, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('paymentmethod', 'str', $paymentmethod, NULL, XARVAR_NOT_REQUIRED)) {return;}

	if (!isset($_SESSION['did_checkout'])) { 
		// the user probably tried to back arrow after completing a purchase
		return xarResponse::Redirect(xarModURL('shop')); 
	}

	$tid = 0;
	$products = array();

	$cust = xarMod::APIFunc('shop','user','customerinfo'); 
	$data['cust'] = $cust; 

	if (isset($_SESSION['products']) && $_SESSION['total'] > 0) {  

		$data['products'] = $_SESSION['products'];

		sys::import('modules.dynamicdata.class.objects.master');
		$transobject = DataObjectMaster::getObject(array('name' => 'shop_transactions'));

		$time = time();
		$_SESSION['time'] = $time;

		$myfields = array('first_name', 'last_name', 'street_addr', 'city_addr', 'state_addr', 'postal_code', 'card_type','card_num', 'cvv2', 'exp_date');

		// If we're using a saved payment method...
		if ($savedmethodsubmit) {
			
			$paymentobject = DataObjectMaster::getObject(array('name' => 'shop_paymentmethods'));
			$paymentobject->getItem(array('itemid' => $paymentmethod));
 
			foreach ($myfields as $field) {
					${$field} = $paymentobject->properties[$field]->getValue();
					unset($_SESSION['errors'][$field]); //Clear errors from any earlier submission
			}	

		} else {  // We're not using a saved payment method...

			foreach ($myfields as $field) {
				$isvalid = $transobject->properties[$field]->checkInput();
				if (!$isvalid) {
					$_SESSION['errors'][$field] = true;
				} else {
					unset($_SESSION['errors'][$field]); // In case we previously submitted invalid input in this field
				}

				${$field} = $transobject->properties[$field]->getValue();

				if (isset($exp_date)) {
					$exp_month = substr($exp_date,0,2);
					$exp_year = substr($exp_date,2,4);
					$reverse_date = $exp_year . $exp_month;
					$minimum_date = date('ym',time());
					if ($minimum_date > $reverse_date) {
						$_SESSION['errors']['exp_date'] = true;
					}
				}
				
				if ($field != 'card_num') {
					/*Save values to $_SESSION['checkout'] in case we need to re-display the form in user-checkout.xt, but don't re-display the card number*/ 
					$_SESSION['checkout'][$field] = ${$field};
				}

			} // end foreach
		}
		// If fields don't validate, re-display the checkout page.
		// If were not in Demo Mode, the payment gateway will do its own validation, but we might as well try to catch things here before submitting the transaction.
		if (!empty($_SESSION['errors'])) {
			xarResponse::Redirect(xarModURL('shop','user','checkout').'#errors');
			return;
		}

		// These fields are used for saving the payment method, processing the transaction and for saving our own transaction record...
		$paymentfields = array(
			'customer' => $cust['id'], 
			'first_name' => $first_name,
			'last_name' => $last_name,
			'street_addr' => $street_addr,
			'city_addr' => $city_addr,
			'state_addr' => $state_addr,
			'postal_code' => $postal_code,
			'card_type' => $card_type,
			'card_num' => $card_num,
			'cvv2' => $cvv2,
			'exp_date' => $exp_date
			);

		$transfields = $paymentfields;
		// A few more fields we need for the transaction...
		$transfields['date'] = $time;
		$transfields['products'] = serialize($data['products']);
		$transfields['total'] =  $_SESSION['total'];

		if (!$savedmethodsubmit) { 
			// We only need to save customer/roles info if this is the customer's first transaction
			// Save the customer data
			$custobject = DataObjectMaster::getObject(array('name' => 'shop_customers'));
			$id = $custobject->getItem(array('itemid' => $cust['id']));
			// An empty last name indicates that we probably need to save some customer info...
			if (empty($custobject->properties['last_name']->value)) {
				$custobject->properties['first_name']->setValue($first_name);
				$custobject->properties['last_name']->setValue($last_name);
				$custobject->properties['street_addr']->setValue($street_addr);
				$custobject->properties['city_addr']->setValue($city_addr);
				$custobject->properties['postal_code']->setValue($postal_code);
				$custobject->properties['state_addr']->setValue($state_addr);
				$custobject->updateItem();

				// update the name field in roles to use first and last name instead of email
				$rolesobject = DataObjectMaster::getObject(array('name' => 'roles_users'));
				$rolesobject->getItem(array('itemid' => $cust['id']));
				$rolesobject->properties['name']->setValue($first_name . ' ' . $last_name);
				$rolesobject->updateItem();
			}
		}

		/*****************************/
		/***** PAYMENT PROCESSING ****/
		/*****************************/

		$response = xarMod::APIFunc('shop','admin','handlepgresponse', array('transfields' => $transfields));
		
		if (isset($response['trans_id']) && !empty($response['trans_id'])) { 
			// We have a successful transaction...
			$data['response'] = $response;
			$transfields['pg_transaction_id'] = $response['trans_id'];
			$tid = $transobject->createItem($transfields);
			unset($_SESSION['pg_response']);
			unset($_SESSION['checkout']); 

			if (!$savedmethodsubmit) {
				// Even though the customer has elected not to select one of the saved payment methods (radio buttons), let's see if by some chance they're submitting a payment through the form that uses one of their saved payment methods... and if so, let's make sure we have the latest exp date...
				$paymentobject = DataObjectMaster::getObjectList(array('name' => 'shop_paymentmethods'));
				$filters = array(
								 'status'    => DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
								'where' => 'card_num eq '. $card_num
								);
				$items = $paymentobject->getItems($filters);

				if (count($items) > 0) {
					// We've saved this payment method before... but let's update it since we know the transaction went through, so we know we have the latest info (exp date) for this payment method...
					foreach ($items as $item) { 
						$id = $item['id'];
					}
					$paymentobject = DataObjectMaster::getObject(array('name' => 'shop_paymentmethods'));
					$paymentobject->getItem(array('itemid' => $id));
					$paymentobject->properties['exp_date']->setValue($exp_date);
					$paymentobject->updateItem();
				} else {
					// This is a new payment method, so create it...
					$paymentobject = DataObjectMaster::getObject(array('name' => 'shop_paymentmethods'));
					$pid = $paymentobject->createItem($paymentfields);
				}

			}

		} else {
			// There must be a problem...
			$pg_id = xarModVars::get('shop','pg_id');
			$pg_key = xarModVars::get('shop','pg_key');
			if (empty($pg_key)) {
				$_SESSION['pg_response']['msg'] .= "<p style='color:red'><strong>Looks like you haven't entered a payment gateway key.  <a href='".xarModURL('shop','admin','overview')."'>Read me</a>.</strong></p>";
			}
 
			xarResponse::Redirect(xarModURL('shop','user','checkout'));
			return;
		}

	}

	$data['total'] = $_SESSION['total'];
	$data['tid'] = $tid;
	$data['date'] = date('F j, Y g:i a',$_SESSION['time']);
	
	//Need to clear all this now that the purchase went through
	unset($_SESSION['errors']);
	unset($_SESSION['shop']);
	unset($_SESSION['products']);
	unset($_SESSION['did_checkout']);

	return $data;

}

?>