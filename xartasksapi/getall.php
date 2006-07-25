<?php

function xproject_tasksapi_getall($args)
{
    extract($args);
	
    if ($startnum == "") {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    if (!isset($filter)) {
        $filter = 0;
    }
	if(empty($parentid) || !is_numeric($parentid)) $parentid = 0;
	
    $invalid = array();
    if (!isset($projectid) || !is_numeric($projectid)) {
        $invalid[] = 'projectid';
    }
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'tasks', 'getall', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
	
    $tasks = array();

    if (!xarSecAuthAction(0, 'xproject::Tasks', '::', ACCESS_OVERVIEW)) {
        $msg = xarML('Not authorized to access #(1) items',
                    'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $taskstable = $xartable['xproject_tasks'];

    $sql = "SELECT xar_taskid,
                   xar_parentid,
                   xar_projectid,
                   xar_name,
				   xar_status,
				   xar_priority,
				   xar_description,
				   xar_private,
				   xar_creator,
				   xar_owner,
				   xar_assigner,
				   xar_groupid,
				   xar_date_created,
				   xar_date_approved,
				   xar_date_changed,
				   xar_date_start_planned,
				   xar_date_start_actual,
				   xar_date_end_planned,
				   xar_date_end_actual,
				   xar_hours_planned,
				   xar_hours_spent,
				   xar_hours_remaining,
				   xar_cost,
				   xar_recurring,
				   xar_periodicity,
				   xar_reminder
            FROM $taskstable";
			
// IMPLEMENT FILTER CODE FOR WHERE CLAUSE
$userId = xarSessionGetVar('uid');
switch($filter) {
	case 1: // My Tasks
		$sql .= " WHERE xar_projectid = $projectid
				" . ($parentid ? "AND xar_parentid = " . $parentid : "") . "
				AND (xar_creator = " . ($userId ? $userId : "0") . "
					OR xar_owner = " . ($userId ? $userId : "0") . "
					OR xar_assigner = " . ($userId ? $userId : "0") . ")
				AND xar_status = 0
				ORDER BY xar_priority DESC, xar_name";
		break;
	case 2: // Available Tasks
		$sql .= " WHERE xar_projectid = $projectid
				" . ($parentid ? "AND xar_parentid = " . $parentid : "") . "
				AND xar_owner IS NULL
				AND xar_status = 0
				ORDER BY xar_priority DESC, xar_name";
		break;
	case 3: // Priority List
		$sql .= " WHERE xar_projectid = $projectid
				" . ($parentid ? "AND xar_parentid = " . $parentid : "") . "
				AND xar_status = 0
				ORDER BY xar_priority DESC, xar_name";
		break;
	case 4: // Recent Activity
		$sql .= " WHERE xar_projectid = $projectid
				" . ($parentid ? "AND xar_parentid = " . $parentid : "") . "
				ORDER BY xar_status, xar_priority DESC, xar_name";
		break;
	case 5:
		$sql .= " WHERE xar_projectid = $projectid
				" . ($parentid ? "AND xar_parentid = " . $parentid : "") . "
				ORDER BY xar_status, xar_priority DESC, xar_name";
		break;
	case 0:
	default:
		$sql .= " WHERE xar_projectid = $projectid
				AND xar_parentid = " . ($parentid ? $parentid : "0") . "
				ORDER BY xar_status, xar_priority DESC, xar_name";

}			

    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    for (; !$result->EOF; $result->MoveNext()) {
        list($taskid,
			   $parentid,
			   $projectid,
			   $name,
			   $status,
			   $priority,
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
			   $cost,
			   $recurring,
			   $periodicity,
			   $reminder) = $result->fields;
        if (xarSecAuthAction(0, 'xproject::Tasks', "::", ACCESS_READ)) {
			$numsubtasks = xarModAPIFunc('xproject', 'tasks', 'countitems', array('parentid' => $taskid));
            $tasks[] = array('taskid' => $taskid,
                             'parentid' => $parentid,
							 'projectid' => $projectid,
                             'name' => $name,
							 'status' => $status,
							 'priority' => $priority,
							 'description' => $description,
							 'private' => $private,
							 'creator' => $creator,
							 'owner' => $owner,
							 'assigner' => $assigner,
							 'groupid' => $groupid,
							 'date_created' => $date_created,
							 'date_approved' => $date_approved,
							 'date_changed' => $date_changed,
							 'date_start_planned' => $date_start_planned,
							 'date_start_actual' => $date_start_actual,
							 'date_end_planned' => $date_end_planned,
							 'date_end_actual' => $date_end_actual,
							 'hours_planned' => $hours_planned,
							 'hours_spent' => $hours_spent,
							 'hours_remaining' => $hours_remaining,
							 'cost' => $cost,
							 'recurring' => $recurring,
							 'periodicity' => $periodicity,
							 'reminder' => $reminder,
							 'numsubtasks' => $numsubtasks);
        }
    }

    $result->Close();

    return $tasks;
}

?>
