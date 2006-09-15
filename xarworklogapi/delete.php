<?php

function xtasks_worklogapi_delete($args)
{
    extract($args);

    if (!isset($worklogid) || !is_numeric($worklogid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'worklogid', 'worklog', 'delete', 'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // does it exist ?
    $item = xarModAPIFunc('xtasks',
                            'worklog',
                            'get',
                            array('worklogid' => $worklogid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('AuditWorklog', 1, 'Item', "All:All:All")) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xtasks', xarVarPrepForStore($worklogid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $worklogtable = $xartable['xtasks_worklog'];

    $sql = "DELETE FROM $worklogtable
            WHERE worklogid = " . $worklogid;
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
