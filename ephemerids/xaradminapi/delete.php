<?php
// delete ephemerids
function ephemerids_adminapi_delete($args)
{
    extract($args);

    // Argument check
    if (!isset($eid) || !is_numeric($eid)) {
        $msg = xarML('Invalid argument',
                    'eid', 'admin', 'delete', 'ephemerid');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security Check
    if(!xarSecurityCheck('DeleteEphemerids')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ephemtable = $xartable['ephem'];

    $query = "DELETE FROM $ephemtable WHERE xar_eid = ?";
    $bindvars = array($eid);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    // Let any hooks know that we have deleted a link
    xarModCallHooks('item', 'delete', $eid, '');
    // Let the calling process know that we have finished successfully
    return true;
}
?>