<?php
// return an array containing ephemerids data
function ephemerids_adminapi_display()
{
    // Security Check
    if(!xarSecurityCheck('EditEphemerids')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $ephemtable = $xartable['ephem'];

    $query = "SELECT xar_eid,
                     xar_did, 
                     xar_mid, 
                     xar_yid,
                     xar_content,
                     xar_elanguage
    FROM $ephemtable ORDER BY xar_eid DESC";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $resarray = array();

    while(list($eid, $did, $mid, $yid, $content, $elanguage) = $result->fields) {
    $result->MoveNext();

    $resarray[] = array('eid' => $eid,
                'did' => $did,
                'mid' => $mid,
                'yid' => $yid,
                'content' => $content,
                'elanguage' => $elanguage);
    }
    $result->Close();

    return $resarray;
}
?>