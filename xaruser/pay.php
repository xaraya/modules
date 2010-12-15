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
 *  Finish the payment.
 *  This is the page that Amazon will request when the buyer has completed their  
 *	 purchase.
 */

$modulepath = sys::code() . 'modules/amazonfps/'; 
require_once($modulepath . 'xarincludes/.config.inc.php');
require_once($modulepath . 'Amazon/IpnReturnUrlValidation/SignatureUtilsForOutbound.php');

function amazonfps_user_pay()
{

    if (!xarSecurityCheck('AddAmazonFPS')) return;

	$data['missing'] = array();

	// returnurl from the cbui will contain: expiry, tokenID, status, callerReference and signature
	$params = array('tokenID' => 'str:1:', 'expiry' => 'str:7:7', 'status' => 'str:1:', 'amount' => 'str', 'callerReference' => 'str:1:', 'signature' => 'str:1:', 'signatureMethod' => 'str:1:', 'signatureVersion' => 'str:1:', 'certificateUrl' => 'str:1:');

	foreach ($params as $key => $val) {
		if (!xarVarFetch($key, $val, ${$key}, NULL, XARVAR_NOT_REQUIRED)) return;
		if (!${$key}) {
			$data['missing'][] = $key;
		} else {
			$sigvalidation[$key] = ${$key};
		}
	}

	//$urlEndPoint = xarModURL('amazonfps','user','pay');
	$urlEndPoint = str_replace('&amp;','&',xarModURL('amazonfps','user','pay',array('amount' => $amount)));
	
	// validate the signature to make sure this request came from Amazon
	$utils = new Amazon_FPS_SignatureUtilsForOutbound();
	
	$utils->validateRequest($sigvalidation, $urlEndPoint, "GET"); 

	if (!empty($data['missing'])) {
		$data['layout'] = 'cbui_response';
		$data['missing'] = implode(', ',$data['missing']);
		return xarTplModule('amazonfps','user','errors',$data);
	}

	$args = array( 
		'tokenID' => $tokenID,
		'callerReference' => $callerReference,
		'paymentamount' => $amount,
		'signature' => $signature 
		); 

	$result = xarMod::APIFunc('amazonfps','user','pay',$args);

	$amazon_error = $result['error'];

	if (empty($amazon_error)) {
		sys::import('modules.dynamicdata.class.objects.master');

		$object = DataObjectMaster::getObject(array('name' => 'amazonfps_payments')); 

		$callerItemid = str_replace('p', '', $callerReference);
		
		$object->getItem(array('itemid' => $callerItemid));
		$success_url = $object->properties['success_url']->value;
		$object->properties['paid']->setValue(1); // we've now successfully paid
		$object->updateItem();

		xarResponse::redirect($success_url);
		return true;

	} else {
		$data['amazon_error'] = $amazon_error;
		$data['layout'] = 'pay_response';
		return xarTplModule('amazonfps','user','errors',$data);
	}
}

?>
