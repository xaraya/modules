<?php
/**
 * Display one task
 *
 */
function tasks_user_display($args)
{
    $data=array();
    list($modname, $mainid, $filter,$objectid,$module,$type,$func) = 
        xarVarCleanFromInput('modname',
                             'id',
                             'filter',
                             'objectid',
                             'module',
                             'type',
                             'func');
    
    // how to get module id of calling module?
    // check if output has already been displayed
    // if not, use xarvarcleanfrominput('module') to get mod id
    // then set output as displayed
    // where to unset?
    // set session var for displayed and for module name
    // if module name is different, unset displayed
    
    extract($args);
    
	if(empty($mainid) || !is_numeric($mainid)) {
		xarSessionSetVar('errormgs', xarGetStatusMsg() . '<br>' . xarML("Module argument error") . ': tasks_user_display');
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
	    //$output->Text(tasks_menu());
	}

    // Get the task information from the database
    $task = xarModAPIFunc('tasks', 'user', 'get', array('id' => $id));

    if ($task == false) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>' . xarML("Getting task failed"));
		// WHAT DOES THIS DO?
		if($id == $mainid) {
			//$output->Text(tasks_feedback());
			//return $output->GetOutput();
		}
    }

	$userID = xarSessionGetVar('uid');
	// IS CLIENT OR MEMBER AND TASK IS PUBLIC
	// IS CREATOR/OWNER/ASSIGNER OF PRIVATE TASK?
	// IS MODERATOR / PM
    // 	if ((/*!xarSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_READ) && */ $task['private'] == 0) && 
    //         /*(!xarSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_COMMENT) && */ 
    //          ($task['creator'] == $userID || $task['owner'] == $userID || $task['assigner'] == $userID) /* && */
    //         /*(!xarSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_MODERATE))*/ ) {
    //         xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>' . _TASKS_NOAUTH);
    // 		$output->Text(tasks_feedback());
    //         return $output->GetOutput();
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

        // TODO: investigate this
		//$output->Text(tasks_feedback());

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

/**
 * Construct the menu
 *
 */
function tasks_menu()
{
// 	$output = new pnHTML();

// 	list($filter,$id,$module,$type,	$func) = xarVarCleanFromInput('filter',
// 									'id',
// 									'module',
// 									'type',
// 									'func');
									
// 	if(isset($filter)) xarSessionSetVar('filter', $filter);

//     $output->SetInputMode(_PNH_VERBATIMINPUT);
//     $output->TableStart(_TASKS);
//     $output->SetOutputMode(_PNH_RETURNOUTPUT);
//     $columns = array();
// 	if(isset($id) && $module == "tasks" && $func == "display") {
// 		$columns[] = $output->Text('<a href="#task">Task</a>');	
// 		$columns[] = $output->Text('<a href="#addtask">New Subtask</a>');	
// 		$columns[] = $output->Text('<a href="#tasklist">Subtask List</a>');	
// 	}
//     $columns[] = $output->URL(xarmodurl('tasks',
//                                        'user',
//                                        'view'),
//                               _TASKS_VIEW);
// 	if (xarSecAuthAction(0, 'tasks::task', '::', ACCESS_ADD)) {
// 		$columns[] = $output->URL(xarmodurl('tasks',
// 										   'admin',
// 										   'new'),
// 								  _TASKS_ADD);
// 	}
//     $output->SetOutputMode(_PNH_KEEPOUTPUT);
//     $output->Text('<tr><td align=center>' . implode(" | ",$columns) . '</td></tr>');
//     $output->TableEnd();

    return '';

}

/**
 * Give feedback
 *
 */
function tasks_feedback()
{
	$feedback = xarGetStatusMsg();
    if(empty($feedback)) $feedback="";
	return $feedback;
}

?>