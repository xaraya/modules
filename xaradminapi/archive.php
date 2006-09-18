<?php

function xproject_adminapi_archive($args)
{
    extract($args);

    $invalid = array();
    if (!isset($projectid) || !is_numeric($projectid)) {
        $invalid[] = 'Project ID';
    }
    if (!isset($actual_end_date) || !is_string($actual_end_date)) {
        $invalid[] = 'actual end date';
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

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xprojecttable = $xartable['xProjects'];

    $query = "UPDATE $xprojecttable
            SET status = ?,
                actual_end_date = ?
            WHERE projectid = ?";

    $bindvars = array(
              "Archive",
              $actual_end_date ? $actual_end_date : NULL,
              $projectid);

    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    $userid = xarUserGetVar('uid');
    $logdetails = "Project archived.";
    if($actual_end_date != $item['actual_end_date'])
        $logdetails .= "<br>Actual Project timeframe modified.";

    $logid = xarModAPIFunc('xproject',
                        'log',
                        'create',
                        array('projectid'   => $projectid,
                            'userid'        => $userid,
                            'details'        => $logdetails,
                            'changetype'    => "ARCHIVED"));

    return true;
}
?>