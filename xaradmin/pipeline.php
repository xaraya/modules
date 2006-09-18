<?php

function xproject_admin_pipeline($args)
{
    extract($args);

    $data = array();

//    $data['projects_objectid'] = xarModGetVar('xproject', 'projects_objectid');
//    xarModAPILoad('xprojects', 'user');
    $targetdate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("t"), date("Y")));
    $min_planned_end_date = date("Y-m-d", mktime(0, 0, 0, date("m")-1, date("t"), date("Y")));



    $newprojects = xarModAPIFunc('xproject', 'user', 'getall');

    if (!isset($newprojec) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $pipeline = array();
    foreach($newprojects as $projectinfo) {
        if(isset($projectinfo['status'])) {
            if(isset($pipeline[$projectinfo['status']])) {
                $pipeline[$projectinfo['status']] += $projectinfo['estimate'];
            } else {
                $pipeline[$projectinfo['status']] = $projectinfo['estimate'];
            }
        }
    }

    $data['pipeline'] = $pipeline;

    return $data;
}

?>