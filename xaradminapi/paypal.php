<?php

/** DoDirectPayment NVP example; last modified 08MAY23.
 *
 *  Process a credit card payment. 
*/

$environment = 'sandbox';

/**
 * Send HTTP POST Request
 *
 * @param   string  The API method name
 * @param   string  The POST Message fields in &name=value pair format
 * @return  array   Parsed HTTP Response body
 */

function shop_adminapi_paypal($args) 
{
    global $environment;

    // defaults
    $paymentType = urlencode('Sale');
    $countrycode = 'US';
    $currency = 'USD';

    extract($args);
    
    $firstName = urlencode($transfields['first_name']);
    $lastName = urlencode($transfields['last_name']);
    $creditCardType = urlencode($transfields['card_type']);
    $creditCardNumber = urlencode($transfields['card_num']);
    
    $exp = $transfields['exp_date'];
    $exp_month = substr($exp,0,2);
    $exp_year = '20' . substr($exp,2,4); // will work until later this century

    $expDateMonth = urlencode($exp_month);
    $expDateYear = urlencode($exp_year);
    $cvv2Number = urlencode($transfields['cvv2']);
    $address1 = urlencode($transfields['street_addr']); 
    $city = urlencode($transfields['city_addr']);
    $state = urlencode($transfields['state_addr']);
    $zip = urlencode($transfields['postal_code']);
    $country = urlencode($countrycode);
    $amount = urlencode($transfields['total']);
    $currencyID = urlencode($currency);     

    // Add request-specific fields to the request string.
    $str =  "&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber";
    $str .= "&EXPDATE=$expDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName";
    $str .= "&STREET=$address1&CITY=$city&STATE=$state&ZIP=$zip&COUNTRYCODE=$country&CURRENCYCODE=$currencyID";

    $nvpStr_ = $str;

    // Set up your API credentials, PayPal end point, and API version.
    $API_UserName = urlencode(trim(xarModVars::get('shop','pg_id')));
    $API_Password = urlencode(trim(xarModVars::get('shop','pg_key')));
    $API_Signature = urlencode(trim(xarModVars::get('shop','pg_api_signature')));
    $API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";

    $version = urlencode('51.0');

    // Set the curl parameters.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);

    // Turn off the server and peer verification (TrustManager Concept).
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);

    // Set the API operation, version, and API signature in the request.
    $nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";
 
    // Set the request as a POST FIELD for curl.
    curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

    // Get response from the server.
    $httpResponse = curl_exec($ch);

    if(!$httpResponse) {
        exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
    }

    // Extract the response details.
    $httpResponseAr = explode("&", $httpResponse);

    $httpParsedResponseAr = array();
    foreach ($httpResponseAr as $i => $value) {
        $tmpAr = explode("=", $value);
        if(sizeof($tmpAr) > 1) {
            $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
        }
    }

    if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
        exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
    }

    return $httpParsedResponseAr;

}

?>