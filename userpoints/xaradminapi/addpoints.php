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
                  VALUES(?,?,?,?,?,?,?,?)";
            $bindvars = array((int)$nextId, (int)$moduleid, $itemtype, $objectid, $status, 
                                   $authorid, $pubdata, $points);
            $result =& $dbconn->Execute($sql, $bindvars);
            if (!$result) return;

    
    // Return the extra info
    return ;
}
?>
