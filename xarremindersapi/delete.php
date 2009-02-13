<?php

function xtasks_remindersapi_delete($args)
{
    extract($args);

    if (!isset($reminderid) || !is_numeric($reminderid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'reminder ID', 'reminders', 'delete', 'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // does it exist ?
    $item = xarModAPIFunc('xtasks',
                            'reminders',
                            'get',
                            array('reminderid' => $reminderid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('UseReminders', 1, 'Item', "All:All:All")) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xtasks', $projectid);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $reminderstable = $xartable['xtasks_reminders'];

    // does it have children ?
    $sql = "DELETE FROM $reminderstable
            WHERE reminderid = " . $reminderid;
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
