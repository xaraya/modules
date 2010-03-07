<?php

function fedexws_adminapi_getrequestresponse($client) {

	$msg = '<h2>Transaction processed successfully.</h2>'. "\n"; 
	$msg .= '<h2>Request</h2>' . "\n";
	$msg .= '<pre>' . htmlspecialchars($client->__getLastRequest()). '</pre>';  
	$msg .= "\n";

	$msg .= '<h2>Response</h2>'. "\n";
	$msg .= '<pre>' . htmlspecialchars($client->__getLastResponse()). '</pre>';
	$msg .= "\n";

	return $msg;

}


?>