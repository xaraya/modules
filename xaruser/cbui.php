<?php
 /**
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage amazonfps Module
 * @link http://www.xaraya.com/index.php/release/eid/1033
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *  Don't use this.  Call the CBUI user api function directly.
 */
function amazonfps_user_cbui()
{

 /*   if (!xarSecurityCheck('AddAmazonFPS')) return;

	if (!xarVarFetch('objectname',    'str',   $objectname, NULL,     XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('productid',    'int',   $productid, NULL,     XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('amount',    'int',   $amount, NULL,     XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('currency',    'str',   $currency, 'USD',     XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('description',    'str',   $description, NULL,     XARVAR_NOT_REQUIRED)) return;

    sys::import('modules.dynamicdata.class.objects.master');

	$object = DataObjectMaster::getObject(array('name' => 'amazonfps_payments'));


	$isvalid = $object->checkInput();

	if (!$isvalid) {
		return xarTplModule('amazonfps','admin','cbui', $data);
	} else {
		$properties = $object->getProperties();
		$values = $object->getFieldValues(); 

		$itemid = $object->createItem();

		$args = array( 
			'callerReference' => $itemid, // the payment itemid
			'returnurl' => xarModURL('amazonfps','user','pay'),
			'amount' => $amount,
			'reason' => $description 
			); 

		// send the user to the CBUI
		$url = xarMod::APIFunc('amazonfps','admin','cbui',$args);
		xarResponse::Redirect($url);
		return true;

	return $data;

	}*/

	return;
    
}

?>
