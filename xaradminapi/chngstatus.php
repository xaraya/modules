<?php

function xproject_adminapi_chngstatus($args)
{
    extract($args);

    $invalid = array();
    if (!isset($projectid) || !is_numeric($projectid)) {
        $invalid[] = 'project ID';
    }
    if (!isset($newstatus) || !is_string($newstatus)) {
        $invalid[] = 'newstatus';
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

    $project_table = $xartable['xProjects'];

    $query = "UPDATE $project_table
            SET status = ?
            WHERE projectid = ?";

    $bindvars = array($newstatus,$projectid);

    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    $userid = xarUserGetVar('uid');
    $logdetails = "Project modified.";
    $logdetails .= "<br>Project status changed from ".$item['status']." to ".$newstatus;

    $logid = xarModAPIFunc('xproject',
                        'log',
                        'create',
                        array('projectid'   => $projectid,
                            'userid'        => $userid,
                            'details'        => $logdetails,
                            'changetype'    => "MODIFIED"));

    return true;
}
?>