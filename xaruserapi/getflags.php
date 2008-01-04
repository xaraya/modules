<?php
function netquery_userapi_getflags($args)
{
    extract($args);
    if ((!isset($startnum)) || (!is_numeric($startnum))) $startnum = 1;
    if ((!isset($numitems)) || (!is_numeric($numitems))) $numitems = -1;
    $flags = array();
    if (!xarSecurityCheck('OverviewNetquery',0)) return $flags;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $FlagsTable = $xartable['netquery_flags'];
    $query = "SELECT * FROM $FlagsTable ORDER BY flagnum";
    $result =& $dbconn->SelectLimit($query, (int)$numitems, (int)$startnum-1);
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext())
    {
        list($flag_id, $flagnum, $keyword, $fontclr, $backclr, $lookup_1, $lookup_2) = $result->fields;
        $flags[] = array('flag_id'  => $flag_id,
                         'flagnum'  => $flagnum,
                         'keyword'  => $keyword,
                         'fontclr'  => $fontclr,
                         'backclr'  => $backclr,
                         'lookup_1' => $lookup_1,
                         'lookup_2' => $lookup_2);
    }
    $result->Close();
    return $flags;
}
?>