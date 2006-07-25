<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xTasks Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
function xtasks_user_display($args)
{
    extract($args);
    if (!xarVarFetch('projectid', 'id', $projectid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startnum', 'int', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('taskid', 'id', $taskid, $taskid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('filter', 'str', $filter, $filter, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $projectid = $objectid;
    }

    $data = xarModAPIFunc('xtasks','user','menu');
    $data['projectid'] = $projectid;
    $data['status'] = '';
    $data['taskid'] = $taskid;

    $project = xarModAPIFunc('xtasks',
                          'user',
                          'get',
                          array('projectid' => $projectid));

    if (!isset($project) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    list($project['name']) = xarModCallHooks('item',
                                         'transform',
                                         $project['projectid'],
                                         array($project['name']));

    $data['name'] = $project['name'];
    $data['description'] = $project['description'];

    if(is_numeric($taskid) && $taskid > 0) {
        $task = xarModAPIFunc('xtasks',
                              'tasks',
                              'get',
                              array('taskid' => $taskid));

        if (isset($task) && xarCurrentErrorType() == XAR_NO_EXCEPTION) {
            list($task['name']) = xarModCallHooks('item',
                                                 'transform',
                                                 $taskid,
                                                 array($task['name']));
        }

        $data['taskname'] = $task['name'];
        $data['taskdescription'] = $task['description'];

        //if (xarSecAuthAction(0, 'xtasks::Tasks', '$task[name]::$taskid', ACCESS_EDIT)) {
        if (xarSecurityCheck('EditXProject', 0, 'Item', "All:All:All")) {//TODO: security
            $data['curtask_editurl'] = xarModURL('xtasks', 'tasks', 'modify', array('taskid' => $taskid));
        } else {
            $data['curtask_editurl'] = "";
        }
        if (!xarSecurityCheck('DeleteXProject', 0, 'Item', "All:All:All")) {//TODO: security
        //if (xarSecAuthAction(0, 'xtasks::Tasks', '$task[name]::$taskid', ACCESS_DELETE)) {
            $data['curtask_deleteurl'] = xarModURL('xtasks', 'tasks', 'delete', array('taskid' => $taskid));
        } else {
            $data['curtask_deleteurl'] = "";
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
            $data['taskparent_name'] = $parent['name'];
            $data['taskparent_id'] = $parent['taskid'];
        }
        $data['taskroot_name'] = xarML('Project Top');
    }

    $hooks = xarModCallHooks('item',
                             'display',
                             $projectid,
                             xarModURL('xtasks',
                                       'user',
                                       'display',
                                       array('projectid' => $projectid)));
    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }

    // BUILD TASK ADD FORM
    if (xarSecurityCheck('EditXProject', 0, 'Item', "All:All:All")) {//TODO: security
    //if (xarSecAuthAction(0, 'xtasks::Projects', '$project[name]::$project[projectid]', ACCESS_MODERATE)) {
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
        $item['module'] = 'xtasks';
        $hooks = xarModCallHooks('item','new','',$item);
            if (empty($hooks)) {
                $data['hookoutput'] = array();
            } else {
                /* You can use the output from individual hooks in your template too, e.g. with
                 * $hookoutput['categories'], $hookoutput['dynamicdata'], $hookoutput['keywords'] etc.
                 */
                $data['hookoutput'] = $hooks;
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
    $tasks = xarModAPIFunc('xtasks',
                            'tasks',
                            'getall',
                            array('startnum' => $startnum,
                                   'projectid' => $projectid,
                                'parentid' => $taskid,
                                'filter' => $filter));
    if (isset($tasks) && is_array($tasks) && (xarCurrentErrorType() == XAR_NO_EXCEPTION)) {
        for ($i = 0; $i < count($tasks); $i++) {
            $task = $tasks[$i];
            $tasks[$i]['created'] = strftime($data['dateformatlist'][xarModGetVar('xtasks','dateformat')],$task['date_created']);
            $tasks[$i]['modified'] = strftime($data['dateformatlist'][xarModGetVar('xtasks','dateformat')],$task['date_changed']);
            $tasks[$i]['closed'] = ($task['status'] == 1) ? "x" : "";
            $tasks[$i]['createdby'] = xarUserGetVar('uname',$task['creator']);
            $tasks[$i]['assignedto'] = xarUserGetVar('uname',$task['owner']);
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
