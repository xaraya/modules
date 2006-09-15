<?php

function xtasks_worklogapi_getall($args)
{
    extract($args);

    $invalid = array();
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
    
    if(!empty($maxdate) && !empty($ttldays)) {
        $mindate = date("Y-m-d", strtotime($maxdate) - ($ttldays * 3600 * 24) );
    }
    
    xarModAPILoad('xproject', 'user');
    
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $xprojecttable = $xartable['xProjects'];
    $taskstable = $xartable['xtasks'];
    $worklogtable = $xartable['xtasks_worklog'];

    $sql = "SELECT a.worklogid,
                  a.taskid,
                  b.task_name,
                  b.projectid,
                  c.project_name,
                  c.clientid,
                  a.ownerid,
                  a.eventdate,
                  a.hours,
                  a.notes
            FROM $worklogtable a, $taskstable b
            LEFT JOIN $xprojecttable c
            ON c.projectid = b.projectid
            WHERE a.taskid = b.taskid";
            
    $whereclause = array();
    if(!empty($ownerid)) {
        $whereclause[] = "a.ownerid = '".$ownerid."'";
    }
    if(!empty($maxdate)) {
        $whereclause[] = "a.eventdate < '".$maxdate."'";
    }
    if(!empty($mindate)) {
        $whereclause[] = "a.eventdate > '".$mindate."'";
    }
    if(count($whereclause) > 0) {
        $sql .= " WHERE ".implode(" AND ", $whereclause);
    }
    
    $sql .= " ORDER BY a.eventdate DESC";

    $result = $dbconn->Execute($sql);

    if (!$result) return;
    
    $items = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($worklogid,
              $taskid,
              $taskname,
              $projectid,
              $project_name,
              $clientid,
              $ownerid,
              $eventdate,
              $hours,
              $notes) = $result->fields;
        $items[] = array('worklogid'        => $worklogid,
                          'taskid'          => $taskid,
                          'taskname'        => $taskname,
                          'projectid'       => $projectid,
                          'project_name'    => $project_name,
                          'clientid'        => $clientid,
                          'ownerid'         => $ownerid,
                          'eventdate'       => $eventdate,
                          'hours'           => $hours,
                          'notes'           => $notes);
    }

    $result->Close();

    return $items;
}

?>