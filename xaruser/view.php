<?php

function xproject_user_view($args)
{
    $startnum = xarVarCleanFromInput('startnum');

    $data = xarModAPIFunc('xproject','user','menu');

	$data['items'] = array();

    if (!xarSecAuthAction(0, 'xproject::', '::', ACCESS_OVERVIEW)) {
        $msg = xarML('Not authorized to access to #(1)',
                    'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $xprojects = xarModAPIFunc('xproject',
                          'user',
                          'getall',
                          array('startnum' => $startnum,
                                'numitems' => 10));

	if (!isset($xprojects) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;
	
    for ($i = 0; $i < count($xprojects); $i++) {
        $project = $xprojects[$i];
		if (xarSecAuthAction(0, 'xproject::Projects', "$project[name]::$project[projectid]", ACCESS_READ)) {
			$xprojects[$i]['link'] = xarModURL('xproject',
											   'user',
											   'display',
											   array('projectid' => $project['projectid']));
		}
		if (xarSecAuthAction(0, 'xproject::Projects', "$project[name]::$project[projectid]", ACCESS_EDIT)) {
			$xprojects[$i]['editurl'] = xarModURL('xproject',
											   'admin',
											   'modify',
											   array('projectid' => $project['projectid']));
		} else {
			$xprojects[$i]['editurl'] = '';
		}
		if (xarSecAuthAction(0, 'xproject::Projects', "$project[name]::$project[projectid]", ACCESS_DELETE)) {
			$xprojects[$i]['deleteurl'] = xarModURL('xproject',
											   'admin',
											   'delete',
											   array('projectid' => $project['projectid']));
		} else {
			$xprojects[$i]['deleteurl'] = '';
		}
    }

    $data['xprojects'] = $xprojects;
	$data['pager'] = '';
    return $data;
}

?>