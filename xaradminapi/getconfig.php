<?php
/**
 * API function for setting the configuration for group.
 * Function save the sent messages configuration for selected group.
 * * @param groupid,selected group for send messages.
 */
function messages_adminapi_getconfig( $args )
{
    extract( $args );
        
    $selectedGroup = unserialize(xarModItemVars::get('messages',"allowedSendMessages",$group));
    
    if (!empty($selectedGroup)) {        
        
        $selectedGroupStr = implode(",", $selectedGroup);//convert array into comma seprated string.
        
        return $selectedGroupStr;
    } else {
        $msg = xarML('Invalid select group info in #(1) function #(2)in module #(3)',
                         'adminapi', 'getconfig', 'messages');
        //throw new Exception($msg);
        return null;
    }
}
