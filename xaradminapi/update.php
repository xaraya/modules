<?php
/**
 * Update an example item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
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

    $invalid = array();
    if (!isset($projectid) || !is_numeric($projectid)) {
        $invalid[] = 'Project ID';
    }
    if (!isset($project_name) || !is_string($project_name)) {
        $invalid[] = 'project_name';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update', 'Example');
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
                  private = ?, 
                  description = ?,
                  clientid = ?,
                  ownerid = ?,
                  status = ?,
                  priority = ?,
                  importance = ?,
                  date_approved = ?,
                  planned_start_date = ?,
                  planned_end_date = ?,
                  actual_start_date = ?,
                  actual_end_date = ?,
                  hours_planned = ?,
                  hours_spent = ?,
                  hours_remaining = ?,
                  associated_sites = ?
            WHERE projectid = ?";

    $bindvars = array(
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
              $associated_sites,
              $projectid);
              
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    $item['module'] = 'xproject';
    $item['itemid'] = $projectid;
    $item['name'] = $project_name;
    xarModCallHooks('item', 'update', $projectid, $item);

    return true;
}
?>