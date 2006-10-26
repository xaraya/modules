<?php
function netquery_adminapi_getflag($args)
{
    extract($args);
    if (!isset($flag_id))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $FlagsTable = $xartable['netquery_flags'];
    $query = "SELECT * FROM $FlagsTable WHERE flag_id = ?";
    $bindvars = array((int)$flag_id);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    list($flag_id, $flagnum, $keyword, $fontclr, $backclr, $lookup_1, $lookup_2) = $result->fields;
    if (!xarSecurityCheck('OverviewNetquery')) return;
    $flag = array('flag_id'  => $flag_id,
                  'flagnum'  => $flagnum,
                  'keyword'  => $keyword,
                  'fontclr'  => $fontclr,
                  'backclr'  => $backclr,
                  'lookup_1' => $lookup_1,
                  'lookup_2' => $lookup_2);
    $result->Close();
    return $flag;
}
?>