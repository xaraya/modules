<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function xproject_admin_delete($args)
{
    extract($args);
    
    if (!xarVarFetch('projectid', 'id', $projectid)) return;
    if (!xarVarFetch('objectid', 'isset', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'isset', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $projectid = $objectid;
    }
    
    $project = xarModAPIFunc('xproject',
                         'user',
                         'get',
                         array('projectid' => $projectid));

    if (!isset($project) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;


    if (!xarSecurityCheck('DeleteXProject', 1, 'Item', "$project[project_name]:All:$projectid")) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xproject', xarVarPrepForDisplay($projectid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    if (empty($confirm)) {
        xarModLoad('xproject','user');
        $data = xarModAPIFunc('xproject','user','menu');

        $data['projectid'] = $projectid;

        $data['project_name'] = xarVarPrepForDisplay($project['project_name']);
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

    xarResponseRedirect(xarModURL('xproject', 'admin'));

    return true;
}

?>
