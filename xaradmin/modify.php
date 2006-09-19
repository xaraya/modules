<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function xproject_admin_modify($args)
{
    extract($args);

    if (!xarVarFetch('projectid',     'id',     $projectid,     $projectid,     XARVAR_NOT_REQUIRED)) return;

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

    $data = array();

    $data['projects_objectid'] = xarModGetVar('xproject', 'projects_objectid');

    $data['projectid'] = $projectinfo['projectid'];

    $data['teamlist'] = $teamlist;

    $data['authid'] = xarSecGenAuthKey();

    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update'));

    $item['module'] = 'xproject';

    $data['statuslist'] = array('Draft','Proposed','Approved','WIP','QA','Archived');

    $data['item'] = $projectinfo;

    $data['returnurl'] = xarServerGetVar('HTTP_REFERER');

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