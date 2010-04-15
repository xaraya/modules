<?php 
function shop_adminapi_handlepgresponse($args) {

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

				$args['transfields'] = $transfields;
				$args['methodName_'] = 'DoDirectPayment';
				
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