<?php

function xproject_featuresapi_get($args)
{
    extract($args);

    if (!isset($featureid) || !is_numeric($featureid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'feature ID', 'user', 'get', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $projectstable = $xartable['xProjects'];
    $featurestable = $xartable['xProject_features'];

    $query = "SELECT featureid,
                  feature_name,
                  $featurestable.projectid,
                  $projectstable.project_name,
                  $featurestable.importance,
                  details,
                  tech_notes,
                  $featurestable.date_approved,
                  date_available
            FROM $featurestable, $projectstable
            WHERE $projectstable.projectid = $featurestable.projectid
            AND featureid = ?";
    $result = &$dbconn->Execute($query,array($featureid));

    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    list($featureid,
          $feature_name,
          $projectid,
          $project_name,
          $importance,
          $details,
          $tech_notes,
          $date_approved,
          $date_available) = $result->fields;

    $result->Close();

    if (!xarSecurityCheck('ReadXProject', 1, 'Item', "$project_name:All:$projectid")) {
        $msg = xarML('Not authorized to view this project.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'AUTH_FAILED',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $item = array('featureid'           => $featureid,
                  'feature_name'        => $feature_name,
                  'projectid'           => $projectid,
                  'project_name'        => $project_name,
                  'importance'          => $importance,
                  'details'             => $details,
                  'tech_notes'          => $tech_notes,
                  'date_approved'       => $date_approved == "0000-00-00" ? NULL : $date_approved,
                  'date_available'      => $date_available == "0000-00-00" ? NULL : $date_available);

    return $item;
}

?>