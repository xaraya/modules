<?php
 /**
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage shop Module
 * @link http://www.xaraya.com/index.php/release/eid/1031
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *  Connect to authorize.net
 */
function shop_adminapi_authorizenet($args) {

	extract($args);

	$post_url = "https://test.authorize.net/gateway/transact.dll";

	$post_values = array(

		// the API Login ID and Transaction Key must be replaced with valid values
		"x_login"			=> xarModVars::get('shop','pg_id'),
		"x_tran_key"		=> xarModVars::get('shop','pg_key'),

		"x_version"			=> "3.1",
		"x_delim_data"		=> "TRUE",
		"x_delim_char"		=> "|",
		"x_relay_response"	=> "FALSE",

		"x_type"			=> "AUTH_CAPTURE",
		"x_method"			=> "CC",
		"x_card_num"		=> $card_num,
		"x_exp_date"		=> $exp_date,

		"x_amount"			=> $total,
		"x_description"		=> $products,

		"x_first_name"		=> $first_name,
		"x_last_name"		=> $last_name,
		"x_address"			=> $street_addr,
		"x_state"			=> $state_addr,
		"x_zip"				=> $postal_code
		// Additional fields can be added here as outlined in the AIM integration
		// guide at: http://developer.authorize.net
	);

	// This section takes the input fields and converts them to the proper format
	// for an http post.  For example: "x_login=username&x_tran_key=a1B2c3D4"
	$post_string = "";
	foreach( $post_values as $key => $value )
		{ $post_string .= "$key=" . urlencode( $value ) . "&"; }
	$post_string = rtrim( $post_string, "& " );

	// This sample code uses the CURL library for php to establish a connection,
	// submit the post, and record the response.
	// If you receive an error, you may want to ensure that you have the curl
	// library enabled in your php configuration
	$request = curl_init($post_url); // initiate curl object
		curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
		curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
		curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); // use HTTP POST to send form data
		curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response.
		$post_response = curl_exec($request); // execute curl post and store results in $post_response
		// additional options may be required depending upon your server configuration
		// you can find documentation on curl options at http://www.php.net/curl_setopt
	curl_close ($request); // close curl object

	$array = array(0 => '');

	// This line takes the response and breaks it into an array using the specified delimiting character
	$response_array = explode($post_values["x_delim_char"],$post_response);

	//to make things consistent with the authorize.net documentation, we want the authorize.net values to start with a key of 1 
	$response_array = array_merge($array, $response_array);

	return $response_array;

}

?>