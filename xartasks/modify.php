<?php

function xtasks_tasks_modify($args)
{
    list($startnum,
         $taskid,
         $objectid) = xarVarCleanFromInput('startnum',
                                          'taskid',
                                          'objectid');

    extract($args);
    
    if (!empty($objectid)) {
        $taskid = $objectid;
    }
    
    if (!xarModLoad('xtasks', 'user')) return;
    
    $data = xarModAPIFunc('xtasks','user','menu');
    
    $data['status'] = '';
    
    $task = xarModAPIFunc('xtasks',
                         'tasks',
                         'get',
                         array('taskid' => $taskid));
    
    if (!isset($task) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $project = xarModAPIFunc('xtasks',
                          'user',
                          'get',
                          array('projectid' => $task['projectid']));

    if (isset($project['projectid']) && xarCurrentErrorType() == XAR_NO_EXCEPTION) {
        list($project['name']) = xarModCallHooks('item',
                                             'transform',
                                             $project['projectid'],
                                             array($project['name']));
    
        $data['name'] = $project['name'];
        $data['description'] = $project['description'];
    }

    if (!xarSecAuthAction(0, 'xtasks::Tasks', "$task[name]::$taskid", ACCESS_EDIT)) {
        $msg = xarML('Not authorized to modify #(1) item #(2)',
                    'xtasks', xarVarPrepForDisplay($projectid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    if($task['parentid'] > 0) {
        $parent = xarModAPIFunc('xtasks',
                              'tasks',
                              'get',
                              array('taskid' => $task['parentid']));
    } else {
        $data['roottask'] = xarML('project overview');
    }
    
    if (isset($parent) && xarCurrentErrorType() == XAR_NO_EXCEPTION) {
        $data['taskparent'] = $parent['name'];
        $data['taskparent_id'] = $parent['taskid'];
    } else {
        $data['taskparent'] = xarML('Project Top');
        $data['taskparent_id'] = 0;
    }

    $data['projectid'] = $task['projectid'];
    $data['authid'] = xarSecGenAuthKey();
    $data['taskid'] = $taskid;
    $data['parentid'] = $task['parentid'];

    $data['taskname'] = $task['name'];
    $data['description'] = $task['description'];

    $statusoptions = array();    
    $statusoptions[] = array('id'=>0,'name'=>xarML('Open'),'value'=>0);
    $statusoptions[] = array('id'=>1,'name'=>xarML('Closed'),'value'=>1);
    $data['statusoptions'] = $statusoptions;
    $data['status'] = $task['status'];

    $data['priority'] = $task['priority'];

    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Modify'));

    $item = array();
    $item['module'] = 'xtasks';
    $hooks = xarModCallHooks('item','modify',$taskid,$item);
    if (empty($hooks)) {
        $data['hookoutput'] = '';
    } elseif (is_array($hooks)) {
        $data['hookoutput'] = join('',$hooks);
    } else {
        $data['hookoutput'] = $hooks;
    }

    $data['tasks'] = array();

    $tasks = xarModAPIFunc('xtasks',
                            'tasks',
                            'getall',
                            array('startnum' => $startnum,
                                'projectid' => $task['projectid'],
                                'parentid' => $taskid));
    if (isset($tasks) && is_array($tasks) && (xarCurrentErrorType() == XAR_NO_EXCEPTION)) {
        for ($i = 0; $i < count($tasks); $i++) {
            $task = $tasks[$i];
            if (xarSecAuthAction(0, 'xtasks::Tasks', "$task[name]::$task[taskid]", ACCESS_EDIT)) {
                $tasks[$i]['editurl'] = xarModURL('xtasks',
                                                   'tasks',
                                                   'modify',
                                                   array('taskid' => $task['taskid']));
            } else {
                $tasks[$i]['editurl'] = '';
            }
            if (xarSecAuthAction(0, 'xtasks::Tasks', "$task[name]::$task[taskid]", ACCESS_DELETE)) {
                $tasks[$i]['deleteurl'] = xarModURL('xtasks',
                                                   'tasks',
                                                   'delete',
                                                   array('taskid' => $task['taskid']));
            } else {
                $tasks[$i]['deleteurl'] = '';
            }
        }
        $data['tasks'] = $tasks;
        $data['numtasks'] = count($tasks);
    }

    return $data;
}

?>
