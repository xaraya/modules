<?php

function xproject_admin_modify($args)
{
    extract($args);

    if (!xarVarFetch('projectid',     'id',     $projectid,     $projectid,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('inline',     'str::',     $inline,     "",     XARVAR_NOT_REQUIRED)) return;

    if(!xarModLoad('addressbook', 'user')) return;

    if (!empty($objectid)) {
        $projectid = $objectid;
    }
    $projectinfo = xarModAPIFunc('xproject',
                         'user',
                         'get',
                         array('projectid' => $projectid));

    if (!isset($projectinfo) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('EditXProject', 1, 'Item', "$projectinfo[project_name]:All:$projectid")) {
        return;
    }

    $teamlist = xarModAPIFunc('xproject',
                            'team',
                            'getall',
                            array('projectid' => $projectid));

    if($inline) {
        $data = array();
    } else {
        $data = xarModAPIFunc('xproject', 'admin', 'menu');
    }
    
    $data['inline'] = $inline;

    $data['projects_objectid'] = xarModGetVar('xproject', 'projects_objectid');

    $data['projectid'] = $projectinfo['projectid'];

    $data['teamlist'] = $teamlist;

    $data['authid'] = xarSecGenAuthKey();

    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update'));

    $data['statuslist'] = array('Draft','Proposed','Approved','WIP','QA','Archived');

    $data['item'] = $projectinfo;

    $data['returnurl'] = xarServerGetVar('HTTP_REFERER');

    $projectinfo['module'] = "xproject";
    $projectinfo['itemtype'] = 0;
    $projectinfo['itemid'] = $projectid;

    $hooks = xarModCallHooks('item','modify',$projectid,$projectinfo);

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