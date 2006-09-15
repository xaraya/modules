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
    if (!isset($q)) {
        $q = "";
    }
    if (!isset($sortby)) {
        $sortby = "";
    }
    if (!isset($status)) {
        $status = "";
    }
    if (!isset($clientid) || !is_numeric($clientid)) {
        $clientid = 0;
    }
    if (!isset($max_priority) || !is_numeric($max_priority)) {
        $max_priority = 0;
    }
    if (!isset($projecttype)) {
        $projecttype = "";
    }
    if (!isset($max_importance) || !is_numeric($max_importance)) {
        $max_importance = 0;
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
                  estimate,
                  budget,
                  associated_sites
            FROM $xprojecttable
            WHERE projectid > 0 ";

//	$sql .= " WHERE $taskcolumn[parentid] = $parentid";
//	$sql .= " AND $taskcolumn[projectid] = $projectid";
	if($private == "public") $sql .= " AND private != '1'";
    
	if($status == "New") { 
        $sql .= " AND status NOT IN ('Draft','Closed Won','Closed Lost', 'R & D','Hold','Active','Archive')";
    } elseif(!empty($status)) {
        $sql .= " AND status = '".$status."'";
    }
	
    if($clientid > 0) $sql .= " AND clientid = '".$clientid."'";
	if(!empty($projecttype)) $sql .= " AND projecttype = '".$projecttype."'";
	if($max_priority > 0) $sql .= " AND priority <= '".$max_priority."'";
	if($max_importance > 0) $sql .= " AND importance <= '".$max_importance."'";
	if(!empty($planned_end_date)) $sql .= " AND planned_end_date <= '".$planned_end_date."'";
	if(!empty($min_planned_end_date)) $sql .= " AND planned_end_date >= '".$min_planned_end_date."'";
    if(!empty($q)) {
        $sql .= " AND (project_name LIKE '%".$q."%'
                    OR description LIKE '%".$q."%')";
    }    
    switch($sortby) {
        case "importance":
            $sql .= " ORDER BY importance";
            break;
        case "priority":
            $sql .= " ORDER BY priority";
            break;
        case "status":
            $sql .= " ORDER BY status";
            break;
        case "planned_end_date":
            $sql .= " ORDER BY planned_end_date DESC";
            break;
        case "project_name":
        default:
            $sql .= " ORDER BY project_name";
    }
    
//die($sql);
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
              $estimate,
              $budget,
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
                              'estimate'            => sprintf("%.2f", $estimate),
                              'budget'              => sprintf("%.2f", $budget),
                              'associated_sites'    => $associated_sites);
        }
    }

    $result->Close();

    return $projects;
}

?>