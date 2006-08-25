<?php

function xproject_team_modify($args)
{
	extract($args);
    
    if (!xarVarFetch('projectid',    'id',     $projectid,    $projectid,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('memberid',     'id',     $memberid,     $memberid,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projectrole',     'str',     $projectrole,     '',     XARVAR_NOT_REQUIRED)) return;

    if (!xarModAPILoad('xproject', 'user')) return;
    
	$item = xarModAPIFunc('xproject',
                         'team',
                         'get',
                         array('projectid' => $projectid,
                            'memberid' => $memberid));
	
	if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('EditXProject', 1, 'Item', "$item[project_name]:All:$item[projectid]")) {
        return;
    }
    
    $data = xarModAPIFunc('xproject','user','menu');
    
	$data['featureid'] = $item['featureid'];
	
    $data['authid'] = xarSecGenAuthKey();
	
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update'));

	$item['module'] = 'xproject';

	$data['item'] = $item;
    
    $data['projectinfo'] = $projectinfo;

    return $data;
}

?>