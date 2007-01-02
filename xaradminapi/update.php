<?php
/**
 * Update an example item
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
/**
 * Update an example item
 *
 * @author the Example module development team
 * @param  $args ['exid'] the ID of the item
 * @param  $args ['name'] the new name of the item
 * @param  $args ['number'] the new number of the item
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xproject_adminapi_update($args)
{
    extract($args);

    if(is_array($associated_sites)) $associated_sites = serialize($associated_sites);

    $invalid = array();
    if (!isset($projectid) || !is_numeric($projectid)) {
        $invalid[] = 'Project ID';
    }
    if (!isset($project_name) || !is_string($project_name)) {
        $invalid[] = 'project_name';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('xproject',
                            'user',
                            'get',
                            array('projectid' => $projectid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditXProject', 1, 'Item', "$item[project_name]:All:$projectid")) {
        return;
    }
    if (!xarSecurityCheck('EditXProject', 1, 'Item', "$project_name:All:$projectid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xprojecttable = $xartable['xProjects'];

    $query = "UPDATE $xprojecttable
              SET project_name =?,
                  reference =?,
                  private = ?,
                  summary = ?,
                  description = ?,
                  clientid = ?,
                  ownerid = ?,
                  status = ?,
                  priority = ?,
                  importance = ?,
                  projecttype = ?,
                  thumbnail = ?,
                  previewimage = ?,
                  previewurl = ?,
                  date_approved = ?,
                  planned_start_date = ?,
                  planned_end_date = ?,
                  actual_start_date = ?,
                  actual_end_date = ?,
                  hours_planned = ?,
                  hours_spent = ?,
                  hours_remaining = ?,
                  estimate = ?,
                  probability = ?,
                  budget = ?,
                  associated_sites = ?
            WHERE projectid = ?";

    $bindvars = array(
              $project_name,
              $reference,
              $private,
              $summary,
              $description,
              $clientid,
              $ownerid,
              $status,
              $priority,
              $importance,
              $projecttype,
              $thumbnail,
              $previewimage,
              $previewurl,
              $date_approved ? $date_approved : NULL,
              $planned_start_date ? $planned_start_date : NULL,
              $planned_end_date ? $planned_end_date : NULL,
              $actual_start_date ? $actual_start_date : NULL,
              $actual_end_date ? $actual_end_date : NULL,
              $hours_planned,
              $hours_spent,
              $hours_remaining,
              $estimate,
              $probability,
              $budget,
              $associated_sites,
              $projectid);
              
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    $userid = xarUserGetVar('uid');
    $logdetails = "Project modified.";
    if($project_name != $item['project_name'])
        $logdetails .= "<br>Project name changed from ".$item['project_name']." to ".$project_name;
    if($importance != $item['importance'])
        $logdetails .= "<br>Project importance changed from ".$item['importance']." to ".$importance;
    if($status != $item['status'])
        $logdetails .= "<br>Project status changed from ".$item['status']." to ".$status;
    if($priority != $item['priority'])
        $logdetails .= "<br>Project priority changed from ".$item['priority']." to ".$priority;
    if($hours_planned != $item['hours_planned']
        || $hours_spent != $item['hours_spent']
        || $hours_remaining != $item['hours_remaining'])
        $logdetails .= "<br>Project hours modified.";
    if($planned_start_date != $item['planned_start_date']
        || $planned_end_date != $item['planned_end_date'])
        $logdetails .= "<br>Planned Project timeframe modified.";
    if($actual_start_date != $item['actual_start_date']
        || $actual_end_date != $item['actual_end_date'])
        $logdetails .= "<br>Actual Project timeframe modified.";

    $logid = xarModAPIFunc('xproject',
                        'log',
                        'create',
                        array('projectid'   => $projectid,
                            'userid'        => $userid,
                            'details'        => $logdetails,
                            'changetype'    => "MODIFIED"));

    $args['module'] = 'xproject';
    $args['itemtype'] = 0;
    $args['itemid'] = $projectid;
    $args['name'] = $project_name;
    xarModCallHooks('item', 'update', $projectid, $args);

    return true;
}
?>
