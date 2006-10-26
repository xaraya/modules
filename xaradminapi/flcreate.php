<?php
function netquery_adminapi_flcreate($args)
{
    extract($args);
    if ((!isset($flag_flagnum)) || (!isset($flag_keyword)) || (!isset($flag_fontclr)))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    if(!xarSecurityCheck('AddNetquery')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $FlagsTable = $xartable['netquery_flags'];
    $nextId = $dbconn->GenId($FlagsTable);
    $query = "INSERT INTO $FlagsTable (
              flag_id, flagnum, keyword, fontclr, lookup_1)
              VALUES (?,?,?,?,?)";
    $bindvars = array($nextId, (int)$flag_flagnum, $flag_keyword, $flag_fontclr, $flag_lookup_1);
    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) return;
    $flag_id = $dbconn->PO_Insert_ID($FlagsTable, 'flag_id');
    return $flag_id;
}
?>