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

    $invalid = array();
    if (!isset($private)) {
        $private = "";
    }
    if (!isset($startnum) || !is_numeric($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $numitems = -1;
    }

    if (!xarSecurityCheck('ViewXProject', 0, 'Item', "All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $xprojecttable = $xartable['xProjects'];

    $sql = "SELECT projectid,
                  reference,
                  project_name,
                  private,
                  description,
                  clientid,
                  ownerid,
                  status,
                  priority,
                  importance,
                  projecttype,
                  date_approved,
                  planned_start_date,
                  planned_end_date,
                  actual_start_date,
                  actual_end_date,
                  hours_planned,
                  hours_spent,
                  hours_remaining,
                  associated_sites
            FROM $xprojecttable";

//	$sql .= " WHERE $taskcolumn[parentid] = $parentid";
//	$sql .= " AND $taskcolumn[projectid] = $projectid";
	if($private == "public") $sql .= " WHERE private != '1'";
    $sql .= " ORDER BY project_name";

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

    if ($dbconn->ErrorNo() != 0) return;

    $projects = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($projectid,
              $reference,
              $project_name,
              $private,
              $description,
              $clientid,
              $ownerid,
              $status,
              $priority,
              $importance,
              $projecttype,
              $date_approved,
              $planned_start_date,
              $planned_end_date,
              $actual_start_date,
              $actual_end_date,
              $hours_planned,
              $hours_spent,
              $hours_remaining,
              $associated_sites) = $result->fields;
        if (xarSecurityCheck('ReadXProject', 0, 'Item', "$project_name:All:$projectid")) {
            $projects[] = array('projectid'         => $projectid,
                              'reference'           => $reference,
                              'project_name'        => $project_name,
                              'private'             => $private,
                              'description'         => $description,
                              'clientid'            => $clientid,
                              'ownerid'             => $ownerid,
                              'status'              => $status,
                              'priority'            => $priority,
                              'importance'          => $importance,
                              'projecttype'         => $projecttype,
                              'date_approved'       => $date_approved == "0000-00-00" ? NULL : $date_approved,
                              'planned_start_date'  => $planned_start_date == "0000-00-00" ? NULL : $planned_start_date,
                              'planned_end_date'    => $planned_end_date == "0000-00-00" ? NULL : $planned_end_date,
                              'actual_start_date'   => $actual_start_date == "0000-00-00" ? NULL : $actual_start_date,
                              'actual_end_date'     => $actual_end_date == "0000-00-00" ? NULL : $actual_end_date,
                              'hours_planned'       => $hours_planned,
                              'hours_spent'         => $hours_spent,
                              'hours_remaining'     => $hours_remaining,
                              'associated_sites'    => $associated_sites);
        }
    }

    $result->Close();

    return $projects;
}

?>