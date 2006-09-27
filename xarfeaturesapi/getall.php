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
function xproject_featuresapi_getall($args)
{
    extract($args);

    $invalid = array();
    if (!isset($projectid) || !is_numeric($projectid)) {
        $invalid[] = 'projectid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'features', 'getall', 'xProject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
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

    $projectstable = $xartable['xProjects'];
    $featurestable = $xartable['xProject_features'];

    $sql = "SELECT featureid,
                  feature_name,
                  $featurestable.projectid,
                  $projectstable.project_name,
                  $featurestable.importance,
                  details,
                  tech_notes,
                  $featurestable.date_approved,
                  date_available
            FROM $featurestable, $projectstable
            WHERE $projectstable.projectid = $featurestable.projectid
            AND $featurestable.projectid = $projectid";

//    $sql .= " WHERE $taskcolumn[parentid] = $parentid";
//    $sql .= " AND $taskcolumn[projectid] = $projectid";
//    if($groupid > 0) $sql .= " AND $taskcolumn[groupid] = $groupid";
    $sql .= " ORDER BY $featurestable.importance, feature_name";

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

    $result = $dbconn->Execute($sql);

    if (!$result) return;

    $items = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($featureid,
              $feature_name,
              $projectid,
              $project_name,
              $importance,
              $details,
              $tech_notes,
              $date_approved,
              $date_available) = $result->fields;
        if (xarSecurityCheck('ReadXProject', 0, 'Item', "$project_name:All:$projectid")) {
            $items[] = array('featureid'            => $featureid,
                              'feature_name'        => $feature_name,
                              'projectid'           => $projectid,
                              'project_name'        => $project_name,
                              'importance'          => $importance,
                              'details'             => $details,
                              'tech_notes'          => $tech_notes,
                              'date_approved'       => $date_approved == "0000-00-00" ? NULL : $date_approved,
                              'date_available'      => $date_available == "0000-00-00" ? NULL : $date_available);
        }
    }

    $result->Close();

    return $items;
}

?>