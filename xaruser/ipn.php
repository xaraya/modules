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
 *  Accept an IPN from Amazon
 */

$modulepath = sys::code() . 'modules/amazonfps/'; 
require_once($modulepath . 'xarincludes/.config.inc.php');
require_once($modulepath . 'Amazon/IpnReturnUrlValidation/SignatureUtilsForOutbound.php');

function amazonfps_user_ipn() {

	$data['missing'] = array();

	// local (Xaraya) validation for params can be somewhat loose, as we'll do the real validation below with the validateRequest method
	$params['transactionId'] = 'str:1:';
    $params['transactionDate'] = 'int';
    $params['status'] = 'str:1:';
    $params['notificationType'] = 'str:1:';
    $params['callerReference'] = 'str:1:';
    $params['operation'] = 'str:1:';
    $params['transactionAmount'] = 'str:1:';
    $params['buyerName'] = 'str:1:';
    $params['paymentMethod'] = 'str:1:';
    $params['paymentReason'] = 'str:1:';
    $params['recipientEmail'] = 'str:1:';
    $params['signatureMethod'] = 'str:1:';
    $params['signatureVersion'] = 'int';
    $params['certificateUrl'] = 'str:1:';
    $params["signature"] = "str:1:";

	foreach ($params as $key => $val) {
		if (!xarVarFetch($key, $val, ${$key}, NULL, XARVAR_NOT_REQUIRED)) return;
		if (!${$key}) {
			$data['missing'][] = $key;
		} else {
			$sigvalidation[$key] = ${$key};
		}
	}
	
	if (empty($data['missing'])) {

		$urlEndPoint = str_replace('&amp;','&',xarModURL('amazonfps','user','ipn'));
		
		// Amazon validation -- if the params don't validate, this will throw an exception
		$utils = new Amazon_FPS_SignatureUtilsForOutbound();
		$utils->validateRequest($params, $urlEndPoint, "POST");

		sys::import('modules.dynamicdata.class.objects.master');

		$object = DataObjectMaster::getObject(array('name' => 'amazonfps_payments')); 

		$callerItemid = str_replace(trim(xarModVars::get('amazonfps','callerreference_prefix')), '', $callerReference);
		
		$object->getItem(array('itemid' => $callerItemid));
		$object->properties['transactionstatus']->setValue($status);
		$object->properties['paymentmethod']->setValue($paymentMethod);
		$object->updateItem();

		return true;

	} else {
		return false;
	}

}

?>