<?php

function xtasks_remindersapi_get($args)
{
    extract($args);

    if (!isset($reminderid) || !is_numeric($reminderid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'feature ID', 'user', 'get', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $projectstable = $xartable['xProjects'];
    $reminderstable = $xartable['xProject_reminders'];

    $query = "SELECT reminderid,
                  reminder_name,
                  $reminderstable.projectid,
                  $projectstable.project_name,
                  $reminderstable.status,
                  sequence,
                  $reminderstable.description,
                  relativeurl
            FROM $reminderstable, $projectstable
            WHERE $projectstable.projectid = $reminderstable.projectid
            AND reminderid = ?";
    $result = &$dbconn->Execute($query,array($reminderid));

    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    list($reminderid,
          $reminder_name,
          $projectid,
          $project_name,
          $status,
          $sequence,
          $description,
          $relativeurl) = $result->fields;

    $result->Close();

    if (!xarSecurityCheck('ReadXProject', 1, 'Item', "$project_name:All:$projectid")) {
        $msg = xarML('Not authorized to view this project.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'AUTH_FAILED',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $item = array('reminderid'              => $reminderid,
                  'reminder_name'           => $reminder_name,
                  'projectid'           => $projectid,
                  'project_name'        => $project_name,
                  'status'              => $status,
                  'sequence'            => $sequence,
                  'description'         => $description,
                  'relativeurl'         => $relativeurl);

    return $item;
}

?>