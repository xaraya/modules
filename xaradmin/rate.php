<?php
 /**
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage uspsws Module
 * @link http://www.xaraya.com/index.php/release/eid/1033
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *  Rate calculator
 */
function uspsws_admin_rate()
{

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AdminUSPSWS')) return;

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');

	// Get the object label for the template
	$object = DataObjectMaster::getObject(array('name' => 'uspsws_rate'));
	$data['label'] = $object->label;

	// Get the fields to display in the admin interface
	//$config = $object->configuration;

	$data['object'] = $object;

    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

    if ($data['confirm']) {

		$userid = xarModVars::get('uspsws','userid');

		if (empty($userid)) {
			$msg = "You need to enter your USPS developer credentials first.  <a href='" . xarmodurl('uspsws','admin','overview') . "'>Read me</a>.";
			$data['msg'] = $msg;
		} else {
			$isvalid = $object->checkInput();

			if (!$isvalid) {
				return xarTplModule('uspsws','admin','rate', $data);
			} else {
				$properties = $object->getProperties();
				$values = $object->getFieldValues();
				// etcetera

				// Here we're just testing with just one package
				$packages[0] = array( 
					'service' => $values['service'],
					'firstclassmailtype' => $values['firstclassmailtype'],
					'ziporigination' => $values['ziporigination'],
					'zipdestination' => $values['zipdestination'],
					'pounds' => $values['pounds'],
					'ounces' => $values['ounces'],
					'size' => $values['size'],
					'machinable' => $values['machinable'],
					);
				// We could add more than one package to the $packages array...
				/*$packages[1] = array( 
					'service' => 'FIRST CLASS',
					'firstclassmailtype' => 'LETTER',
					'ziporigination' => '20002',
					'zipdestination' => '80303',
					'pounds' => 0,
					'ounces' => 5,
					'size' => 'REGULAR',
					'machinable' => 1,
					);*/

				$args['packages'] = $packages;
				$response = xarMod::APIFunc('uspsws','admin','rate',$args);

				if (count($response->Package) > 1) {
					foreach($response->Package as $pkg) {
						$rates[] = (float)$pkg->Postage->Rate;
					}
					$data['rate'] = array_sum($rates);
				} else {
					$data['rate'] = $response->Package->Postage->Rate;
				}

				if(isset($response->Package->Error)) {
					$data['error'] = $response->Package->Error->Description;
				}

				$data['response'] = $response;
	
			}
		}
	}


    // Return the template variables defined in this function
    return $data;
}

?>
