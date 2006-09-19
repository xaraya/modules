<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
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

    $result->Close();

    if (!xarSecurityCheck('ReadXProject', 1, 'Item', "$project_name:All:$projectid")) {
        $msg = xarML('Not authorized to view this project.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'AUTH_FAILED',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $item = array('projectid'           => $projectid,
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

    return $item;
}

?>