<?php

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
		$data = xarModAPIFunc('xproject','user','menu');

        $data['projectid'] = $projectid;

        $data['name'] = xarVarPrepForDisplay($project['name']);
        $data['confirmbutton'] = xarML('Confirm');

        $data['authid'] = xarSecGenAuthKey();

        return $data;
	}
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('xproject',
                     'admin',
                     'delete',
                     array('projectid' => $projectid))) {
        return;
    }
    xarSessionSetVar('statusmsg', xarML('Project Deleted'));

    xarResponseRedirect(xarModURL('xproject', 'admin', 'view'));

    return true;
}

?>
