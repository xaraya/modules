<?php
function xproject_admin_main()
{
    xarResponseRedirect(xarModURL('xproject','admin','modifyconfig'));
	return;
}

function xproject_admin_view()
{
    xarResponseRedirect(xarModURL('xproject','admin','modifyconfig'));
	return;
}

function xproject_admin_new()
{
	xarModLoad('xproject','user');
	$data = xproject_user_menu();

    if (!xarSecAuthAction(0, 'xproject::', '::', ACCESS_ADD)) {
        $msg = xarML('Not authorized to access to #(1)',
                    'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    $data['authid'] = xarSecGenAuthKey();

    $data['namelabel'] = xarVarPrepForDisplay(xarMLByKey('Project'));
    $data['displaydates'] = xarVarPrepForDisplay(xarMLByKey('Use Date Fields'));
    $data['displayhours'] = xarVarPrepForDisplay(xarMLByKey('Use Hours Fields'));
    $data['displayfreq'] = xarVarPrepForDisplay(xarMLByKey('Use Frequency Fields'));
    $data['private'] = xarVarPrepForDisplay(xarMLByKey('Make Private'));
    $sendmailoptions = array();    
    $sendmailoptions[] = array('id'=>0,'name'=>'Please choose an email option');
    $sendmailoptions[] = array('id'=>1,'name'=>"any changes");
    $sendmailoptions[] = array('id'=>2,'name'=>"major changes");
    $sendmailoptions[] = array('id'=>3,'name'=>"weekly summaries");
    $sendmailoptions[] = array('id'=>4,'name'=>"Do NOT send email");
	$data['sendmailoptions'] = $sendmailoptions;
    $data['sendmails'] = xarVarPrepForDisplay(xarMLByKey('Email Group'));
	for($x=0;$x<=9;$x++) {
		$data['importantdaysdropdown'][] = array('id' => $x, 'name' => $x);
	}
    $data['importantdays'] = xarVarPrepForDisplay(xarMLByKey('Important Days'));
	for($x=0;$x<=9;$x++) {
		$data['criticaldaysdropdown'][] = array('id' => $x, 'name' => $x);
	}
    $data['criticaldays'] = xarVarPrepForDisplay(xarMLByKey('Critical Days'));
    $data['billable'] = xarVarPrepForDisplay(xarMLByKey('Billable'));
	
    $data['descriptionlabel'] = xarVarPrepForDisplay(xarMLByKey('Description'));
    $data['addbutton'] = xarVarPrepForDisplay(xarMLByKey('Add'));

    $item = array();
    $item['module'] = 'xproject';
    $hooks = xarModCallHooks('item','new','',$item);
    if (empty($hooks) || !is_string($hooks)) {
        $data['hooks'] = '';
    } else {
        $data['hooks'] = $hooks;
    }

    return $data;
}

function xproject_admin_create($args)
{
    list($name,
		$displaydates,
		$displayhours,
		$displayfreq,
		$private,
		$sendmails,
		$importantdays,
		$criticaldays,
		$billable,
		$description) =	xarVarCleanFromInput('name',
											'displaydates',
											'displayhours',
											'displayfreq',
											'private',
											'sendmails',
											'importantdays',
											'criticaldays',
											'billable',
											'description');

    extract($args);

    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for creating new #(1) item',
                    'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    if (!xarModAPILoad('xproject', 'admin')) return;

    $projectid = xarModAPIFunc('xproject',
                        'admin',
                        'create',
                        array('name' 		=> $name,
							'displaydates'	=> $displaydates,
							'displayhours'	=> $displayhours,
							'displayfreq'	=> $displayfreq,
							'private'		=> $private,
							'sendmails'		=> $sendmails,
							'importantdays'	=> $importantdays,
							'criticaldays'	=> $criticaldays,
							'billable'		=> $billable,
							'description'	=> $description));


	if (!isset($projectid) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;

	xarSessionSetVar('statusmsg', xarMLByKey('PROJECTCREATED'));

    xarResponseRedirect(xarModURL('xproject', 'admin', 'view'));
//    xarResponseRedirect(xarModURL('xproject', 'user', 'display', array('projectid' => $projectid)));

    return true;
}

function xproject_admin_modify($args)
{
    list($projectid,
         $objectid)= xarVarCleanFromInput('projectid',
                                         'objectid');

	extract($args);
	
    if (!empty($objectid)) {
        $projectid = $objectid;
    }
	
	if (!xarModAPILoad('xproject', 'user')) return;
	
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
    if (empty($hooks) || !is_string($hooks)) {
        $hooks = '';
    }
    $data['hooks'] = $hooks;

    return $data;
}

function xproject_admin_update($args)
{
    list($projectid,
		$name,
		$displaydates,
		$displayhours,
		$displayfreq,
		$private,
		$sendmailfreq,
		$importantdays,
		$criticaldays,
		$billable,
		$description) =	xarVarCleanFromInput('projectid',
											'name',
											'displaydates',
											'displayhours',
											'displayfreq',
											'private',
											'sendmailfreq',
											'importantdays',
											'criticaldays',
											'billable',
											'description');

    extract($args);

    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for creating new #(1) item #(2)',
                    'xproject', xarVarPrepForDisplay($projectid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    if (!xarModAPILoad('xproject', 'admin')) return;

    if(!xarModAPIFunc('xproject',
					'admin',
					'update',
					array('projectid'	=> $projectid,
						'name' 			=> $name,
						'displaydates'	=> $displaydates,
						'displayhours'	=> $displayhours,
						'displayfreq'	=> $displayfreq,
						'private'		=> $private,
						'sendmailfreq'	=> $sendmailfreq,
						'importantdays'	=> $importantdays,
						'criticaldays'	=> $criticaldays,
						'billable'		=> $billable,
						'description'	=> $description))) {
		return;
	}


	xarSessionSetVar('statusmsg', xarMLByKey('Project Updated'));

    xarResponseRedirect(xarModURL('xproject', 'admin', 'view'));
//    xarResponseRedirect(xarModURL('xproject', 'user', 'display', array('projectid' => $projectid)));

    return true;
}

function xproject_admin_delete($args)
{
    list($projectid,
         $objectid,
         $confirm) = xarVarCleanFromInput('projectid',
										  'objectid',
										  'confirm');

    extract($args);

     if (!empty($objectid)) {
         $projectid = $objectid;
     }                     

    if (!xarModAPILoad('xproject', 'user')) return;

    $project = xarModAPIFunc('xproject',
                         'user',
                         'get',
                         array('projectid' => $projectid));

    if (!isset($project) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;

    if (!xarSecAuthAction(0, 'xproject::Project', "$project[name]::$projectid", ACCESS_DELETE)) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xproject', xarVarPrepForDisplay($projectid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    if (empty($confirm)) {
		xarModLoad('xproject','user');
		$data = xproject_user_menu();

        $data['projectid'] = $projectid;

        $data['confirmtext'] = xarML('Confirm deleting this project?');
        $data['itemid'] =  xarML('Project ID');
        $data['namelabel'] =  xarMLByKey('Project');
        $data['namevalue'] = xarVarPrepForDisplay($project['name']);
        $data['confirmbutton'] = xarML('Confirm');

        $data['authid'] = xarSecGenAuthKey();

        return $data;
	}
	if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for deleting #(1) item #(2)',
                    'xproject', xarVarPrepForDisplay($projectid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }
	if (!xarModAPILoad('xproject', 'admin')) return;
    if (!xarModAPIFunc('xproject',
                     'admin',
                     'delete',
                     array('projectid' => $projectid))) {
        return;
    }
    xarSessionSetVar('statusmsg', xarMLByKey('Project Deleted'));

    xarResponseRedirect(xarModURL('xproject', 'admin', 'view'));

    return true;
}

function xproject_admin_modifyconfig()
{
	xarModLoad('xproject','user');
	$data = xproject_user_menu();
	
    if (!xarSecAuthAction(0, 'xproject::', '::', ACCESS_ADMIN)) {
        $msg = xarML('Not authorized to modify #(1) configuration settings',
                               'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }
	
	$data['authid'] = xarSecGenAuthKey();

    $dateoptions = array();    
	$dateformatlist = $data['dateformatlist'];
/*
	$x = 0;
	foreach($dateformatlist as $format) {
		$dateoptions[] = array('id'=>$x++,'name'=>strftime($format, time()),'selected'=>"");
	}
*/
	for ($x = 0; $x < count($dateformatlist) ; $x++) {
		$dateoptions[] = array('id'=>$x,'name'=>strftime($dateformatlist[$x], time()),'selected'=>"");
	}
	
	
	
	$data['dateoptions'] = $dateoptions;
	$data['dateoptions'][xarModGetVar('xproject', 'dateformat')]['selected'] = "selected=selected";
    $data['dateformat'] = xarVarPrepForDisplay(xarMLByKey('Date Format'));
    $data['dateformatvalue'] = xarModGetVar('xproject', 'dateformat');
	$data['maxdonedropdown'] = array();
	for($x=1;$x<=9;$x++) {
		if($x == xarModGetVar('xproject', 'maxdone')) $selected = "selected=true";
		else $selected = "";
		$data['maxdonedropdown'][] = array('id' => $x, 'name' => $x, 'selected' => $selected);
	}
    $data['maxdone'] = xarVarPrepForDisplay(xarMLByKey('Max Completed Shown'));
    $data['refreshmain'] = xarVarPrepForDisplay(xarMLByKey('Refresh view (in seconds)'));
    $data['refreshmainvalue'] = xarModGetVar('xproject', 'refreshmain');

    $data['showextraasterisk'] = xarVarPrepForDisplay(xarMLByKey('Show extra asterisk'));
    $data['showextraasteriskcheck'] = xarModGetVar('xproject','showextraasterisk') ? 'checked' : '';
    $data['showlinenumbers'] = xarVarPrepForDisplay(xarMLByKey('Show line numbers'));
    $data['showlinenumberscheck'] = xarModGetVar('xproject','showlinenumbers') ? 'checked' : '';
    $data['showpercent'] = xarVarPrepForDisplay(xarMLByKey('Show percentage in table'));
    $data['showpercentcheck'] = xarModGetVar('xproject','showpercent') ? 'checked' : '';
    $data['showpriority'] = xarVarPrepForDisplay(xarMLByKey('Show priority in table'));
    $data['showprioritycheck'] = xarModGetVar('xproject','showpriority') ? 'checked' : '';
    $data['todoheading'] = xarVarPrepForDisplay(xarMLByKey('Displayed header text'));
    $data['todoheadingvalue'] = xarModGetVar('xproject', 'todoheading');
	for($x=1;$x<=9;$x++) {
		if($x == xarModGetVar('xproject', 'itemsperpage')) $selected = "selected=true";
		else $selected = "";
		$data['itemsperpagedropdown'][] = array('id' => $x, 'name' => $x, 'selected' => $selected);
	}
    $data['itemsperpage'] = xarVarPrepForDisplay(xarMLByKey('Tasks per page'));
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration'));

    $hooks = xarModCallHooks('module', 'modifyconfig', 'example',
                            array('module' => 'example'));
    if (empty($hooks) || !is_string($hooks)) {
        $data['hooks'] = '';
    } else {
        $data['hooks'] = $hooks;
    }

    return $data;
}

function xproject_admin_updateconfig()
{
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for updating #(1) configuration',
                    'Example');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

	list($displaydates,
		$displayhours,
		$displayfrequency,
		$accessrestricted,
		$dateformat,
		$maxdone,
		$mostimportantdays,
		$refreshmain,
		$sendmails,
		$showextraasterisk,
		$showlinenumbers,
		$showpercent,
		$showpriority,
		$todoheading,
		$veryimportantdays,
		$itemsperpage) = xarVarCleanFromInput('displaydates',
												'displayhours',
												'displayfrequency',
												'accessrestricted',
												'dateformat',
												'maxdone',
												'mostimportantdays',
												'refreshmain',
												'sendmails',
												'showextraasterisk',
												'showlinenumbers',
												'showpercent',
												'showpriority',
												'todoheading',
												'veryimportantdays',
												'itemsperpage');
/*												
	trim($newprojectname);
	if(!empty($newprojectname)) {
		xarModAPIFunc('categories',
						  'admin', 
						  'create', 
						  Array('name' => $newprojectname,
								'description' => $newprojectdesc,
								'parent_id' => xarModGetVar('xproject', 'projectmastercid')));
	}
*/
    if (!isset($displaydates)) $displaydates = false;
    if (!isset($displayhours)) $displayhours = false;
    if (!isset($displayfrequency)) $displayfrequency = false;
    if (!isset($accessrestricted)) $accessrestricted = false;
    if (!isset($dateformat)) $dateformat = 1;
    if (!isset($maxdone)) $maxdone = 10;
    if (!isset($mostimportantdays)) $mostimportantdays = 0;
    if (!isset($refreshmain)) $refreshmain = 600;
    if (!isset($sendmails)) $sendmails = false;
    if (!isset($showextraasterisk)) $showextraasterisk = false;
    if (!isset($showlinenumbers)) $showlinenumbers = false;
    if (!isset($showpercent)) $showpercent = false;
    if (!isset($showpriority)) $showpriority = false;
    if (!isset($todoheading)) $todoheading = "Task Management Administration";
    if (!isset($veryimportantdays)) $veryimportantdays = 0;
    if (!isset($itemsperpage)) $itemsperpage = 20;

    xarModSetVar('xproject', 'displaydates', $displaydates);
    xarModSetVar('xproject', 'displayhours', $displayhours);
    xarModSetVar('xproject', 'displayfrequency', $displayfrequency);
    xarModSetVar('xproject', 'accessrestricted', $accessrestricted);
    xarModSetVar('xproject', 'dateformat', $dateformat);
    xarModSetVar('xproject', 'maxdone', $maxdone);
    xarModSetVar('xproject', 'mostimportantdays', $mostimportantdays);
    xarModSetVar('xproject', 'refreshmain', $refreshmain);
    xarModSetVar('xproject', 'sendmails', $sendmails);
    xarModSetVar('xproject', 'showextraasterisk', $showextraasterisk);
    xarModSetVar('xproject', 'showlinenumbers', $showlinenumbers);
    xarModSetVar('xproject', 'showpercent', $showpercent);
    xarModSetVar('xproject', 'showpriority', $showpriority);
    xarModSetVar('xproject', 'todoheading', $todoheading);
    xarModSetVar('xproject', 'veryimportantdays', $veryimportantdays);
    xarModSetVar('xproject', 'itemsperpage', $itemsperpage);
	
    xarModCallHooks('module','updateconfig','xproject',
                   array('module' => 'xproject'));
				   
    xarResponseRedirect(xarModURL('xproject', 'admin', 'main'));

    return true;
}

function xproject_admin_migrate($args)
{
    list($taskcheck,
		$submit,
		$taskfocus,
		$taskid,
		$taskoption,
		$projectid,
		$parentid) =	xarVarCleanFromInput('taskcheck',
											'submit',
											'taskfocus',
											'taskid',
											'taskoption',
											'projectid',
											'parentid');

    extract($args);

    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for migrating #(1) items in project id #(2)',
                    'xproject', xarVarPrepForDisplay($projectid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    if (!xarModAPILoad('xproject', 'admin')) return;

    if($newtaskid = xarModAPIFunc('xproject',
								'admin',
								'migrate',
								array('taskid'		=> $taskid,
									'projectid'	=> $projectid,
									'parentid'		=> $parentid,
									'taskoption'	=> $taskoption,
									'taskcheck'		=> $taskcheck,
									'submit' 		=> $submit,
									'taskfocus'		=> $taskfocus))) {

		xarSessionSetVar('statusmsg', xarMLByKey('Project(s) Migrated'));
	}

    xarResponseRedirect(xarModURL('xproject',
						'user',
						'display',
						array('projectid' => $projectid,
								'taskid' => $newtaskid)));

    return true;
}

function xproject_admin_menu()
{
    $menu = array();

	$dateformatlist = array('Please choose a Date/Time Format',
							'%m/%d/%Y',
							'%B %d, %Y',
							'%a, %B %d, %Y',
							'%A, %B %d, %Y',
							'%m/%d/%Y %H:%M',
							'%B %d, %Y %H:%M',
							'%a, %B %d, %Y %H:%M',
							'%A, %B %d, %Y %H:%M',
							'%m/%d/%Y %I:%M %p',
							'%B %d, %Y %I:%M %p',
							'%a, %B %d, %Y %I:%M %p',
							'%A, %B %d, %Y %I:%M %p');
	$menu['dateformatlist'] = $dateformatlist;

    $menu['menutitle'] = xarML('XProject Administration');

    $menu['menulabel_new'] = xarMLByKey('New Project');
    $menu['menulabel_view'] = xarMLByKey('Projects');
    $menu['menulabel_search'] = xarMLByKey('Search');
    $menu['menulabel_config'] = xarMLByKey('Config');

    $menu['status'] = '';

    return $menu;
}
?>