<?php

function xproject_admin_modify($args)
{
	extract($args);
    
    if (!xarVarFetch('projectid',     'id',     $projectid,     $projectid,     XARVAR_NOT_REQUIRED)) return;
	
    if (!empty($objectid)) {
        $projectid = $objectid;
    }
	$item = xarModAPIFunc('xproject',
                         'user',
                         'get',
                         array('projectid' => $projectid));
	
	if (!isset($project) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('EditXProject', 1, 'Item', "$item[project_name]:All:$projectid")) {
        return;
    }
    
	$data = array();
	$data['projectid'] = $item['projectid'];
	
    $data['authid'] = xarSecGenAuthKey();
	
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update'));

	$item['module'] = 'xproject';
    
    $data['statuslist'] = array('Draft','Proposed','Approved','WIP','QA','Archived');

	$data['item'] = $item;

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