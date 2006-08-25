<?php

function xtasks_worklog_modify($args)
{
	extract($args);
    
    if (!xarVarFetch('reminderid',     'id',     $reminderid,     $reminderid,     XARVAR_NOT_REQUIRED)) return;
	
    if (!empty($objectid)) {
        $reminderid = $objectid;
    }

    if (!xarModAPILoad('xtasks', 'user')) return;
    
	$item = xarModAPIFunc('xtasks',
                         'reminders',
                         'get',
                         array('reminderid' => $reminderid));
	
	if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('EditXProject', 1, 'Item', "$item[project_name]:All:$item[projectid]")) {
        return;
    }

    $projectinfo = xarModAPIFunc('xtasks',
                          'user',
                          'get',
                          array('projectid' => $item['projectid']));
    
    $data = xarModAPIFunc('xtasks','admin','menu');
    
	$data['reminderid'] = $item['reminderid'];
	
    $data['authid'] = xarSecGenAuthKey();
	
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update'));

	$item['module'] = 'xtasks';
    
    $data['statuslist'] = array('Draft','Proposed','Approved','WIP','QA','Archived');

	$data['item'] = $item;
    
    $data['projectinfo'] = $projectinfo;
    
    return $data;
}

?>