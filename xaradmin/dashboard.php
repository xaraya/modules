<?php

function xproject_admin_dashboard($args)
{
    extract($args);
    
    $data = array();
    
//    $data['projects_objectid'] = xarModGetVar('xproject', 'projects_objectid');
//    xarModAPILoad('xprojects', 'user');
    $targetdate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("t"), date("Y")));
    $min_planned_end_date = date("Y-m-d", mktime(0, 0, 0, date("m")-1, date("t"), date("Y")));
    $activeprojects = xarModAPIFunc('xproject', 'user', 'getall',
                            array('sortby' => "planned_end_date",
                                'status' => "Active",
                                'planned_end_date' => $targetdate,
                                'min_planned_end_date' => $min_planned_end_date));
    
    if (!isset($activeprojects) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    $data['activeprojects'] = $activeprojects;
    
    
    $newprojects = xarModAPIFunc('xproject', 'user', 'getall',
                            array('status' => "New"));
    
    if (!isset($newprojects) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    $newprojectlist = array();
    foreach($newprojects as $projectinfo) {
        $newprojectlist[$projectinfo['ownerid']][] = $projectinfo;
    }
    
    $data['newprojectlist'] = $newprojectlist;
    
    
    $top_projects = xarModAPIFunc('xproject', 'user', 'getall',
                            array('sortby' => "importance",
                                'numitems' => 10));
    
    if (!isset($top_projects) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    $data['top_projects'] = $top_projects;
    
	return $data;
}

?>