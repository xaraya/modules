<?php
/**
 * Add a new task
 *
 */
function tasks_admin_new($args)
{
    $data=array();
	list($module,$type,	$func) = xarVarCleanFromInput('module',
									'type',
									'func');

	extract($args);
	

  // DISPLAY ONLY IF COMMENT AUTH FOR BASETASKID, OR MOD AUTH FOR NO BASETASKID
//     if (!pnSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_ADD)) {
//         pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_NOAUTH);
//         pnRedirect(pnModURL('tasks','user','view'));
// 		return;
//     }

// NEED TO INVESTIGATE THIS
// 	if($module == "tasks" && $type == "admin" && $func == "new") {
// 	    $output->Text(tasks_menu());
// 	}

    $statusoptions = xarModAPIFunc('tasks','user','getstatusoptions');
    $data['statusoptions']=$statusoptions;
    $prioritydropdown =xarModAPIFunc('tasks','user','getpriorities');
    $data['prioritydropdown']=$prioritydropdown;
	
    // NEED TO INVESTIGATE THIS
// 	if($module == "tasks" && $type == "admin" && $func == "new") {
// 		$output->Text(tasks_feedback());
// 	}
    //$data['feedback']=tasks_feedback();

    $data['parentid']= (empty($parentid))? 0: $parentid;
    $data['modname']= (empty($module)) ? '' : $module;
    $data['objectid']= (empty($objectid))? 0: $objectid;

    $data['submitbutton']=xarML('Add task');
    return $data;
/*
// EXTRANEOUS	
    $sendmailoptions = array();    
    $sendmailoptions[] = array('id'=>0,'name'=>'Please choose an email option');
    $sendmailoptions[] = array('id'=>1,'name'=>"any changes");
    $sendmailoptions[] = array('id'=>2,'name'=>"major changes");
    $sendmailoptions[] = array('id'=>3,'name'=>"weekly summaries");
    $sendmailoptions[] = array('id'=>4,'name'=>"Do NOT send email");
	$data['sendmailoptions'] = $sendmailoptions;
    $data['sendmails'] = pnVarPrepForDisplay(xarML('Email Group'));
	for($x=0;$x<=9;$x++) {
		$data['importantdaysdropdown'][] = array('id' => $x, 'name' => $x);
	}
    $data['importantdays'] = pnVarPrepForDisplay(xarML('Important Days'));
	for($x=0;$x<=9;$x++) {
		$data['criticaldaysdropdown'][] = array('id' => $x, 'name' => $x);
	}
*/
}

?>