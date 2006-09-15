<?php

function xtasks_remindersapi_getall($args)
{
    extract($args);

    $invalid = array();
    if (!isset($taskid) || !is_numeric($taskid)) {
        $invalid[] = 'taskid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'reminders', 'getall', 'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if (!xarSecurityCheck('ViewReminders', 0, 'Item', "All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $reminderstable = $xartable['xtasks_reminders'];

    $sql = "SELECT reminderid,
                  taskid,
                  ownerid,
                  eventdate,
                  reminder
            FROM $reminderstable
            WHERE taskid = $taskid
            ORDER BY eventdate";

    $result = $dbconn->Execute($sql);

    if (!$result) return;
    
    $items = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($reminderid,
              $taskid,
              $ownerid,
              $eventdate,
              $reminder) = $result->fields;
        if (xarSecurityCheck('ViewReminders', 0, 'Item', "All:All:All")) {
            $items[] = array('reminderid'       => $reminderid,
                              'taskid'          => $taskid,
                              'ownerid'         => $ownerid,
                              'eventdate'       => $eventdate,
                              'reminder'        => $reminder);
        }
    }

    $result->Close();

    return $items;
}

?>