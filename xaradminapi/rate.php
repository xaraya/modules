<?php
/**
 * fedexws
 *
 * @package modules
 * @copyright (C) 2009 WebCommunicate.net
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage fedexws
 * @link http://xaraya.com/index.php/release/1032.html
 * @author Ryan Walker <ryan@webcommunicate.net>
 */
/**
 * Calculate FedEx rates
 */

function fedexws_adminapi_rate($args) {

	require_once('code/modules/fedexws/fedex/library/fedex-common.php');
	
	$getobj = false;

	$fedex_key = xarModVars::get('fedexws','key');
	$fedex_password = xarModVars::get('fedexws','password');

	$fedex_acctnumber = xarModVars::get('fedexws','acctnumber');
	$fedex_meternumber = xarModVars::get('fedexws','meternumber');

	//Defaults for testing...

	$Shipper = array('Address' => array(
											  'StreetLines' => array('10 Fed Ex Pkwy'), // Origin details
											  'City' => 'Memphis',
											  'StateOrProvinceCode' => 'TN',
											  'PostalCode' => '38115',
											  'CountryCode' => 'US'));

	$DropoffType = 'REGULAR_PICKUP'; 
	$ServiceType = 'FEDEX_GROUND'; 
	$PackagingType = 'YOUR_PACKAGING';

	$Recipient = array('Address' => array (
												   'StreetLines' => array('13450 Farmcrest Ct'), // Destination details
												   'City' => 'Herndon',
												   'StateOrProvinceCode' => 'VA',
												   'PostalCode' => '20171',
												   'CountryCode' => 'US'));

	$PaymentType = 'SENDER';
	$PayorCountryCode = 'US';
	$RateRequestTypes = 'ACCOUNT'; 
	$RateRequestTypes = 'LIST'; // not sure why they set this twice
	$PackageCount = '2';
	$PackageDetail = 'INDIVIDUAL_PACKAGES';  //  Or PACKAGE_SUMMARY
	$RequestedPackageLineItems = array('0' => array('Weight' => array('Value' => 2.0,
																						'Units' => 'LB'),
																						'Dimensions' => array('Length' => 10,
																							'Width' => 10,
																							'Height' => 3,
																							'Units' => 'IN')),
																	   '1' => array('Weight' => array('Value' => 5.0,
																						'Units' => 'LB'),
																						'Dimensions' => array('Length' => 20,
																							'Width' => 20,
																							'Height' => 10,
																							'Units' => 'IN')));


	extract($args);


	$newline = "<br />";
	//The WSDL is not included with the sample code.
	//Please include and reference in $path_to_wsdl variable.
	$path_to_wsdl = "code/modules/fedexws/fedex/wsdl/RateService_v7.wsdl";

	ini_set("soap.wsdl_cache_enabled", "0");
	 
	$client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

	$request['WebAuthenticationDetail'] = array('UserCredential' =>
										  array('Key' => $fedex_key, 'Password' => $fedex_password)); // Replace 'XXX' and 'YYY' with FedEx provided credentials 
	$request['ClientDetail'] = array('AccountNumber' => $fedex_acctnumber, 'MeterNumber' => $fedex_meternumber);// Replace 'XXX' with your account and meter number
	$request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Request v7 using PHP ***');
	$request['Version'] = array('ServiceId' => 'crs', 'Major' => '7', 'Intermediate' => '0', 'Minor' => '0');
	$request['ReturnTransitAndCommit'] = true;
	
	
	$request['RequestedShipment']['DropoffType'] = $DropoffType; // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
	$request['RequestedShipment']['ShipTimestamp'] = date('c');
	$request['RequestedShipment']['ServiceType'] = $ServiceType; // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
	$request['RequestedShipment']['PackagingType'] = $PackagingType; // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
	$request['RequestedShipment']['Shipper'] = $Shipper;
	$request['RequestedShipment']['Recipient'] =$Recipient;
	$request['RequestedShipment']['ShippingChargesPayment'] = array('PaymentType' => $PaymentType,
															'Payor' => array('AccountNumber' => $fedex_acctnumber, // payor's account number
																		 'CountryCode' => $PayorCountryCode));
	// $request['RequestedShipment']['RateRequestTypes'] = 'ACCOUNT'; // mistake in FedEx's code?
	$request['RequestedShipment']['RateRequestTypes'] = $RateRequestTypes; 
	$request['RequestedShipment']['PackageCount'] = $PackageCount;
	$request['RequestedShipment']['PackageDetail'] = $PackageDetail;  //  Or PACKAGE_SUMMARY
	$request['RequestedShipment']['RequestedPackageLineItems'] = $RequestedPackageLineItems;


	try 
	{
		$response = $client ->getRates($request);
			
		if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR')
		{
			if ($getobj) {
				writeToLog($client);
				return $response;
			}
			$res = xarMod::APIFunc('fedexws','admin','getrequestresponse',$client);
		}
		else
		{
			$res = 'Error in processing transaction.'. $newline. $newline; 
			foreach ($response -> Notifications as $notification)
			{           
				if(is_array($response -> Notifications))
				{              
				   $res .= $notification -> Severity;
				   $res .= ': ';           
				   $res .= $notification -> Message . $newline;
				}
				else
				{
					$res .= $notification . $newline;
				}
			} 
		} 

		writeToLog($client);    // Write to log file  
		return $res;

	} catch (SoapFault $exception) {
	   printFault($exception, $client);        
	}

}

?>