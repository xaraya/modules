<?php

function xtasks_remindersapi_getall($args)
{
    extract($args);

    $invalid = array();
    
    if (!isset($ownerid) || !is_numeric($ownerid)) {
        $invalid[] = 'ownerid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'reminders', 'getall', 'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $items = array();

    if (!xarSecurityCheck('UseReminders', 0, 'Item', "All:All:All")) {//TODO: security
        /* FAIL SILENTLY
        $msg = xarML('Not authorized to access #(1) items',
                    'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
        */
        return $items;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $reminderstable = $xartable['xtasks_reminders'];

    $sql = "SELECT reminderid,
                  taskid,
                  ownerid,
                  eventdate,
                  reminder,
                  warning
            FROM $reminderstable
            WHERE ownerid = $ownerid";
            
    $whereclause = array();
    if(!empty($startdate)) {
        $whereclause[] = "DATE_SUB(eventdate, INTERVAL warning MINUTE) >= '".$startdate."'";
    }
    if(!empty($enddate)) {
        $whereclause[] = "eventdate <= '".$enddate."'";
    }
    if(count($whereclause) > 0) {
        $sql .= " AND ".implode(" AND ", $whereclause);
    }
    
    $sql .= " ORDER BY eventdate";

    $result = $dbconn->Execute($sql);

    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($reminderid,
              $taskid,
              $ownerid,
              $eventdate,
              $reminder) = $result->fields;
        if (xarSecurityCheck('UseReminders', 0, 'Item', "All:All:All")) {
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
