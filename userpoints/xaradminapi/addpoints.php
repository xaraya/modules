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
          $sql = "INSERT INTO $pointstable (
                                     xar_upid,
							         xar_moduleid, 
							         xar_itemtype,
							         xar_objectid, 
							         xar_status, 
							         xar_authorid, 
							         xar_pubdate, 
							         xar_cpoints)
                  VALUES(" . xarVarPrepForStore($nextId) . ",
						 " . xarVarPrepForStore($moduleid) .",
						 " . xarVarPrepForStore($itemtype) .",
						 " . xarVarPrepForStore($objectid) .",
						 " . xarVarPrepForStore($status) .",
						 " . xarVarPrepForStore($authorid) .",
						 " . xarVarPrepForStore($pubdate) .",
						 " . xarVarPrepForStore($points) . "
							)";
            $result =& $dbconn->Execute($sql);
            if (!$result) return;

    
	// Return the extra info
	return ;
}
?>