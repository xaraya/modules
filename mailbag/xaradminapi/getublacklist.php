<?php
/**
    Gets all Recipients in the blacklist
*/
function mailbag_adminapi_getublacklist($args)
{
    $dbconn   =& xarDBGetConn();
    $xartable =  xarDBGetTables();
    
    $table = $xartable['mailbag_ublacklist'];

    $sql = "SELECT xar_ubid,
                   xar_uid
            FROM $table
            ORDER BY xar_uid";
    $result = $dbconn->Execute($sql);
    if (!isset($result)) return;

    $ublacklist = array();
    while(list($ubid, $uid) = $result->fields)
    {
        $ublacklist[$uid] = $uid;
        $result->MoveNext();
    }

    $result->Close();
    
    return $ublacklist;
}
?>