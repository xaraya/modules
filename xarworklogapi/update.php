<?php

function xtasks_worklogapi_update($args)
{
    extract($args);

    $invalid = array();
    if (!isset($reminderid) || !is_numeric($reminderid)) {
        $invalid[] = 'reminder ID';
    }
    if (!isset($reminder_name) || !is_string($reminder_name)) {
        $invalid[] = 'reminder_name';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'reminders', 'update', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('xproject',
                            'reminders',
                            'get',
                            array('reminderid' => $reminderid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditXProject', 1, 'Item', "$item[project_name]:All:$item[projectid]")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $reminderstable = $xartable['xProject_reminders'];

    $query = "UPDATE $reminderstable
            SET reminder_name =?, 
                  status = ?,
                  description = ?,
                  relativeurl = ?
            WHERE reminderid = ?";

    $bindvars = array(
              $reminder_name,
              $status,
              $description,
              $relativeurl,
              $reminderid);
              
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) { // return;
        $msg = xarML('SQL: #(1)',
            $dbconn->ErrorMsg());
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $logdetails = "Page modified: ".$item['reminder_name'].".";
    $logid = xarModAPIFunc('xproject',
                        'log',
                        'create',
                        array('projectid'   => $item['projectid'],
                            'userid'        => xarUserGetVar('uid'),
                            'details'	    => $logdetails,
                            'changetype'	=> "PAGE"));

    return true;
}
?>