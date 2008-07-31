<?php
/**
 * API function for setting the configuration for group.
 * Function save the sent messages configuration for selected group.
 * * @param groupid,selected group for send messages.
 */
include_once("./modules/commonutil.php");
function messages_adminapi_setconfig( $args )
{
    extract( $args );
      	
	if ($childgroupsimploded == '$childgroupsoptionkeys') {
		$childgroupsimploded = 0;//select all groups
	}
    
	$selectedGroup = explode(",",$childgroupsimploded);
	
	TracePrint($selectedGroup,"arrary selectedGroup");
	
	xarModVars::set('messages',"allowedSendMessages[$group]",serialize($selectedGroup));
	
	return true;
}
