<?php
function userpoints_adminapi_addpoints($args)
{
    extract($args);
    
	//add the points to the table.
	$dbconn =& xarDBGetConn();
	$xartable =& xarDBGetTables();
	$pointstable = $xartable['userpoints'];
	
	// Get next ID in table
	$nextId = $dbconn->GenId($pointstable);	
	
  $query = "INSERT INTO $pointstable(xar_upid, 
							xar_uptid, 
							xar_uid, 
							xar_points)
							VALUES (
							$nextId,
							" . xarVarPrepForStore($uptid) .",
							" . xarVarPrepForStore($uid) .",
							" . xarVarPrepForStore($points) . "
							)";
	
	$result =& $dbconn->Execute($query);
	if (!$result) return;
    
	// Return the extra info
	return $extrainfo;
}
?>