<?php
/**
    Gets all Recipients in the blacklist
*/
function mailbag_adminapi_getsblacklist()
{

    $dbconn   =& xarDBGetConn();
    $xartable =  xarDBGetTables();
    
    $table = $xartable['mailbag_sblacklist'];

    $sql = "SELECT xar_sbid,
                   xar_from
            FROM $table
            ORDER BY xar_from";
    $result = $dbconn->Execute($sql);
    if (!isset($result)) return;

    $sblacklist = array();
    while(list($sbid, $from) = $result->fields)
    {
        $sblacklist[$from] = $from;
        $result->MoveNext();
    }

    $result->Close();
  
    return $sblacklist;
}
?>