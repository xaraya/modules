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
 * @subpackage xproject module
 * @author Chad Kraeft <stego@xaraya.com>
*/
function xproject_userapi_getall($args)
{
    extract($args);

//	if (empty($parentid)) $parentid = 0;
	
//	if ($projectid <= 0) $projectid = xarModGetVar('xproject','private');
	
//	if (empty($groupid)) $groupid = 0;
	
    if ($startnum == "") {
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
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
	
    $tasks = array();

    if (!xarSecAuthAction(0, 'xproject::', '::', ACCESS_OVERVIEW)) {
        $msg = xarML('Not authorized to access #(1) items',
                    'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xprojecttable = $xartable['xproject'];

    $sql = "SELECT xar_projectid,
                   xar_name,
                   xar_description,
                   xar_usedatefields,
				   xar_usehoursfields,
				   xar_usefreqfields,
				   xar_allowprivate,
				   xar_importantdays,
				   xar_criticaldays,
				   xar_sendmailfreq,
				   xar_billable
            FROM $xprojecttable";

//	$sql .= " WHERE $taskcolumn[parentid] = $parentid";
//	$sql .= " AND $taskcolumn[projectid] = $projectid";
//	if($groupid > 0) $sql .= " AND $taskcolumn[groupid] = $groupid";
    $sql .= " ORDER BY xar_name";

/*
    if ($selected_project != "all") {
        $sql .= " AND $xproject_todos_column[project_id]=".$selected_project;
		
	if (xarSessionGetVar('xproject_my_tasks') == 1 ) {
        // show only tasks where I'm responsible for
        $query .= " 
            AND $xproject_responsible_persons_column[user_id] = ".xarUserGetVar('uid')."
            AND $xproject_todos_column[todo_id] = $xproject_responsible_persons_column[todo_id]";
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
        $msg = xarML('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    for (; !$result->EOF; $result->MoveNext()) {
        list($projectid,
			$name, 
			$description, 
			$usedatefields, 
			$usehoursfields, 
			$usefreqfields, 
			$allowprivate, 
			$importantdays, 
			$criticaldays, 
			$sendmailfreq, 
			$billable) = $result->fields;
        if (xarSecAuthAction(0, 'xproject::', "$name::$projectid", ACCESS_READ)) {
			$numtasks = xarModAPIFunc('xproject', 'tasks', 'countitems', array('projectid' => $projectid));
            $tasks[] = array('projectid' => $projectid,
                             'name' => $name,
							 'description' => $description,
                             'usedatefields' => ($usedatefields ? "*" : ""),
							 'usehoursfields' => ($usehoursfields ? "*" : ""),
							 'usefreqfields' => ($usefreqfields ? "*" : ""),
							 'allowprivate' => ($allowprivate ? "*" : ""),
							 'importantdays' => $importantdays,
							 'criticaldays' => $criticaldays,
							 'sendmailfreq' => $sendmailfreq,
							 'billable' => ($billable ? "*" : ""),
							 'numtasks' => ($numtasks ? $numtasks : 0));
        }
    }

    $result->Close();

    return $tasks;
}

?>