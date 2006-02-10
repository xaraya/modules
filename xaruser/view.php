<?php
/**
 * View a list of tasks
 *
 */
function tasks_user_view($args)
{
    $data = array();
    if (!xarVarFetch('parentid',     'int:1',  $parentid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('module',       'str:1:', $module, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('type',         'str:1:', $type, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('func',         'str:1:', $func, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('filter',       'str:1:', $filter, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('displaydepth', 'int:1',  $displaydepth, -1, XARVAR_NOT_REQUIRED)) return;
    extract($args);

    if (isset($filter)) {
        $filter = 0;
        xarSessionSetVar('filter', $filter);
    }

    //$maxlevel = xarSessionGetVar('maxlevel');
    $maxlevel = 9;
    if ($displaydepth == -1) $displaydepth = ($maxlevel ? $maxlevel : 1);

    xarSessionSetVar('maxlevel', $displaydepth);

    $tasks = xarModAPIFunc('tasks','user','getall',
                           array('parentid' => $parentid,
                                 'modname' => $module,
                                 // 'objectid' => $objectid,
                                 'displaydepth' => 1));
    if (!$tasks && $tasks != array()) {
        $msg = xarML("Getting tasks failed");
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    $basetaskid = xarModAPIFunc('tasks', 'user', 'getroot', array('id' => $parentid));

    $data['filterformtarget']=xarModURL('tasks','user',
                                        ($parentid ? 'display':'view'),
                                        array('' => '#tasklist'));
    $data['parentid']= (empty($parentid)) ? 0 : $parentid;
    $data['modname'] = (empty($module)) ? '' : $module;
    $data['objectid'] =(empty($objectid)) ? 0 : $objectid;

    $data['filter']=$filter;

    // Construct the depth dropdown
    $depthdropdown = array();
    $maxdepth = xarModGetVar('tasks', 'maxdisplaydepth');
    $data['maxdepth']= xarSessionGetVar('maxlevel');
    $data['filtersubmit']=xarML("Filter");

    if ($filter == 1 || $filter == 2 || $filter == 3) {
        // echo _TASKS_OPENTASKSONLY;
    }

    if (is_array($tasks) && count($tasks) > 0) {
        foreach($tasks as $key => $task) {
            $dateformat = xarModGetVar('tasks', 'dateformat');
            $dateformatlist = xarModAPIFunc('tasks','user','dateformatlist',array());
            if(empty($dateformat)) $dateformat = 1;
            $tasks[$key]['date_created'] = strftime($dateformatlist[$dateformat],$task['date_created']);
            $tasks[$key]['date_changed'] = strftime($dateformatlist[$dateformat],$task['date_changed']);
            $tasks[$key]['private'] = ($task['private'] == 1) ? "?" : "";
            $tasks[$key]['closed'] = ($task['status'] == 1) ? "x" : "";
            $tasks[$key]['creator'] = xarUserGetVar('uname',$task['creator']);
            $tasks[$key]['owner'] = xarUserGetVar('uname',$task['owner']);

            // TODO: Do this in the template
            $indent = "";
            for($x=2;$x<=$tasks[$key]['depth'];$x++) {
                $indent .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            }
            $tasks[$key]['indent']=$indent;
            $tasks[$key]['url']=xarModUrl('tasks', 'user', 'display',  array('id' => $task['id']));
            $userID = xarUserGetVar('uid');
            if (/*xarSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_ADMIN) ||*/
                /* (xarSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid:$task[basetaskid]', ACCESS_MODERATE) && */
                   ($task['creator'] == $userID || $task['owner'] == $userID     || $task['assigner'] == $userID)
                  )
                {
                    // Checkbox insertion (see template)
            }

            if (/* xarSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_MODERATE) || */
                /* (xarSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_MODERATE) && */
                     ($task['creator'] == $userID || $task['owner'] == $userID   || $task['assigner'] == $userID)
                   ) {
                if(xarModGetVar('tasks', 'showoptions')) {
                    $options = array();

                    $options[] = xarModURL('tasks','admin','modify', array('id' => $task['id'])); // Edit
                    // Only allow accept if currently unassigned
                    // If assigned to current user, or after accepted, must be approved before re-assignment (see below)
                    $options[] = xarModURL('tasks', 'admin', 'accept', array('id' => $task['id'])); // Accept

                    // This forces approval before re-assignment
                    // TODO: Implement user list for re-assignment
                    // Pull all member of groups *other* than primary user group *unless*:
                    // - current user is not a member of any other group
                    // TODO: Create new api function to hanle this: tasks_userapi_getpeers($args('uid' = xarSessionGetVar('uid')))

                    $options[] = ($task['date_approved'] > 0) ?
                        xarmodurl('tasks', 'admin', 'assign', array('id' => $task['id'])): // Assign
                        xarmodurl('tasks', 'admin','approve', array('id' => $task['id'])); // Approve

                    $options[] = ($task['status'] == 1) ?
                        xarmodurl('tasks', 'admin', 'open', array('id' => $task['id'])): // Open
                        xarmodurl('tasks', 'admin','close', array('id' => $task['id'])); //Close

                    $options[] = (empty($task['private'])) ?
                        xarmodurl('tasks', 'admin', 'unpublish',array('id' => $task['id'])): // Unpublish
                        xarmodurl('tasks', 'admin', 'publish',array('id' => $task['id'])); // Publish
                    $tasks[$key]['options']=$options;
                    $data['options'] = $options;
                }
            }
        }
    }
    $data['tasks']=$tasks;
    // Construct the task options
    $taskoptionslist = array(
        1 => xarML("Surface tasks"),
        2 => xarML("Delete") . ' (' . xarML("delete subtasks") . ')',
        3 => xarML("Surface subs") . ' (' . xarML("surface subtasks"). ')');
    $taskoptions = array();
    foreach($taskoptionslist as $optionid=>$option) {
        $taskoptions[] = array('id' => $optionid,
                               'name' => $option);
    }
    $data['taskoptions']=$taskoptions;
    $data['tasksubmitbutton']=xarML("X");
    return $data;
}

?>