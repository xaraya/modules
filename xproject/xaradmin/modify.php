<?php

function xproject_admin_modify($args)
{
    list($projectid,
         $objectid)= xarVarCleanFromInput('projectid',
                                         'objectid');

	extract($args);
	
    if (!empty($objectid)) {
        $projectid = $objectid;
    }
	$project = xarModAPIFunc('xproject',
                         'user',
                         'get',
                         array('projectid' => $projectid));
	
	if (!isset($project) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;

    if (!xarSecAuthAction(0, 'xproject::Project', "$project[name]::$projectid", ACCESS_EDIT)) {
        $msg = xarML('Not authorized to modify #(1) item #(2)',
                    'xproject', xarVarPrepForDisplay($projectid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }
	
	xarModLoad('xproject','user');
	$data = array();
	$data['projectid'] = $project['projectid'];
	$data['name'] = $project['name'];
	$data['description'] = $project['description'];
	$data['usedatefields'] = $project['usedatefields'];
	$data['usehoursfields'] = $project['usehoursfields'];
	$data['usefreqfields'] = $project['usefreqfields'];
	$data['allowprivate'] = $project['allowprivate'];
	$data['importantdays'] = $project['importantdays'];
	$data['criticaldays'] = $project['criticaldays'];
	$data['sendmailfreq'] = $project['sendmailfreq'];
	$data['billable'] = $project['billable'];
	
    $data['authid'] = xarSecGenAuthKey();

    $sendmailoptions = array();    
    $sendmailoptions[] = array('id'=>0,'name'=>xarML("Please choose an email option"),'selected'=>"");
    $sendmailoptions[] = array('id'=>1,'name'=>xarML("any changes"),'selected'=>"");
    $sendmailoptions[] = array('id'=>2,'name'=>xarML("major changes"),'selected'=>"");
    $sendmailoptions[] = array('id'=>3,'name'=>xarML("weekly summaries"),'selected'=>"");
    $sendmailoptions[] = array('id'=>4,'name'=>xarML("Do NOT send email"),'selected'=>"");
	$sendmailoptions[$project['sendmailfreq']]['selected'] = "1";
	$data['sendmailoptions'] = $sendmailoptions;
	
    $data['updatebutton'] = xarVarPrepForDisplay(xarMLByKey('Update'));

    $item = array();
	$item['module'] = 'xproject';
    $hooks = xarModCallHooks('item','modify',$projectid,$item);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    return $data;
}

?>