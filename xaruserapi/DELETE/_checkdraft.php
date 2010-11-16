<?php
/**
 * Psspl:Adeded the function for checking the draft messages.
 * It sets the "allow_modify" options if selected recipient 
 * is available in current configuration.
  * @return array of messages selected. 
 */

sys::import('modules.messages.xarincludes.defines');

function messages_userapi_checkdraft( $args )
{

    extract($args);
    
        $messages = xarMod::apiFunc('messages', 'user', 'getall', array('folder' => 'drafts',));
        
        $users = xarMod::apiFunc('messages', 'user', 'get_sendtousers');
        
        foreach ($messages as $key => $message) {
            $messages[$key]['allow_modify'] = false;
            foreach ($users as $user_key => $user) {
                if ($message['recipient'] == $user['name']) {
                        $messages[$key]['allow_modify'] = true;
                }
            }
        }
        return $messages;
}
?>    
