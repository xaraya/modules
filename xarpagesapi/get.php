<?php

function xproject_pagesapi_get($args)
{
    extract($args);

    if (!isset($pageid) || !is_numeric($pageid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'feature ID', 'user', 'get', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $projectstable = $xartable['xProjects'];
    $pagestable = $xartable['xProject_pages'];

    $query = "SELECT pageid,
                  parentid,
                  page_name,
                  $pagestable.projectid,
                  $projectstable.project_name,
                  $pagestable.status,
                  sequence,
                  $pagestable.description,
                  relativeurl
            FROM $pagestable, $projectstable
            WHERE $projectstable.projectid = $pagestable.projectid
            AND pageid = ?";
    $result = &$dbconn->Execute($query,array($pageid));

    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    list($pageid,
          $parentid,
          $page_name,
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

    $item = array('pageid'              => $pageid,
                  'parentid'            => $parentid,
                  'page_name'           => $page_name,
                  'projectid'           => $projectid,
                  'project_name'        => $project_name,
                  'status'              => $status,
                  'sequence'            => $sequence,
                  'description'         => $description,
                  'relativeurl'         => $relativeurl);

    return $item;
}

?>