<?php

function xtasks_user_worklog($args)
{
    xarVarFetch('taskid',   'id::', $taskid);

    extract($args);
    
    $data = array();
    
    $data['taskid'] = $taskid;

    $taskinfo = xarModAPIFunc('xtasks',
                              'user',
                              'get',
                              array('taskid' => $taskid));
    if($taskinfo == false) return;
    
    $data['taskinfo'] = $taskinfo;
    
    $projectinfo = array();
    if($taskinfo['objectid'] > 0 && $taskinfo['modid'] == xarModGetIDFromName('xproject')) {
        $projectinfo = xarModAPIFunc('xproject',
                                      'user',
                                      'get',
                                      array('projectid' => $taskinfo['objectid']));
    }
    $data['projectinfo'] = $projectinfo;

    $data['uid'] = xarSessionGetVar('uid');
    $data['authid'] = xarSecGenAuthKey('xtasks');
    
    if (!isset($time)) {
        $time = time();
    }
    $time += xarMLS_userOffset($time) * 3600;
    
    $data['currentdate'] = date("Y-m-d H:i:s", $time);
    
    $data['returnurl'] = xarModURL('xproject','user','dashboard');
    $data['newargs'] = "&amp;inline=1&amp;returnurl=".urlencode($data['returnurl']);
    
    $worklog = xarModAPIFunc('xtasks', 'worklog', 'getallfromtask', array('taskid' => $taskid));
    
    if($worklog === false) return;
    
    $data['worklog'] = $worklog;
    
    
    return $data;



}

?>