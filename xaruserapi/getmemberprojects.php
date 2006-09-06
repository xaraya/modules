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
function xproject_userapi_getmemberprojects($args)
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
    if (!isset($clientid) || !is_numeric($clientid)) {
        $clientid = 0;
    }
    if (!isset($max_priority) || !is_numeric($max_priority)) {
        $max_priority = 0;
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

    $invalid = array();
    if (!isset($memberid) || !is_numeric($memberid)) {
        $invalid[] = 'memberid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'feature ID', 'team', 'delete', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
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
    $teamtable = $xartable['xProject_team'];

    $sql = "SELECT a.projectid,
                  a.reference,
                  a.project_name,
                  a.private,
                  a.description,
                  a.clientid,
                  a.ownerid,
                  a.status,
                  a.priority,
                  a.importance,
                  a.projecttype,
                  a.date_approved,
                  a.planned_start_date,
                  a.planned_end_date,
                  a.actual_start_date,
                  a.actual_end_date,
                  a.hours_planned,
                  a.hours_spent,
                  a.hours_remaining,
                  a.estimate,
                  a.budget,
                  a.associated_sites
            FROM $xprojecttable a, $teamtable b
            WHERE b.projectid = a.projectid
            AND b.memberid = $memberid";

//	$sql .= " WHERE $taskcolumn[parentid] = $parentid";
//	$sql .= " AND $taskcolumn[projectid] = $projectid";
	if($private == "public") $sql .= " AND private != '1'";
	if(!empty($status)) $sql .= " AND status = '".$status."'";
	if($clientid > 0) $sql .= " AND clientid = '".$clientid."'";
	if($max_priority > 0) $sql .= " AND priority <= '".$max_priority."'";
	if($max_importance > 0) $sql .= " AND importance <= '".$max_importance."'";
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
                              'estimate'            => $estimate,
                              'budget'              => $budget,
                              'associated_sites'    => $associated_sites);
        }
    }

    $result->Close();

    return $projects;
}

?>