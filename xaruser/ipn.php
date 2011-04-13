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
	
	// Available post vars: transactionId, statusMessage, transactionDate, signatureVersion, signatureMethod, buyerEmail, notificationType, callerReference, operation, transactionAmount, transactionStatus, recipientEmail, buyerName, signature, recipientName, paymentMethod, certificateUrl, statusCode

	// Local (Xaraya) validation for params can be somewhat loose, as we'll do the real validation below with the validateRequest method.

	// required
	$params['paymentMethod'] = 'str:1:';
	$params['statusCode'] = 'str:1:';
    $params['signatureMethod'] = 'str:1:';
    $params['signatureVersion'] = 'int';
    $params['certificateUrl'] = 'str:1:';
    $params['signature'] = 'str:1:';
	$params['callerReference'] = 'str:1:';

	// not required?
	$params['notificationType'] = 'str:1:';
	$params['transactionId'] = 'str:1:';
    $params['transactionDate'] = 'int';
    $params['recipientEmail'] = 'str:1:';
    $params['operation'] = 'str:1:';
    $params['transactionAmount'] = 'str:1:';
    $params['buyerName'] = 'str:1:';

	foreach ($params as $key => $val) {
		if (!xarVarFetch($key, $val, ${$key}, NULL, XARVAR_POST_ONLY)) {
			//mail('test@test.com','test',$key);
			return;
		}
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
		$refitemid = $object->properties['refitemid']->value;
		$modulename = $object->properties['modulename']->value;
		$objectname = $object->properties['objectname']->value;
		$object->properties['transactionstatus']->setValue($statusCode);
		$object->properties['paymentmethod']->setValue($paymentMethod);
		$object->updateItem();

		if ($statusCode == 'Success') {
			
			/*xarMod::apiFunc('mail', 'admin', 'sendmail', array(
						'info'         => 'ryandw@gmail.com',
						'name'         => 'admin',
						'subject'      => 'Success', 
						'message'      => 'ipn test'
						//'from'         => 'ryan@webcommunicate.net',
						//'fromname'     => 'admin'
				));*/

			// let another module know that the status == 'Success'
			$ipn = xarMod::apiFunc('contributions', 'admin', 'ipn', array( 
							'objectname' => $objectname,
							'itemid' => $refitemid 
				));

		}

		return true;

	} else {
		return false;
	}

}

?>