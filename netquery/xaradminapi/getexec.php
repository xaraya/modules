<?php
/**
 * get executables data
 */
function netquery_adminapi_getexec($args)
{
    extract($args);
    if (!isset($exec_type)) {
        $msg = xarML('Invalid Parameter Count');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ExecTable = $xartable['netquery_exec'];
    $query = "SELECT * FROM $ExecTable WHERE exec_type = '" . xarVarPrepForStore($exec_type) . "'";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    list($exec_id, $exec_type, $exec_local, $exec_winsys, $exec_remote, $exec_remote_t) = $result->fields;
    if(!xarSecurityCheck('OverviewNetquery')) return;
    $exec = array('exec_id'        => $exec_id,
                  'exec_type'      => $exec_type,
                  'exec_local'     => $exec_local,
                  'exec_winsys'    => $exec_winsys,
                  'exec_remote'    => $exec_remote,
                  'exec_remote_t'  => $exec_remote_t);
    $result->Close();
    return $exec;
}

?>
