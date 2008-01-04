<?php
function netquery_userapi_getflagdata($args)
{
    extract($args);
    if (!isset($flagnum))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $FlagsTable = $xartable['netquery_flags'];
    $query = "SELECT * FROM $FlagsTable WHERE flagnum = ?";
    $bindvars = array($flagnum);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    list($flag_id, $flagnum, $keyword, $fontclr, $backclr, $lookup_1, $lookup_2) = $result->fields;
    if (!xarSecurityCheck('OverviewNetquery',0)) return;
    $flagdata = array('flag_id'  => $flag_id,
                      'flagnum'  => $flagnum,
                      'keyword'  => $keyword,
                      'fontclr'  => $fontclr,
                      'backclr'  => $backclr,
                      'lookup_1' => $lookup_1,
                      'lookup_2' => $lookup_2);
    $result->Close();
    return $flagdata;
}
?>