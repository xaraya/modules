<?php

function xproject_user_display($args)
{
    list($projectid,
         $startnum,
         $taskid,
         $filter,
         $objectid) = xarVarCleanFromInput('projectid',
                                          'startnum',
                                          'taskid',
                                          'filter',
                                          'objectid');

    extract($args);
    if (!empty($objectid)) {
        $projectid = $objectid;
    }

    $data = xarModAPIFunc('xproject','user','menu');
    
    $data['status'] = '';
    $data['taskid'] = $taskid;
    
    $project = xarModAPIFunc('xproject',
                          'user',
                          'get',
                          array('projectid' => $projectid));

    if (!isset($project) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;

    list($project['name']) = xarModCallHooks('item',
                                         'transform',
                                         $project['projectid'],
                                         array($project['name']));

    $data['name'] = $project['name'];
    $data['description'] = $project['description'];

    if(is_numeric($taskid) && $taskid > 0) {
        $task = xarModAPIFunc('xproject',
                              'tasks',
                              'get',
                              array('taskid' => $taskid));
    
        if (isset($task) && xarExceptionMajor() == XAR_NO_EXCEPTION) {
            list($task['name']) = xarModCallHooks('item',
                                                 'transform',
                                                 $taskid,
                                                 array($task['name']));
        }
        
        $data['taskname'] = $task['name'];
        $data['taskdescription'] = $task['description'];
        
        if (xarSecAuthAction(0, 'xproject::Tasks', '$task[name]::$taskid', ACCESS_EDIT)) {
            $data['curtask_editurl'] = xarModURL('xproject', 'tasks', 'modify', array('taskid' => $taskid));
        } else {
            $data['curtask_editurl'] = "";
        }
        if (xarSecAuthAction(0, 'xproject::Tasks', '$task[name]::$taskid', ACCESS_DELETE)) {
            $data['curtask_deleteurl'] = xarModURL('xproject', 'tasks', 'delete', array('taskid' => $taskid));
        } else {
            $data['curtask_deleteurl'] = "";
        }
        
        if($task['parentid'] > 0) {
            $parent = xarModAPIFunc('xproject',
                                  'tasks',
                                  'get',
                                  array('taskid' => $task['parentid']));
        } else {
            $data['roottask'] = xarML('project overview');
        }
        
        if (isset($parent) && xarExceptionMajor() == XAR_NO_EXCEPTION) {
            $data['taskparent_name'] = $parent['name'];
            $data['taskparent_id'] = $parent['taskid'];
        }
        $data['taskroot_name'] = xarML('Project Top');
    }

    $hooks = xarModCallHooks('item',
                             'display',
                             $projectid,
                             xarModURL('xproject',
                                       'user',
                                       'display',
                                       array('projectid' => $projectid)));
    if (empty($hooks)) {
        $data['hookoutput'] = '';
    } elseif (is_array($hooks)) {
        $data['hookoutput'] = join('',$hooks);
    } else {
        $data['hookoutput'] = $hooks;
    }

    // BUILD TASK ADD FORM
    if (xarSecAuthAction(0, 'xproject::Projects', '$project[name]::$project[projectid]', ACCESS_MODERATE)) {
        $data['authid'] = xarSecGenAuthKey();

        $data['projectid'] = $project['projectid'];
        $data['parentid'] = $taskid;
    
        if(!isset($taskid) || $taskid == 0) $data['tasknamelabel'] = xarVarPrepForDisplay(xarML('New Task'));
        else $data['tasknamelabel'] = xarVarPrepForDisplay(xarML('New Sub-Task'));
        
        $statusoptions = array();    
        $statusoptions[] = array('id'=>0,'name'=>xarML('Open'));
        $statusoptions[] = array('id'=>1,'name'=>xarML('Closed'));
        $data['statusoptions'] = $statusoptions;

        $data['prioritydropdown'] = array();
        for($x=0;$x<=9;$x++) {
            $data['prioritydropdown'][] = array('id' => $x, 'name' => $x);
        }

        $data['addbutton'] = xarVarPrepForDisplay(xarML('Add'));
    
        $item = array();
        $item['module'] = 'xproject';
        $hooks = xarModCallHooks('item','new','',$item);
                if (empty($hooks)) {
                    $data['hooks'] = '';
                } elseif (is_array($hooks)) {
                    $data['hooks'] = join('',$hooks);
                } else {
                    $data['hooks'] = $hooks;
                }
    }
    
    $filteroptions = array(xarML('default'),
                            xarML('My Tasks'),
                            xarML('Available Tasks'),
                            xarML('Priority List'),
                            xarML('Recent Activity'),
                            "");
    $data['filteroptions'] = array();
    foreach($filteroptions as $id=>$name) {
        $data['filteroptions'][] = array('id' => $id,
                                        'name' => $name,
                                        'selected' => ($id == $filter ? 1 : 0));
    }
    $data['filterbutton'] = xarVarPrepForDisplay(xarML('Filter'));
    // BUILD TASKS ARRAY
    $data['tasks'] = array();
    $data['tasklistfilter'] = $filter;
    $tasks = xarModAPIFunc('xproject',
                            'tasks',
                            'getall',
                            array('startnum' => $startnum,
                                   'projectid' => $projectid,
                                'parentid' => $taskid,
                                'filter' => $filter));
    if (isset($tasks) && is_array($tasks) && (xarExceptionMajor() == XAR_NO_EXCEPTION)) {
        for ($i = 0; $i < count($tasks); $i++) {
            $task = $tasks[$i];
            $tasks[$i]['created'] = strftime($data['dateformatlist'][xarModGetVar('xproject','dateformat')],$task['date_created']);
            $tasks[$i]['modified'] = strftime($data['dateformatlist'][xarModGetVar('xproject','dateformat')],$task['date_changed']);
            $tasks[$i]['closed'] = ($task['status'] == 1) ? "x" : "";
            $tasks[$i]['createdby'] = xarUserGetVar('uname',$task['creator']);
            $tasks[$i]['assignedto'] = xarUserGetVar('uname',$task['owner']);
            if (xarSecAuthAction(0, 'xproject::Tasks', "$task[name]::$task[taskid]", ACCESS_EDIT)) {
                $tasks[$i]['editurl'] = xarModURL('xproject',
                                                   'tasks',
                                                   'modify',
                                                   array('taskid' => $task['taskid']));
            } else {
                $tasks[$i]['editurl'] = '';
            }
            if (xarSecAuthAction(0, 'xproject::Tasks', "$task[name]::$task[taskid]", ACCESS_DELETE)) {
                $tasks[$i]['deleteurl'] = xarModURL('xproject',
                                                   'tasks',
                                                   'delete',
                                                   array('taskid' => $task['taskid']));
            } else {
                $tasks[$i]['deleteurl'] = '';
            }
        }
        $data['tasks'] = $tasks;
        $data['numtasks'] = count($data['tasks']);
        $numtasks = count($data['tasks']);
    }
        
    $taskoptionslist = array(1 => xarML('Surface'),
                            2 => xarML('Delete') . ' (' . xarML('delete subtasks') . ')',
                            3 => xarML('Delete') . ' (' . xarML('move subtasks up') . ')');
    $taskoptions = array();
    foreach($taskoptionslist as $optionid=>$option) {
        $taskoptions[] = array('id' => $optionid,
                                'name' => $option);
    }
    $data['taskoptions'] = $taskoptions;

    return $data;
}

?>