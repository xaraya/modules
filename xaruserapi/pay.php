<?php
 /**
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage amazonfps
 * @link http://xaraya.com/index.php/release/eid/1169
 * @author potion <ryan@webcommunicate.net>
 */

/**
 *  Finish the payment by sending the tokenID back to Amazon.
 *  Until this function runs, there hasn't really been a payment.
 */

$modulepath = sys::code() . 'modules/amazonfps/';
require_once($modulepath . '.config.inc.php');
require_once($modulepath . 'Amazon/CBUI/CBUISingleUsePipeline.php');

function amazonfps_userapi_pay($args) {

	if (!xarSecurityCheck('AddAmazonFPS')) return;

	$service = new Amazon_FPS_Client(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY);

	$currency = 'USD';

	extract($args);

	$request =  new Amazon_FPS_Model_PayRequest();
	$request->setSenderTokenId($tokenID);//set the proper senderToken here.
	$amount = new Amazon_FPS_Model_Amount();
	$amount->setCurrencyCode($currency);
	$amount->setValue($paymentamount); //set the transaction amount here;
	$request->setTransactionAmount($amount);
	$request->setCallerReference($callerReference); //set the unique caller reference here.

	$result['error'] = '';

	try {
		$response = $service->pay($request);
              
		if ($response->isSetPayResult()) {
			$payResult = $response->getPayResult();
			if ($payResult->isSetTransactionId()) {
				//$msg .= "                TransactionId\n";
				$result['TransactionId'] = $payResult->getTransactionId();
			}
			if ($payResult->isSetTransactionStatus()) {
				//$msg .= "                TransactionStatus\n";
				$result['TransactionStatus'] = $payResult->getTransactionStatus();
			}
		} 
		if ($response->isSetResponseMetadata()) {  
			$responseMetadata = $response->getResponseMetadata();
			if ($responseMetadata->isSetRequestId()) { 
				$result['RequestId'] = $responseMetadata->getRequestId();
			}
		} 

		} catch (Amazon_FPS_Exception $ex) {
			$result['error'] .= "Caught Exception: " . $ex->getMessage() . "<br />";
			$result['error'] .= "Response Status Code: " . $ex->getStatusCode() . "<br />";
			$result['error'] .= "Error Code: " . $ex->getErrorCode() . "<br />";
			$result['error'] .= "Error Type: " . $ex->getErrorType() . "<br />";
			$result['error'] .= "Request ID: " . $ex->getRequestId() . "<br />";
			$result['error'] .= "XML: " . $ex->getXML();
		}
	}

	return $result;

}

?>