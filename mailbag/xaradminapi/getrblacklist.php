<?php
/**
    Gets all Recipients in the blacklist
*/
function mailbag_adminapi_getrblacklist($args)
{
    $dbconn   =& xarDBGetConn();
    $xartable =  xarDBGetTables();
    
    $table = $xartable['mailbag_rblacklist'];

    $sql = "SELECT xar_rbid,
                   xar_to
            FROM $table
            ORDER BY xar_to";
    $result = $dbconn->Execute($sql);
    if (!isset($result)) return;

    $rblacklist = array();
    while(list($rbid, $to) = $result->fields)
    {
        $rblacklist[$to] = $to;
        $result->MoveNext();
    }

    $result->Close();
    
    return $rblacklist;
}
?>