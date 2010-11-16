<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
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

sys::import('modules.messages.xarincludes.defines');

function messages_userapi_reply_message_subject( $args )
{
    extract($args);
       
    $subject    = "RE:";
    $subject    .= $message['subject'];
      
    return $subject;
}

?>
