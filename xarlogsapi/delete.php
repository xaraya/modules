<?php

function dossier_logsapi_delete($args)
{
    extract($args);

    if (!isset($logid) || !is_numeric($logid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'worklogid', 'worklog', 'delete', 'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // does it exist ?
    $item = xarModAPIFunc('dossier',
                            'logs',
                            'get',
                            array('logid' => $logid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('MyDossierLog', 1, 'Log', "All:All:All:All")) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xtasks', $logid);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $logstable = $xartable['dossier_logs'];

    $sql = "DELETE FROM $logstable
            WHERE logid = " . $logid;
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    return true;
}

?>
