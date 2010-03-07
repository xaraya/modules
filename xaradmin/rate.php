<?php
 /**
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage fedexws Module
 * @link http://www.xaraya.com/index.php/release/eid/1031
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *  Rate calculator
 */
function fedexws_admin_rate()
{

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AdminFedExWS')) return;

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');

	// Get the object label for the template
	$object = DataObjectMaster::getObject(array('name' => 'fedexws_rate'));
	$data['label'] = $object->label;

	// Get the fields to display in the admin interface
	//$config = $object->configuration;

	$data['object'] = $object;

    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

    if ($data['confirm']) {

		$key = xarModVars::get('fedexws','key');

		if (empty($key)) {
			$msg = "You need to enter your FedEx developer credentials first.  <a href='" . xarmodurl('fedexws','admin','overview') . "'>Read me</a>.";
			$data['msg'] = $msg;
		} else {
			$isvalid = $object->checkInput();
			

			if (!$isvalid) {
				return xarTplModule('shop','admin','rate', $data);
			} else {
				$properties = $object->getProperties();
				$DropoffType = $properties['dropofftype']->getValue(); 
				$ServiceType = $properties['servicetype']->getValue();
				$PackagingType = $properties['packagingtype']->getValue();
				// etcetera

				$args = array(
					'getobj' => true, // if not true, we'll see the XML string from FedEx
					'DropoffType' => $DropoffType,
					'ServiceType' => $ServiceType,
					'PackagingType' => $PackagingType,
					// etcetera
					);

				$response = xarMod::APIFunc('fedexws','admin','rate',$args);
				$data['response'] = $response;
				if (is_object($response)) {
					$data['charge'] = $response->RateReplyDetails->RatedShipmentDetails[0]->ShipmentRateDetail->TotalBaseCharge;
				}
			}
		}
	}


    // Return the template variables defined in this function
    return $data;
}

?>
