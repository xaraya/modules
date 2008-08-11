<?php
/**Psspl:Added the API function for
 * creating the reply message text.
 *
 * @param message
 * @return body
 */
include_once("./modules/commonutil.php");
function messages_userapi_reply_message_text( $args )
{
    extract($args);
	
    TracePrint($message,"messages_send");
    	
	if($message['postanon']==1){
    	$sender = "Anonymous";
    }else {
    	$sender = $message['sender'];
    }
    
    $sent = xarLocaleFormatDate('%A, %B %d @ %H:%M:%S', $message['raw_date']);
   	$recipient	= $message['recipient'];
   	$subject 	= $message['subject'];
   	$bodyText   = ">";
   	$bodyText  .= $message['body'];	
   	$bodyText   = str_replace("\n", "\n>", "$bodyText", $count);

	
	$body     = "\0\n";
	$body	  .="----------Original messages-----------";
	//$body    .= "\nFrom:  $sender \n";
    $body	 .= "\nSent : $sent";
    //$body	 .= "To:        $recipient\n";
    $body    .= "\nSubject : $subject\n";
    $body    .= "\n$bodyText\n";
    
	return $body;
}

?>
