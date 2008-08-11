<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
/**Psspl:Added the API function for
 * creating the reply message subject.
 *
 * @param message
 * @return subject
 */
include_once("./modules/commonutil.php");
function messages_userapi_reply_message_subject( $args )
{
    extract($args);
	   
   	$subject    = "RE:";
   	$subject 	.= $message['subject'];
   	  
	return $subject;
}

?>
