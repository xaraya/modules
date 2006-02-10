<?php
/**
 * Display one task
 *
 */
function tasks_user_display($args)
{
    $data=array();
    if (!xarVarFetch('modname', 'str:1:', $modname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('id', 'int:1', $mainid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('filter', 'str:1:', $filter, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'int:1', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('module', 'str:1:', $module, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('type', 'str:1:', $type, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('func', 'str:1:', $func, '', XARVAR_NOT_REQUIRED)) return;

    // how to get module id of calling module?
    // check if output has already been displayed
    // if not, use xarVarFetch('module') to get mod id
    // then set output as displayed
    // where to unset?
    // set session var for displayed and for module name
    // if module name is different, unset displayed

    extract($args);

    if(empty($mainid) || !is_numeric($mainid)) {
        xarResponseRedirect(xarmodurl('tasks', 'user', 'view'));
        return;
    } elseif(empty($id)) {
        $id = $mainid;
    }

    if(!isset($id)) {
        xarSessionSetVar('errormgs', xarGetStatusMsg() . '<br>' . xarML("Module argument error") . ': tasks_user_display');
        xarResponseRedirect(xarmodurl('tasks', 'user', 'view'));
        return;
    }

    if($module == "tasks" && ($type == "user" || $type == "") && $func == "display" && $id == $mainid) {
        if(isset($filter)) xarSessionSetVar('filter', $filter);
        //  if(isset($id)) {
        //     $columns[] = '<a href="#task">Task</a>');
        //     $columns[] = '<a href="#addtask">New Subtask</a>';
        //     $columns[] = '<a href="#tasklist">Subtask List</a>';
        //  }
    }

    // Get the task information from the database
    $task = xarModAPIFunc('tasks', 'user', 'get', array('id' => $id));

    if ($task == false) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>' . xarML("Getting task failed"));
        // WHAT DOES THIS DO?
        if($id == $mainid) {
            return;
        }
    }

    $userID = xarSessionGetVar('uid');
    // IS CLIENT OR MEMBER AND TASK IS PUBLIC
    // IS CREATOR/OWNER/ASSIGNER OF PRIVATE TASK?
    // IS MODERATOR / PM
    //     if ((/*!xarSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_READ) && */ $task['private'] == 0) &&
    //         /*(!xarSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_COMMENT) && */
    //          ($task['creator'] == $userID || $task['owner'] == $userID || $task['assigner'] == $userID) /* && */
    //         /*(!xarSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_MODERATE))*/ ) {
    //         xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>' . _TASKS_NOAUTH);
    //         return;
    //     }

    if($id == $mainid) {
        if($task['parentid'] > 0) { // IF NOT ROOT
            // Get the parent task
            $data['parent'] = xarModAPIFunc('tasks', 'user', 'get', array('id' => $task['parentid']));
            $data['parentlink']=xarModUrl('tasks','user','display',array('id' => $task['parentid']));
            if($task['basetaskid'] == false) {
                xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>' . xarML("Invalid base task"));
            } else {
                // Recursion!!!
                $data['displaybase'] = xarModFunc('tasks', 'user', 'display', array('id' => $task['basetaskid']));
            }
        }

        if($task['ttlsubtasks'] > 0) {
            $data['subtaskview'] = xarModFunc('tasks','user','view',array('parentid' => $id,'filter' => $filter));
        } else {
            $data['subtaskview'] = '';
        }

        // Add form for adding a new task
        xarModLoad('tasks','admin');
        $data['addtaskform'] = xarModFunc('tasks', 'admin', 'new', array('parentid' => $id));
    }

    $options = array();
    $options['edit']['link'] = xarmodurl('tasks','admin','modify', array('id' => $task['id'])); // Edit
    $options['edit']['label']= xarML('Edit task');
    // only allow accept if currently unassigned
    // if assigned to current user, or after accepted, must be approved before re-assignment (see below)
    $options['accept']['link'] = xarmodurl('tasks','admin','accept', array('id' => $task['id']));
    $options['accept']['label'] = xarML('Accept task');

    // this forces approval before re-assignment
    // need to implement user list for re-assignment
    // pull all members of groups *other* than primary user group *unless* current
    //   user is not a member of any other group
    //   create new function to handle this: tasks_userapi_getpeers($args('uid' = xarsessiongetvar('uid')))
    if ($task['date_approved'] > 0) {
        $options['assign']['link'] = xarmodurl('tasks','admin','assign', array('id' => $task['id']));
        $options['assign']['label'] = xarML('Assign task');
    } else {
        $options['approve']['link']= xarmodurl('tasks','admin','approve',array('id' => $task['id']));
        $options['approve']['label']= xarML('Approve Task');
    }
    if ($task['status'] == 1) {
        $options['open']['link']= xarmodurl('tasks','admin','open', array('id' => $task['id']));
        $options['open']['label'] = xarML('Open tasks');
    } else {
        $options['close']['link'] =xarmodurl('tasks','admin','close',array('id' => $task['id']));
        $options['close']['label'] = xarML('Close task');
    }
    if (empty($task['private'])) {
        $options['unpublish']['link'] = xarmodurl('tasks','admin','publish', array('id' => $task['id']));
        $options['unpublish']['label']= xarML('Unpublish task');
    } else {
        $options['publish']['link'] = xarmodurl('tasks','admin','publish', array('id' => $task['id']));
        $options['publish']['label'] = xarML('Publish task');
    }

    $data['options'] = $options;
    $data['task']=$task;
    $data['id']=$id;
    $data['mainid']=$mainid;
    return $data;
}

?>