<?php
/**
 * API function for setting the configuration for group.
 * Function save the sent messages configuration for selected group.
 * * @param groupid,selected group for send messages.
 */
include_once("./modules/commonutil.php");
function messages_adminapi_getconfig( $args )
{
    extract( $args );
     	
	$selectedGroup = unserialize(xarModVars::get('messages',"allowedSendMessages[$group]"));
   	
	TracePrint($selectedGroup,"selected");
   	
   	if (!empty($selectedGroup)) {  	 	 
   		
   		$selectedGroupStr = implode(",",$selectedGroup);//convert array into comma seprated string.
   		
   		TracePrint($selectedGroupStr,"str selected");
   		
   		return $selectedGroupStr;
   	}
   	else {
   	  	$msg = xarML('Invalid select group info in #(1) function #(2)in module #(3)',
	                     'adminapi', 'getconfig', 'messages');
	    //throw new Exception($msg);
	    return null;
   	}
}
