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

    $query = "SELECT exec_id,
                     exec_type,
                     exec_local,
                     exec_winsys,
                     exec_remote,
                     exec_remote_t
              FROM $ExecTable
              WHERE exec_type = '" . xarVarPrepForStore($exec_type) . "'";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($exec_id, $exec_type, $exec_local, $exec_winsys, $exec_remote, $exec_remote_t) = $result->fields;

    if(!xarSecurityCheck('OverviewNetquery')) return;

    $exec = array('id'        => $exec_id,
                  'type'      => $exec_type,
                  'local'     => $exec_local,
                  'winsys'    => $exec_winsys,
                  'remote'    => $exec_remote,
                  'remote_t'  => $exec_remote_t);

    $result->Close();
    return $exec;
}

?>
