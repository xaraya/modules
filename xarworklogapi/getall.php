<?php

function xtasks_worklogapi_getall($args)
{
    extract($args);
    
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (empty($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (!isset($projecttype)) {
        $projecttype = "";
    }

    $invalid = array();
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'worklog', 'getall', 'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $items = array();

    if (!xarSecurityCheck('ViewWorklog', 0, 'Item', "All:All:All")) {//TODO: security
        /* FAIL SILENTLY
        $msg = xarML('Not authorized to access #(1) items',
                    'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
        */
        return $items;
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
    } elseif(isset($ttldays) && $ttldays > 0) {
        $whereclause[] = "a.eventdate > '".date("Y-m-d", (time() - ($ttldays * 24 * 3600) ) )."'";
    }
    if(!empty($projectid)) {
        $whereclause[] = "b.projectid = '".$projectid."'";
    }
    if(!empty($projecttype)) {
        $whereclause[] = "c.projecttype = '".$projecttype."'";
    }
    if(count($whereclause) > 0) {
        $sql .= " AND ".implode(" AND ", $whereclause);
    }
    
    $sql .= " ORDER BY a.eventdate DESC";
//die("sql: ".$sql);
    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1);

    if (!$result) return;

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
    
        if(preg_match("/<br\\s*?\/??>/i", $notes)) {
            $formatted_notes = $notes;
        } else {
            $formatted_notes = nl2br($notes);
        }
        
//        $formatted_notes = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]",
//                 "<a href=\"\\0\" target=\"new\">\\0</a>", $formatted_notes);
                 
        $items[] = array('worklogid'        => $worklogid,
                          'taskid'          => $taskid,
                          'taskname'        => $taskname,
                          'projectid'       => $projectid,
                          'project_name'    => $project_name,
                          'clientid'        => $clientid,
                          'ownerid'         => $ownerid,
                          'eventdate'       => $eventdate,
                          'hours'           => $hours,
                          'notes'           => $notes,
                          'formatted_notes' => $formatted_notes);
    }

    $result->Close();

    return $items;
}

?>
