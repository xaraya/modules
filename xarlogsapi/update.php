<?php

function dossier_logsapi_update($args)
{
    extract($args);

    $invalid = array();
    if (!isset($logid) || !is_numeric($logid)) {
        $invalid[] = 'logid';
    }
    if (!isset($logdate) || !is_string($logdate)) {
        $invalid[] = 'logdate';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'logs', 'update', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('dossier',
                            'logs',
                            'get',
                            array('logid' => $logid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('MyDossierLog', 1, 'Log', "All:All:All:All")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $logstable = $xartable['dossier_logs'];

    $query = "UPDATE $logstable
              SET logdate =?, 
                  logtype = ?,
                  notes = ?
              WHERE logid = ?";

    $bindvars = array(
              $logdate,
              $logtype,
              $notes,
              $logid);
              
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) { // return;
        $msg = xarML('SQL: #(1)',
            $dbconn->ErrorMsg());
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    return true;
}
?>
