<?php
function netquery_adminapi_flremove($args)
{
    extract($args);
    if (!isset($flag_id))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $data = xarModAPIFunc('netquery', 'admin', 'getflag', array('flag_id' => (int)$flag_id));
    if (empty($data))
    {
        $msg = xarML('No Such Service Flag Present', 'netquery');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    if(!xarSecurityCheck('DeleteNetquery')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $FlagsTable = $xartable['netquery_flags'];
    $query = "DELETE FROM $FlagsTable WHERE flag_id = ?";
    $result =& $dbconn->Execute($query, array((int)$flag_id));
    if (!$result) return;
    return true;
}
?>