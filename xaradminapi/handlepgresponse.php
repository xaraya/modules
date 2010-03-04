<?php 
function shop_adminapi_handlepgresponse($args) {

		// Config for paypal
		$countrycode = 'US';
		$currency = 'USD';

		extract($args);

		$pg = xarModVars::get('shop','payment_gateway');
		$trans_id = false;

		switch ($pg) {

			case 1:  // demo mode
			
				$trans_id = rand(1000,99999999); // fake trans id
				break;

			case 2:   // authorize.net
				$response = xarMod::APIFunc('shop','admin','authorizenet',$transfields);
				if ($response[1] == 1) { 
					$trans_id = $response[7]; 
				} else {
					$num = $response[1];
					$authorizenet_codes = array(1 => 'Approved', 2 => 'Declined', 3 => 'Error', 4 => 'Held for Review');
					$msg = $response[4];
					$msg .= ' Response code: ' . $authorizenet_codes[$num];
					$_SESSION['pg_response']['msg'] = $msg;
				}
				break;

			case 3:   // paypal web payments pro

				$paymentType = urlencode('Sale');
				$firstName = urlencode($transfields['first_name']);
				$lastName = urlencode($transfields['last_name']);
				$creditCardType = urlencode($transfields['card_type']);
				$creditCardNumber = urlencode($transfields['card_num']);
				
				$exp = $transfields['exp_date'];
				$exp_month = substr($exp,0,2);
				$exp_year = '20' . substr($exp,2,4); // will work until later this century

				$expDateMonth = urlencode($exp_month);
				$expDateYear = urlencode($exp_year);
				$cvv2Number = urlencode($transfields['cvv2']);
				$address1 = urlencode($transfields['street_addr']); 
				$city = urlencode($transfields['city_addr']);
				$state = urlencode($transfields['state_addr']);
				$zip = urlencode($transfields['postal_code']);
				$country = urlencode($countrycode);
				$amount = urlencode($transfields['total']);
				$currencyID = urlencode($currency);						

				// Add request-specific fields to the request string.
				$str =	"&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber";
				$str .= "&EXPDATE=$expDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName";
				$str .= "&STREET=$address1&CITY=$city&STATE=$state&ZIP=$zip&COUNTRYCODE=$country&CURRENCYCODE=$currencyID";

				$args['methodName_'] = 'DoDirectPayment';
				$args['nvpStr_'] = $str;
				$response = xarMod::APIFunc('shop','admin','paypal',$args);
				
				if ($response['ACK'] == 'Success') { 
					$trans_id = $response['TRANSACTIONID']; 
				} else {
					$msg = $response['ACK'];
					$msg .= '. Response: ' . urldecode($response['L_LONGMESSAGE0']);
					$_SESSION['pg_response']['msg'] = $msg;
				}
			
				break;

			 case 4:   // something else
				// your code
				break;

		}
			 
	$response['trans_id'] = $trans_id;
	return $response;

}

  

?>