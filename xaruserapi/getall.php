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
    
    $draftstatus = xarModGetVar('xproject', 'draftstatus');
    $activestatus = xarModGetVar('xproject', 'activestatus');
    $archivestatus = xarModGetVar('xproject', 'archivestatus');

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
                  summary,
                  description,
                  clientid,
                  ownerid,
                  status,
                  priority,
                  importance,
                  projecttype,
                  haspages,
                  thumbnail,
                  previewimage,
                  previewurl,
                  date_approved,
                  planned_start_date,
                  planned_end_date,
                  actual_start_date,
                  actual_end_date,
                  hours_planned,
                  hours_spent,
                  hours_remaining,
                  estimate,
                  probability,
                  budget,
                  associated_sites
            FROM $xprojecttable
            WHERE projectid > 0 ";

//    $sql .= " WHERE $taskcolumn[parentid] = $parentid";
//    $sql .= " AND $taskcolumn[projectid] = $projectid";
    if($private == "public") $sql .= " AND private != '1'";

    if($status == "New") {
        $sql .= " AND status NOT IN ('".$draftstatus."','Closed Won','Closed Lost', 'R & D','Hold','".$activestatus."','".$archivestatus."')";
    } elseif($status == "Hold") {
        $sql .= " AND status NOT IN ('".$draftstatus."','".$activestatus."','".$archivestatus."')";
    } elseif(!empty($status)) {
        $sql .= " AND status = '".$status."'";
    } else {
        $sql .= " AND status != '".$archivestatus."'";
    }

    if($clientid > 0) $sql .= " AND clientid = '".$clientid."'";
    if(!empty($projecttype)) $sql .= " AND projecttype = '".$projecttype."'";
    if($max_priority > 0) $sql .= " AND priority <= '".$max_priority."'";
    if($max_importance > 0) $sql .= " AND importance <= '".$max_importance."'";
    
    /* differentiate between what a planned_end_date being filter by implies by
        using a separate variable name to handle it as the earliest, or minimum, value.
        Otherwise, use filter as latest value, as a constraint */
    if(!empty($planned_end_date)) $sql .= " AND planned_end_date <= '".$planned_end_date."'";
    if(!empty($min_planned_end_date)) $sql .= " AND planned_end_date >= '".$min_planned_end_date."'";
    if(!empty($q)) {
        $sql .= " AND (project_name LIKE '%".$q."%'
                    OR description LIKE '%".$q."%'
                    OR reference LIKE '%".$q."%')";
    }
    switch($sortby) {
        case "status":
            $sql .= " ORDER BY status, importance, priority, project_name";
            break;
        case "projecttype":
            $sql .= " ORDER BY projecttype, status, importance, priority";
            break;
        case "planned_end_date":
            $sql .= " ORDER BY planned_end_date DESC, project_name";
            break;
        case "project_name":
            $sql .= " ORDER BY project_name";
            break;
        case "priority":
            $sql .= " ORDER BY priority, importance, project_name";
            break;
        case "importance":
        default:
            $sql .= " ORDER BY importance, priority, project_name";
            break;
    }

//die $sql;
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
              $summary,
              $description,
              $clientid,
              $ownerid,
              $status,
              $priority,
              $importance,
              $projecttype,
              $haspages,
              $thumbnail,
              $previewimage,
              $previewurl,
              $date_approved,
              $planned_start_date,
              $planned_end_date,
              $actual_start_date,
              $actual_end_date,
              $hours_planned,
              $hours_spent,
              $hours_remaining,
              $estimate,
              $probability,
              $budget,
              $associated_sites) = $result->fields;
        if (xarSecurityCheck('ReadXProject', 0, 'Item', "$project_name:All:$projectid")) {
        
            if(preg_match("/<br\\s*?\/??>/i", $description)) {
                $formatted_desc = $description;
            } else {
                $formatted_desc = nl2br($description);
            }
        
            $projects[] = array('projectid'         => $projectid,
                              'reference'           => $reference,
                              'project_name'        => $project_name,
                              'private'             => $private,
                              'summary'             => $summary,
                              'description'         => $description,
                              'formatted_desc'      => $formatted_desc,
                              'clientid'            => $clientid,
                              'ownerid'             => $ownerid,
                              'status'              => $status,
                              'priority'            => $priority,
                              'importance'          => $importance,
                              'projecttype'         => $projecttype,
                              'haspages'            => $haspages,
                              'thumbnail'           => $thumbnail,
                              'previewimage'        => $previewimage,
                              'previewurl'          => $previewurl,
                              'date_approved'       => $date_approved == "0000-00-00" ? NULL : $date_approved,
                              'planned_start_date'  => $planned_start_date == "0000-00-00" ? NULL : $planned_start_date,
                              'planned_end_date'    => $planned_end_date == "0000-00-00" ? NULL : $planned_end_date,
                              'actual_start_date'   => $actual_start_date == "0000-00-00" ? NULL : $actual_start_date,
                              'actual_end_date'     => $actual_end_date == "0000-00-00" ? NULL : $actual_end_date,
                              'hours_planned'       => $hours_planned,
                              'hours_spent'         => $hours_spent,
                              'hours_remaining'     => $hours_remaining,
                              'estimate'            => sprintf("%.2f", $estimate),
                              'formatted_estimate'  => number_format($estimate, 2),
                              'probability'         => $probability,
                              'budget'              => sprintf("%.2f", $budget),
                              'formatted_budget'    => number_format($budget, 2),
                              'associated_sites'    => $associated_sites);
        }
    }

    $result->Close();

    return $projects;
}

?>