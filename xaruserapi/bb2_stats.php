<?php
function netquery_userapi_bb2_stats()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $SpamblockerTable = $xartable['netquery_spamblocker'];
    $query = "SELECT COUNT(*) FROM $SpamblockerTable WHERE bb_key NOT LIKE '00000000' ";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    list($bbstats) = $result->fields;
    $result->Close();
    return $bbstats;
}
?>
