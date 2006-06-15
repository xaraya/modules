<?php

function xproject_userapi_get($args)
{
    extract($args);

    if (!isset($projectid) || !is_numeric($projectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item ID', 'user', 'get', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xprojecttable = $xartable['xProjects'];

    $query = "SELECT projectid,
                  project_name,
                  private,
                  description,
                  clientid,
                  ownerid,
                  status,
                  priority,
                  importance,
                  date_approved,
                  planned_start_date,
                  planned_end_date,
                  actual_start_date,
                  actual_end_date,
                  hours_planned,
                  hours_spent,
                  hours_remaining,
                  associated_sites
            FROM $xprojecttable
            WHERE projectid = ?";
    $result = &$dbconn->Execute($query,array($projectid));

    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    list($projectid,
          $project_name,
          $private,
          $description,
          $clientid,
          $ownerid,
          $status,
          $priority,
          $importance,
          $date_approved,
          $planned_start_date,
          $planned_end_date,
          $actual_start_date,
          $actual_end_date,
          $hours_planned,
          $hours_spent,
          $hours_remaining,
          $associated_sites) = $result->fields;

    $result->Close();

    if (!xarSecurityCheck('ReadXProject', 1, 'Item', "$project_name:All:$projectid")) {
        $msg = xarML('Not authorized to view this project.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'AUTH_FAILED',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $item = array('projectid'           => $projectid,
                  'project_name'        => $project_name,
                  'private'             => $private,
                  'description'         => $description,
                  'clientid'            => $clientid,
                  'ownerid'             => $ownerid,
                  'status'              => $status,
                  'priority'            => $priority,
                  'importance'          => $importance,
                  'date_approved'       => $date_approved == "0000-00-00" ? NULL : $date_approved,
                  'planned_start_date'  => $planned_start_date == "0000-00-00" ? NULL : $planned_start_date,
                  'planned_end_date'    => $planned_end_date == "0000-00-00" ? NULL : $planned_end_date,
                  'actual_start_date'   => $actual_start_date == "0000-00-00" ? NULL : $actual_start_date,
                  'actual_end_date'     => $actual_end_date == "0000-00-00" ? NULL : $actual_end_date,
                  'hours_planned'       => $hours_planned,
                  'hours_spent'         => $hours_spent,
                  'hours_remaining'     => $hours_remaining,
                  'associated_sites'    => $associated_sites);

    return $item;
}

?>