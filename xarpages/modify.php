<?php

function xproject_pages_modify($args)
{
	extract($args);
    
    if (!xarVarFetch('pageid',     'id',     $pageid,     $pageid,     XARVAR_NOT_REQUIRED)) return;
	
    if (!empty($objectid)) {
        $pageid = $objectid;
    }

    if (!xarModAPILoad('xproject', 'user')) return;
    
	$item = xarModAPIFunc('xproject',
                         'pages',
                         'get',
                         array('pageid' => $pageid));
	
	if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('EditXProject', 1, 'Item', "$item[project_name]:All:$item[projectid]")) {
        return;
    }

    $projectinfo = xarModAPIFunc('xproject',
                          'user',
                          'get',
                          array('projectid' => $item['projectid']));
    
    $data = xarModAPIFunc('xproject','admin','menu');
    
	$data['pageid'] = $item['pageid'];
	
    $data['authid'] = xarSecGenAuthKey();
	
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update'));

	$item['module'] = 'xproject';
    
    $data['statuslist'] = array('Draft','Proposed','Approved','WIP','QA','Archived');

	$data['item'] = $item;
    
    $data['projectinfo'] = $projectinfo;
    
    return $data;
}

?>