<?php
 
function xtasks_adminapi_pnupgrade($args)
{
    extract($args);

    if (!xarSecurityCheck('AdminXTask', 1, 'Item', "All:All:All")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $xtaskstable = $xartable['xtasks'];
    
    $sql = "SELECT pn_id,
                   pn_parentid,
                   pn_name,
                   pn_status,
                   pn_priority,
                   pn_description,
                   pn_date_created,
                   pn_date_approved,
                   pn_date_changed,
                   pn_date_start_planned,
                   pn_date_start_actual,
                   pn_date_end_planned,
                   pn_date_end_actual,
                   pn_hours_planned,
                   pn_hours_spent,
                   pn_hours_remaining
            FROM nuke_xTasks
            ORDER BY pn_parentid, pn_id";
              
    $result = &$dbconn->Execute($sql);

    if (!$result) return;

    if ($result->EOF) {
        $msg = xarML('NOITEMS'. $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR: ',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $tasklist = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($oldtaskid,
             $oldparentid,
             $task_name,
             $status,
             $priority,
             $description,
             $date_created,
             $date_approved,
             $date_changed,
             $date_start_planned,
             $date_start_actual,
             $date_end_planned,
             $date_end_actual,
             $hours_planned,
             $hours_spent,
             $hours_remaining) = $result->fields;

        $nextId = $dbconn->GenId($xtaskstable);
    
        $query = "INSERT INTO $xtaskstable (
                      taskid,
                      objectid,
                      modid,
                      itemtype,
                      parentid,
                      projectid,
                      task_name,
                      status,
                      priority,
                      importance,
                      description,
                      private,
                      creator,
                      owner,
                      assigner,
                      groupid,
                      date_created,
                      date_approved,
                      date_changed,
                      date_start_planned,
                      date_start_actual,
                      date_end_planned,
                      date_end_actual,
                      hours_planned,
                      hours_spent,
                      hours_remaining)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NULL,?,?,?,?,?,?,?,?)";
                
        $bindvars = array(
                        $nextId,
                        0,
                        704,
                        1,
                        0,
                        0,
                        $task_name,
                        $status ? "Closed" : "Active",
                        $priority,
                        5,
                        $description,
                        1,
                        3,
                        3,
                        3,
                        0,
                        $date_created ? "FROM_UNIXTIME(".$date_created.")" : NULL,
                        $date_changed ? "FROM_UNIXTIME(".$date_changed.")" : NULL,
                        $date_start_planned ? "FROM_UNIXTIME(".$date_start_planned.")" : NULL,
                        $date_start_actual ? "FROM_UNIXTIME(".$date_start_actual.")" : NULL,
                        $date_end_planned ? "FROM_UNIXTIME(".$date_end_planned.")" : NULL,
                        $date_end_actual ? "FROM_UNIXTIME(".$date_end_actual.")" : NULL,
                        $hours_planned,
                        $hours_spent,
                        $hours_remaining);
        $result2 = &$dbconn->Execute($query,$bindvars);
        if (!$result2) return;
        
        $taskid = $dbconn->PO_Insert_ID($xtaskstable, 'taskid');
             
        $tasklist[$oldtaskid] = array('taskid' => $taskid,
                        'oldtaskid' => $oldtaskid,
                        'oldparentid' => $oldparentid,
                        'task_name' => $task_name,
                        'status' => $status,
                        'priority' => $priority,
                        'description' => $description,
                        'date_created' => $date_created,
                        'date_approved' => $date_approved,
                        'date_changed' => $date_changed,
                        'date_start_planned' => $date_start_planned,
                        'date_start_actual' => $date_start_actual,
                        'date_end_planned' => $date_end_planned,
                        'date_end_actual' => $date_end_actual,
                        'hours_planned' => $hours_planned,
                        'hours_spent' => $hours_spent,
                        'hours_remaining' => $hours_remaining);
    }
    $output = "";
    $ttlparents = 0;
    foreach($tasklist as $taskinfo) {
        if($taskinfo['oldparentid'] > 0) {

            $query = "UPDATE $xtaskstable
                    SET parentid = ?
                    WHERE taskid = ?";
        
            $bindvars = array(
                            $tasklist[$taskinfo['oldparentid']]['taskid'],
                            $tasklist[$taskinfo['oldtaskid']]['taskid']);
                      
            $result = &$dbconn->Execute($query,$bindvars);
        $output .= "<br />taskid: ".$taskinfo['oldtaskid']." -> ".$taskinfo['taskid'].", parentid: ".$taskinfo['oldparentid']." -> ".$tasklist[$taskinfo['oldparentid']]['taskid'];
            if (!$result) return;
    
            $ttlparents++;
            
        }
    }
    die("ttl parents: ".$ttlparents."<br />".$output);
}
?>