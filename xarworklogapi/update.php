<?php

function xtasks_worklogapi_update($args)
{
    extract($args);

    $invalid = array();
    if (!isset($worklogid) || !is_numeric($worklogid)) {
        $invalid[] = 'worklogid';
    }
    if (!isset($eventdate) || !is_string($eventdate)) {
        $invalid[] = 'eventdate';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'worklog', 'update', 'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('xtasks',
                            'worklog',
                            'get',
                            array('worklogid' => $worklogid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('AuditWorklog', 1, 'Item', "All:All:All")) {
        return;
    }
    
    $offset = xarMLS_userOffset($eventdate);
    $eventdate = date("Y-m-d H:i:s", strtotime($eventdate) - ($offset * 3600));

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $worklogtable = $xartable['xtasks_worklog'];

    $query = "UPDATE $worklogtable
            SET eventdate = ?, 
                  hours = ?,
                  notes = ?
            WHERE worklogid = ?";

    $bindvars = array(
              $eventdate,
              $hours,
              $notes,
              $worklogid);
              
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) { // return;
        $msg = xarML('SQL: #(1)',
            $dbconn->ErrorMsg());
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    
    $taskinfo = xarModAPIFunc('xtasks', 'user', 'get', array('taskid' => $item['taskid']));
    
    $hours_remaining = $taskinfo['hours_remaining'];

    xarModAPIFunc('xtasks', 'admin', 'updatehours',
                array('taskid' => $taskinfo['taskid'],
                    'hours_spent' => $hours,
                    'hours_remaining' => $hours_remaining));

    return true;
}
?>