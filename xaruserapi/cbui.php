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
 *  Create the payment item and send the buyer to the CBUI on Amazon's server.
 *  CBUI stands for Co-branded User Interface.
 *  When the buyer is finished with their purchase, Amazon will return the tokenID for *   use in the pay function
 *
 * @param		int	 refitemid the required itemid of the product/service that this payment is for	 
 * @param		str	 objectname required the object for this product/service
 * @param		str	 modulename required the module for this product/service
 * @param		int	 amount required payment amount
 * @param		str	 currency optional, defaults to 'USD'
 * @param		str	 description  optional brief product/service description
 * @param		str	 success_url optional the URL where the buyer should be directed when payment is complete
 * @return		str the url at Amazon where we're sending this buyer to begin the payment process
 */

$modulepath = sys::code() . 'modules/amazonfps/'; 
require_once($modulepath . 'xarincludes/.config.inc.php');
require_once($modulepath . 'Amazon/CBUI/CBUISingleUsePipeline.php');

function amazonfps_userapi_cbui($args) {

	if (!xarSecurityCheck('AddAmazonFPS')) return;

	$currency = 'USD';
	$success_url = xarModURL('amazonfps','user','success');
	$callerprefix = trim(xarModVars::get('amazonfps','callerreference_prefix')); //optionally add a prefix to the callerReference to ensure uniqueness, for example, if you move from a test server to a production server and start over again at 1 with the payment itemids

	extract($args);

	if (!isset($description)) {
		$description = $objectname . ' ' . $refitemid;
	}

	sys::import('modules.dynamicdata.class.objects.master');

	$object = DataObjectMaster::getObject(array('name' => 'amazonfps_payments'));

	$object->properties['refitemid']->setValue($refitemid);
	$object->properties['objectname']->setValue($objectname);
	$object->properties['modulename']->setValue($modulename);
	$object->properties['amount']->setValue($amount);
	$object->properties['currency']->setValue($currency);
	$object->properties['description']->setValue($description); 
	$object->properties['paid']->setValue(0);
	$object->properties['success_url']->setValue($success_url); // redirect used by the pay function after payment is finalized

	$itemid = $object->createItem();

	if (strlen(AWS_ACCESS_KEY_ID) == 0 || strlen(AWS_SECRET_ACCESS_KEY) == 0) {		
        $msg = xarML('Invalid access keys.  Please make sure you have entered your Amazon access keys at ') . xarModURL('amazonfps','admin','modifyconfig');
        throw new Exception($msg);
	}

	$pipeline = new Amazon_FPS_CBUISingleUsePipeline(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY);

	$returnurl = str_replace('&amp;','&',xarModURL('amazonfps','user','pay',array('amount' => $amount)));
 
	$pipeline->setMandatoryParameters(
			$callerprefix . $itemid, // callerReference uses the payment itemid
			$returnurl, // returnurl
			$amount // amount
		);
	
	//optional parameters
	$pipeline->addParameter("currencyCode", $currency);
	$pipeline->addParameter("paymentReason", $description);

	//CBUI url
	return rawurlencode($pipeline->getUrl()); 
}

?>