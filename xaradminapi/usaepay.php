<?php
 
include "usaepaylib.php";
 
$tran=new umTransaction;
 
$tran->key="WNkue4k63w1h49q9dVNJq09T78xbZg4r";
$tran->ip=$_SERVER['REMOTE_ADDR'];   // This allows fraud blocking on the customers ip address

$tran->usesandbox = true;
$tran->ignoresslcerterrors = true;
 
$tran->testmode=1;    // Change this to 0 for the transaction to process
 
$tran->card="4000200011112222";		// card number, no dashes, no spaces
$tran->exp="1212";			// expiration date 4 digits no /
$tran->amount="1.00";			// charge amount in dollars
$tran->invoice="1234";   		// invoice number.  must be unique.
$tran->cardholder="Test T Jones"; 	// name of card holder
$tran->street="1234 Main Street";	// street address
$tran->zip="05673";			// zip code
$tran->description="Online Order";	// description of charge
$tran->cvv2="435";			// cvv2 code	
 
echo "<h1>Please wait one moment while we process your card...<br>\n";
flush();
 
if($tran->Process())
{
	echo "<b>Card Approved</b><br>";
	echo "<b>Authcode:</b> " . $tran->authcode . "<br>";
	echo "<b>AVS Result:</b> " . $tran->avs . "<br>";
	echo "<b>Cvv2 Result:</b> " . $tran->cvv2 . "<br>";
} else {
	echo "<b>Card Declined</b> (" . $tran->result . ")<br>";
	echo "<b>Reason:</b> " . $tran->error . "<br>";	
	if($tran->curlerror) echo "<b>Curl Error:</b> " . $tran->curlerror . "<br>";	
}		
 
?>