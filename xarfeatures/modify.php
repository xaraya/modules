<?php

function xproject_features_modify($args)
{
    extract($args);

    if (!xarVarFetch('featureid',     'id',     $featureid,     $featureid,     XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $featureid = $objectid;
    }

    if (!xarModAPILoad('xproject', 'user')) return;

    $item = xarModAPIFunc('xproject',
                         'features',
                         'get',
                         array('featureid' => $featureid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('EditXProject', 1, 'Item', "$item[project_name]:All:$item[projectid]")) {
        return;
    }

    $projectinfo = xarModAPIFunc('xproject',
                          'user',
                          'get',
                          array('projectid' => $item['projectid']));

    $data = xarModAPIFunc('xproject','admin','menu');

    $data['featureid'] = $item['featureid'];

    $data['authid'] = xarSecGenAuthKey();

    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update'));

    $item['module'] = 'xproject';

    $data['statuslist'] = array('Draft','Proposed','Approved','WIP','QA','Archived');

    $data['item'] = $item;

    $data['projectinfo'] = $projectinfo;

    return $data;
}

?>