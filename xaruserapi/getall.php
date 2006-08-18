<?php

/**
 *
 *
 * Administration System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xtasks module
 * @author Chad Kraeft <stego@xaraya.com>
*/
function xtasks_userapi_getall($args)
{
    extract($args);

    if(!empty($modname)) {
        $modid = xarModGetIDFromName($modname);
    }
    
    if (!isset($parentid)
        && !isset($projectid)
        && (!isset($modid) || !isset($objectid))) {
        $parentid = '0';
    }

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
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'getall', 'Example');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if (!xarSecurityCheck('ViewXTask', 0, 'Item', "All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $xtaskstable = $xartable['xtasks'];

    $sql = "SELECT a.taskid,
                   a.objectid,
                   a.modid,
                   a.itemtype,
                   a.parentid,
                   a.projectid,
                   a.task_name,
                   a.status,
                   a.priority,
                   a.importance,
                   a.description,
                   a.private,
                   a.creator,
                   a.owner,
                   a.assigner,
                   a.groupid,
                   a.date_created,
                   a.date_approved,
                   a.date_changed,
                   a.date_start_planned,
                   a.date_start_actual,
                   a.date_end_planned,
                   a.date_end_actual,
                   a.hours_planned,
                   a.hours_spent,
                   a.hours_remaining,
                   COUNT(b.parentid)
            FROM $xtaskstable a
            LEFT JOIN $xtaskstable b
            ON b.parentid = a.taskid
            WHERE 1";

    if (!empty($modid) 
        && !empty($objectid)
        && $modid == xarModGetIDFromName('xtasks')) {
        
        $parentid = $objectid;
    }
            
    if (!empty($parentid)) {
        $sql .= " AND a.parentid=".$parentid;
    }
    
    if (!empty($projectid)) {
        $sql .= " AND a.projectid=".$projectid;
    } elseif (!empty($modid)) {
        $sql .= " OR ( a.modid=".$modid;
        if (!empty($objectid)) {
            $sql .= " AND a.objectid=".$objectid;
        }
        if (!empty($itemtype)) {
            $sql .= " AND a.itemtype=".$itemtype;
        }
        $sql .= " )";
    }
        
//	$sql .= " WHERE $taskcolumn[parentid] = $parentid";
//	$sql .= " AND $taskcolumn[projectid] = $projectid";
//	if($groupid > 0) $sql .= " AND $taskcolumn[groupid] = $groupid";
    $sql .= " GROUP BY b.parentid ";
    $sql .= " ORDER BY a.task_name ";

/*
    if ($selected_project != "all") {
        $sql .= " AND $xtasks_todos_column[project_id]=".$selected_project;

    if (xarSessionGetVar('xtasks_my_tasks') == 1 ) {
        // show only tasks where I'm responsible for
        $query .= "
            AND $xtasks_responsible_persons_column[user_id] = ".xarUserGetVar('uid')."
            AND $xtasks_todos_column[todo_id] = $xtasks_responsible_persons_column[todo_id]";
    }

    // WHERE CLAUSE TO NOT PULL IF TASK IS PRIVATE AND USER IS NOT OWNER, CREATOR, ASSIGNER, OR ADMIN
    // CLAUSE TO FILTER BY STATUS, MIN PRIORITY, OR DATES
    // CLAUSE WHERE USER IS OWNER
    // CLAUSE WHERE USER IS CREATOR
    // CLAUSE WHERE USER IS ASSIGNER
    // CLAUSE FOR ACTIVE ONLY (ie. started but not yet completed)
    // CLAUSE BY TEAM/GROUPID (always on?)
    //
    // CLAUSE TO PULL PARENT TASK SETS
    // or
    // USERAPI_GET FOR EACH PARENT LEVEL
*/

    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR'. $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR: ',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $tasks = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($taskid,
             $objectid,
             $modid,
             $itemtype,
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
             $hours_remaining,
             $numchildren) = $result->fields;
        if (xarSecurityCheck('ReadXTask', 0, 'Item', "$task_name:All:$taskid")) {
            $numtasks = xarModAPIFunc('xtasks', 'user', 'countitems', array('projectid' => $projectid));
            if(!empty($date_created) && $date_created != "0000-00-00") {
                $days_old = sprintf(".01%", (time() - strtotime($date_created) ) / (24 * 60 * 60));
            } else {
                $days_old = 0;
            }
            if(!empty($date_changed) && $date_changed != "0000-00-00") {
                $days_untouched = sprintf(".01%", (time() - strtotime($date_changed) ) / (24 * 60 * 60));
            } else {
                $days_untouched = 0;
            }
            $tasks[] = array('taskid' => $taskid,
                            'objectid' => $objectid,
                            'modid' => $modid,
                            'itemtype' => $itemtype,
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
                            'days_old' => $days_old,
                            'days_untouched' => $days_untouched,
                            'hours_planned' => $hours_planned,
                            'hours_spent' => $hours_spent,
                            'hours_remaining' => $hours_remaining,
                            'numchildren' => $numchildren);
        }
    }

    $result->Close();

    return $tasks;
}

?>