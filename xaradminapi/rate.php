<?php
/**
 * uspsws
 *
 * @package modules
 * @copyright (C) 2009 WebCommunicate.net
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage uspsws
 * @link http://xaraya.com/index.php/release/1033.html
 * @author Ryan Walker <ryan@webcommunicate.net>
 */
/**
 * Calculate USPS rates
 */

function uspsws_adminapi_rate($args) {

	/* I'm using the USPS production server URL below.  The USPS test server is not very useful.  After you sign up for USPS Web Tools, you can send an email to the tech support address at http://www.usps.com/webtools/technical.htm and request that they upgrade you to a production account.  You will likely get a quick response notifying you that your account has been upgraded. */
	$url = "http://production.shippingapis.com/ShippingAPI.dll";

	$usps_userid = xarModVars::get('uspsws','userid');

	extract($args);

	function ordinal_suffix($n) {
		 $n_last = $n % 100;
		 if (($n_last > 10 && $n_last < 14) || $n == 0){
			  return "{$n}th";
		 }
		 switch(substr($n, -1)) {
			  case '1':    return "{$n}st";
			  case '2':    return "{$n}nd";
			  case '3':    return "{$n}rd";
			  default:     return "{$n}th";
		 }
	}

	$xml = '<RateV3Request USERID="' . $usps_userid . '">';

	$num = 0;

	foreach ($packages as $pkg) {

		$num++;
		$ID = ordinal_suffix($num);
		$ID = strtoupper($ID);

		if ($pkg['machinable'] == 1) {
			$pkg['machinable'] = 'true';
		} 
		if ($pkg['machinable'] == 0) {
			$pkg['machinable'] = 'false';
		}

		// There may be a few other optional fields you can add to the XML below.  See the USPS documentation at http://www.usps.com/webtools/technical.htm

		$xml .= '<Package ID="' . $ID . '">';

			$xml .= '<Service>' . $pkg['service'] . '</Service>';

			$xml .= '<FirstClassMailType>' . $pkg['firstclassmailtype'] . '</FirstClassMailType>';

			$xml .= '<ZipOrigination>' . $pkg['ziporigination'] . '</ZipOrigination>';

			$xml .= '<ZipDestination>' . $pkg['zipdestination'] . '</ZipDestination>';

			$xml .= '<Pounds>' . $pkg['pounds'] . '</Pounds>';

			$xml .= '<Ounces>' . $pkg['ounces'] . '</Ounces>';

			$xml .= '<Size>' . $pkg['size'] . '</Size>';

			$xml .= '<Machinable>' . $pkg['machinable'] . '</Machinable>';

		$xml .= '</Package>';

	}

	$xml .= '</RateV3Request>';

	$str = 'API=RateV3&XML=' . $xml;

	$request = curl_init();  
	curl_setopt($request, CURLOPT_URL, $url);  
	curl_setopt($request, CURLOPT_HEADER, 1);  
	curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);  
	curl_setopt($request, CURLOPT_POST, 1); 

	curl_setopt($request, CURLOPT_POSTFIELDS,$str);  
	$response = curl_exec($request); 
 
	curl_close ($request); // close curl object

	$xml = strstr($response, '<?'); 

	$object = new SimpleXMLElement($xml);

	return $object;
 
}

?>