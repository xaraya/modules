<?php

function dossier_remindersapi_delete($args)
{
    extract($args);

    if (!isset($reminderid) || !is_numeric($reminderid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'reminder ID', 'reminders', 'delete', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // does it exist ?
    $item = xarModAPIFunc('dossier',
                            'reminders',
                            'get',
                            array('reminderid' => $reminderid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    $contactinfo = xarModAPIFunc('dossier',
                            'user',
                            'get',
                            array('contactid' => $contactid));

    if (!isset($contactinfo) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('UseDossierReminders', 1, 'Reminders', $contactinfo['cat_id'].":".$contactinfo['userid'].":".$contactinfo['company'].":".$contactinfo['agentuid'])) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'dossier', $projectid);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $reminderstable = $xartable['dossier_reminders'];

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
