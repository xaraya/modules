<?php

function xtasks_userapi_get($args)
{
    extract($args);

    if (!isset($taskid) || !is_numeric($taskid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item ID', 'user', 'get', 'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xtasks_table = $xartable['xtasks'];

    $query = "SELECT taskid,
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
                   hours_remaining
            FROM $xtasks_table
            WHERE taskid = ?";
    $result = &$dbconn->Execute($query,array($taskid));

    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    list($taskid,
         $parentid,
         $projectid,
         $task_name,
         $status,
         $priority,
         $importance,
         $description,
         $private,
         $creator,
         $owner,
         $assigner,
         $groupid,
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

    $result->Close();

    if (!xarSecurityCheck('ReadXTask', 1, 'Item', "$task_name:All:$taskid")) {
        return;
    }

    $task = array('taskid' => $taskid,
                'parentid' => $parentid,
                'projectid' => $projectid,
                'task_name' => $task_name,
                'status' => $status,
                'priority' => $priority,
                'importance' => $importance,
                'description' => $description,
                'private' => $private,
                'creator' => $creator,
                'owner' => $owner,
                'assigner' => $assigner,
                'groupid' => $groupid,
                'date_created' => $date_created == "0000-00-00" ? "" : $date_created,
                'date_approved' => $date_approved == "0000-00-00" ? "" : $date_approved,
                'date_changed' => $date_changed == "0000-00-00" ? "" : $date_changed,
                'date_start_planned' => $date_start_planned == "0000-00-00" ? "" : $date_start_planned,
                'date_start_actual' => $date_start_actual == "0000-00-00" ? "" : $date_start_actual,
                'date_end_planned' => $date_end_planned == "0000-00-00" ? "" : $date_end_planned,
                'date_end_actual' => $date_end_actual == "0000-00-00" ? "" : $date_end_actual,
                'hours_planned' => $hours_planned,
                'hours_spent' => $hours_spent,
                'hours_remaining' => $hours_remaining);

    return $task;
}

?>