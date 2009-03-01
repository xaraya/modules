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
function xtasks_userapi_getassignments($args)
{
    extract($args);
    
    if(!empty($modname)) {
        $modid = xarModGetIDFromName($modname);
    }
    
    $show_project = xarModGetUserVar('xtasks', 'show_project');
    $show_client = xarModGetUserVar('xtasks', 'show_client');
    
    if (!isset($mode)) {
        $mode = "";
    }
    if (!isset($parentid)
        && !isset($projectid)
        && (!isset($modid) || !isset($objectid))) {
        $parentid = '0';
    }
    if (!isset($orderby)) {
        $orderby = "";
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
    if (!isset($max_priority)) {
        $max_priority = 9;
    }
    if (!isset($max_importance)) {
        $max_importance = 9;
    }
    if (!isset($private)) {
        $private = "";
    }
    if (!isset($q)) {
        $q = "";
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
                    join(', ',$invalid), 'user', 'getall_weighted', 'xtasks');
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
    
    xarModDBInfoLoad('xproject');
    xarModDBInfoLoad('dossier');

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $xtaskstable = $xartable['xtasks'];
    $xprojecttable = $xartable['xProjects'];
    $contactstable = $xartable['dossier_contacts'];
    
    $sql = "SELECT DISTINCT a.owner,
                    d.sortname,
                    d.fname,
                    d.lname
            FROM $xtaskstable a
            LEFT JOIN $contactstable d
            ON d.contactid = a.owner
            WHERE a.owner > 0
            ORDER BY d.fname, d.lname";
//            AND a.status = 'Active'
            
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR'. $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR: ',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $tasks = array();

    $availablestaff = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($taskownerid,
             $taskownername,
             $fname,
             $lname) = $result->fields;
        
        if(!empty($fname) && !empty($lname)) $taskownername = $fname." ".$lname;
        
        $sql2 = "SELECT xp.projectid,
                       xp.project_name,
                       xt.taskid,
                       xt.objectid,
                       xt.modid,
                       xt.itemtype,
                       xt.parentid,
                       xt.dependentid,
                       xt.task_name,
                       xt.status,
                       xp.status AS proj_status,
                       xt.priority,
                       xt.importance,
                       xp.priority as proj_priority,
                       xp.importance as proj_importance,
                       xt.description,
                       xt.private,
                       xt.creator,
                       xt.owner,
                       xt.assigner,
                       xt.groupid,
                       xt.date_created,
                       xt.date_approved,
                       xt.date_changed,
                       xt.date_start_planned,
                       xt.date_start_actual,
                       xt.date_end_planned,
                       xt.date_end_actual,
                      xp.planned_start_date,
                      xp.actual_start_date,
                      xp.planned_end_date,
                      xp.actual_end_date,
                       xt.hours_planned,
                       xt.hours_spent,
                       xt.hours_remaining,
                       (xt.priority * xt.importance * xp.priority * xp.importance)
                FROM $xtaskstable xt, $xprojecttable xp
                WHERE xt.status = 'Active' 
                AND (xp.projectid = xt.projectid
                    OR (xp.projectid = xt.objectid 
                        AND xt.modid = ".xarModGetIDFromName('xproject')."
                        )
                    )
                AND xt.owner = $taskownerid
                ORDER BY (xt.priority * xt.importance * xp.priority * xp.importance)
                ";
//if($taskownerid == 48) die($sql2);
        $result2 = $dbconn->SelectLimit($sql2, 1);
    
        if ($dbconn->ErrorNo() != 0) {
            $msg = xarML('DATABASE_ERROR'. $sql2);
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR: ',
                           new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
            return;
        }
        
        if($result2->EOF) {
            $tasks[] = array('owner' => $taskownerid,
                                'ownername' => $taskownername);
        }
        
        for (; !$result2->EOF; $result2->MoveNext()) {
            list($projectid,
                 $projectname,
                 $taskid,
                 $objectid,
                 $modid,
                 $itemtype,
                 $parentid,
                 $dependentid,
                 $task_name,
                 $status,
                 $proj_status,
                 $priority,
                 $importance,
                 $proj_priority,
                 $proj_importance,
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
                 $project_start_planned,
                 $project_start_actual,
                 $project_end_planned,
                 $project_end_actual,
                 $hours_planned,
                 $hours_spent,
                 $hours_remaining,
                 $taskweight) = $result2->fields;
//            if (xarSecurityCheck('ReadXTask', 0, 'Item', "$task_name:All:$taskid")) {
                if(!empty($date_created) && $date_created != "0000-00-00") {
                    $days_old = sprintf("%01.1f", (time() - strtotime($date_created) ) / (24 * 60 * 60));
                } else {
                    $days_old = 0;
                }
                if(!empty($date_changed) && $date_changed != "0000-00-00") {
                    $days_untouched = sprintf("%01.1f", (time() - strtotime($date_changed) ) / (24 * 60 * 60));
                } else {
                    $days_untouched = 0;
                }
        
                if(preg_match("/<br\\s*?\/??>/i", $description)) {
                    $formatted_desc = $description;
                } else {
                    $formatted_desc = nl2br($description);
                }
                
                $formatted_desc = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]",
                         "<a href=\"\\0\" target=\"new\">\\0</a>", $formatted_desc);
                
                $tasks[] = array('taskid' => $taskid,
                                'objectid' => $objectid,
                                'modid' => $modid,
                                'itemtype' => $itemtype,
                                'parentid' => $parentid,
                                'dependentid' => $dependentid,
                                'projectid' => $projectid,
                                'projectname' => $projectname,
                                'task_name' => $task_name,
                                'status' => $status,
                                'proj_priority' => $proj_priority,
                                'proj_importance' => $proj_importance,
                                'priority' => $priority,
                                'importance' => $importance,
                                'description' => $description,
                                'formatted_desc' => $formatted_desc,
                                'private' => $private,
                                'creator' => $creator,
                                'owner' => $owner,
                                'ownername' => $taskownername,
                                'assigner' => $assigner,
                                'groupid' => $groupid,
                                'date_created' => $date_created == "0000-00-00" ? "" : $date_created,
                                'date_approved' => $date_approved == "0000-00-00" ? "" : $date_approved,
                                'date_changed' => $date_changed == "0000-00-00" ? "" : $date_changed,
                                'date_start_planned' => $date_start_planned == "0000-00-00" ? "" : $date_start_planned,
                                'date_start_actual' => $date_start_actual == "0000-00-00" ? "" : $date_start_actual,
                                'date_end_planned' => $date_end_planned == "0000-00-00" ? "" : $date_end_planned,
                                'date_end_actual' => $date_end_actual == "0000-00-00" ? "" : $date_end_actual,
                                'project_start_planned' => $project_start_planned == "0000-00-00" ? "" : $project_start_planned,
                                'project_start_actual' => $project_start_actual == "0000-00-00" ? "" : $project_start_actual,
                                'project_end_planned' => $project_end_planned == "0000-00-00" ? "" : $project_end_planned,
                                'project_end_actual' => $project_end_actual == "0000-00-00" ? "" : $project_end_actual,
                                'days_old' => $days_old,
                                'days_untouched' => $days_untouched,
                                'hours_planned' => $hours_planned,
                                'hours_spent' => $hours_spent,
                                'hours_remaining' => $hours_remaining,
                                'taskweight' => $taskweight);
//            }
        }
    }
    
    $result->Close();
//echo "<pre>"; print_r($tasks); die("</pre>");
    return $tasks;
}

?>