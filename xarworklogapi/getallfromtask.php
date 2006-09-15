<?php

function xtasks_worklogapi_getallfromtask($args)
{
    extract($args);

    $invalid = array();
    if (!isset($taskid) || !is_numeric($taskid)) {
        $invalid[] = 'taskid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'worklog', 'getall', 'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if (!xarSecurityCheck('ViewWorklog', 0, 'Item', "All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $worklogtable = $xartable['xtasks_worklog'];

    $sql = "SELECT worklogid,
                  taskid,
                  ownerid,
                  eventdate,
                  hours,
                  notes
            FROM $worklogtable";
            
    $whereclause = array();
    if(!empty($taskid)) {
        $whereclause[] = "taskid = '".$taskid."'";
    }
    if(!empty($ownerid)) {
        $whereclause[] = "ownerid = '".$ownerid."'";
    }
    if(!empty($eventdate)) {
        $whereclause[] = "eventdate > '".$eventdate."'";
    }
    if(count($whereclause) > 0) {
        $sql .= " WHERE ".implode(" AND ", $whereclause);
    }
    
    $sql .= " ORDER BY eventdate";

    $result = $dbconn->Execute($sql);

    if (!$result) return;
    
    $items = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($worklogid,
              $taskid,
              $ownerid,
              $eventdate,
              $hours,
              $notes) = $result->fields;
        $items[] = array('worklogid'    => $worklogid,
                          'taskid'      => $taskid,
                          'ownerid'     => $ownerid,
                          'eventdate'   => $eventdate,
                          'hours'       => $hours,
                          'notes'       => $notes);
    }

    $result->Close();

    return $items;
}

?>