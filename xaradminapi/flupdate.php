<?php
function netquery_adminapi_flupdate($args)
{
    extract($args);
    if ((!isset($flag_id)) || (!isset($flag_keyword)) || (!isset($flag_fontclr)))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $data = xarModAPIFunc('netquery', 'admin', 'getflag', array('flag_id' => (int)$flag_id));
    if ($data == false)
    {
        $msg = xarML('No Such Service Flag Present', 'netquery');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    if(!xarSecurityCheck('EditNetquery')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $FlagsTable = $xartable['netquery_flags'];
    $query = "UPDATE $FlagsTable
        SET keyword  = ?,
            fontclr  = ?,
            lookup_1 = ?
        WHERE flag_id = ?";
    $bindvars = array($flag_keyword, $flag_fontclr, $flag_lookup_1, (int)$flag_id);
    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) return;
    return true;
}
?>
