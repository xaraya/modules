<?php

function xtasks_worklogapi_get($args)
{
    extract($args);

    if (!isset($worklogid) || !is_numeric($worklogid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'worklogid', 'worklog', 'get', 'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $worklogtable = $xartable['xtasks_worklog'];

    $query = "SELECT worklogid,
                  taskid,
                  ownerid,
                  eventdate,
                  hours,
                  notes
            FROM $worklogtable
            WHERE worklogid = ?";
    $result = &$dbconn->Execute($query,array($worklogid));

    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    list($worklogid,
          $taskid,
          $ownerid,
          $eventdate,
          $hours,
          $notes) = $result->fields;

    $result->Close();

    if (!xarSecurityCheck('ViewWorklog', 1, 'Item', "All:All:All")) {
        $msg = xarML('Not authorized to view reminders.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'AUTH_FAILED',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    if(preg_match("/<br\\s*?\/??>/i", $notes)) {
        $formatted_notes = $notes;
    } else {
        $formatted_notes = nl2br($notes);
    }
    
    $formatted_notes = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]",
             "<a href=\"\\0\" target=\"new\">\\0</a>", $formatted_notes);

    $item = array('worklogid'       => $worklogid,
                  'taskid'          => $taskid,
                  'ownerid'         => $ownerid,
                  'eventdate'       => $eventdate,
                  'hours'           => $hours,
                  'notes'           => $notes,
                  'formatted_notes' => $formatted_notes);

    return $item;
}

?>