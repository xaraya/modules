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
/**
 * Administration System
 *
 * @author Chad Kraeft <stego@xaraya.com>
 */
function xproject_adminapi_create($args)
{
    extract($args);

    if(isset($associated_sites) && is_array($associated_sites)) $associated_sites = serialize($associated_sites);

    if (!isset($clientid)) {
        $clientid = 0;
    }

    $invalid = array();
    if (!isset($project_name) || !is_string($project_name) || empty($project_name)) {
        $invalid[] = 'project_name';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'create', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('AddXProject', 1, 'Item', "$project_name:All:All")) {
        $msg = xarML('Not authorized to add #(1) items',
                    'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $xprojecttable = $xartable['xProjects'];

    $nextId = $dbconn->GenId($xprojecttable);

    $query = "INSERT INTO $xprojecttable (
                  projectid,
                  project_name,
                  reference,
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
                  associated_sites)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $bindvars = array(
              $nextId,
              $project_name,
              $reference,
              $private ? $private : "",
              $description,
              $clientid,
              $ownerid,
              $status,
              $priority,
              $importance,
              $projecttype,
              $date_approved ? $date_approved : NULL,
              $planned_start_date ? $planned_start_date : NULL,
              $planned_end_date ? $planned_end_date : NULL,
              $actual_start_date ? $actual_start_date : NULL,
              $actual_end_date ? $actual_end_date : NULL,
              $hours_planned,
              $hours_spent,
              $hours_remaining,
              $estimate,
              $budget,
              $associated_sites ? $associated_sites : "");

    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

    $projectid = $dbconn->PO_Insert_ID($xprojecttable, 'xar_projectid');

    $logdetails = "Project created.";
    $logid = xarModAPIFunc('xproject',
                        'log',
                        'create',
                        array('projectid'   => $projectid,
                            'userid'        => xarUserGetVar('uid'),
                            'details'        => $logdetails,
                            'changetype'    => "CREATED"));

    $item = $args;
    $item['module'] = 'xproject';
    $item['itemid'] = $projectid;
    xarModCallHooks('item', 'create', $projectid, $item);

    return $projectid;
}

?>