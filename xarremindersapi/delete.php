<?php

function xtasks_remindersapi_delete($args)
{
    extract($args);

    if (!isset($reminderid) || !is_numeric($reminderid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'feature ID', 'features', 'delete', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // does it exist ?
    $item = xarModAPIFunc('xproject',
                            'reminders',
                            'get',
                            array('reminderid' => $reminderid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('DeleteXProject', 1, 'Item', "$item[project_name]:All:$item[projectid]")) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xproject', xarVarPrepForStore($projectid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $reminderstable = $xartable['xProject_reminders'];

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
    
    xarModAPIFunc('xproject', 'reminders', 'sequence', array('projectid' => $item['projectid']));

    $logdetails = "Page removed: ".$item['reminder_name'].".";
    $logid = xarModAPIFunc('xproject',
                        'log',
                        'create',
                        array('projectid'   => $item['projectid'],
                            'userid'        => xarUserGetVar('uid'),
                            'details'	    => $logdetails,
                            'changetype'	=> "PAGE"));

    // Let the calling process know that we have finished successfully
    return true;
}

?>
