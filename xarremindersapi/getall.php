<?php

function xtasks_remindersapi_getall($args)
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
    $reminderstable = $xartable['xProject_reminders'];

    $sql = "SELECT reminderid,
                  reminder_name,
                  $reminderstable.projectid,
                  $projectstable.project_name,
                  sequence,
                  $reminderstable.status,
                  $reminderstable.description,
                  relativeurl
            FROM $reminderstable, $projectstable
            WHERE $projectstable.projectid = $reminderstable.projectid
            AND $reminderstable.projectid = $projectid
            ORDER BY sequence, reminder_name";

    $result = $dbconn->Execute($sql);

    if (!$result) return;
    
    $items = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($reminderid,
              $reminder_name,
              $projectid,
              $project_name,
              $sequence,
              $status,
              $description,
              $relativeurl) = $result->fields;
        if (xarSecurityCheck('ReadXProject', 0, 'Item', "$project_name:All:$projectid")) {
            $items[] = array('reminderid'            => $reminderid,
                              'reminder_name'        => $reminder_name,
                              'projectid'        => $projectid,
                              'project_name'     => $project_name,
                              'status'           => $status,
                              'sequence'         => $sequence,
                              'description'      => $description,
                              'relativeurl'      => $relativeurl);
        }
    }

    $result->Close();

    return $items;
}

?>