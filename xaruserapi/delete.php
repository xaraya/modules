<?php
/**
    Deletes the ticket 
*/
function helpdesk_userapi_delete($args)
{
    extract($args);
        
    if( empty($tid) )
        return false;
    
    // Database information
    $dbconn =& xarDBGetConn();
    $xartable       =& xarDBGetTables();
    $helpdesktable  = $xartable['helpdesk_tickets'];

    $sql = "DELETE FROM  $helpdesktable 
                   WHERE xar_id = ?";

    $result = $dbconn->Execute($sql, array($tid));
    if (!$result) return;
    $result->Close();

    return true;
}
?>
