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
function xproject_team_delete($args)
{
    extract($args);
    
    if (!xarVarFetch('projectid', 'id', $projectid)) return;
    if (!xarVarFetch('memberid', 'isset', $memberid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'isset', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarModAPILoad('xproject', 'user')) return;
    if (!xarModLoad('addressbook', 'user')) return;
    
    $memberinfo = xarModAPIFunc('xproject',
                         'team',
                         'get',
                         array('projectid' => $projectid,
                            'memberid' => $memberid));

    if (!isset($projectinfo) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;


    if (!xarSecurityCheck('DeleteXProject', 1, 'Item', "$memberinfo[project_name]:All:$memberinfo[projectid]")) {
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
        $data['project_name'] = $memberinfo['project_name'];

        $data['memberid'] = $memberid;
        $data['memberinfo'] = $memberinfo;
        $data['confirmbutton'] = xarML('Confirm');

        $data['authid'] = xarSecGenAuthKey();

        return $data;
    }
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('xproject',
                     'team',
                     'delete',
                     array('projectid' => $projectid,
                            'memberid' => $memberid))) {
        return;
    }
    xarSessionSetVar('statusmsg', xarML('Team Member Removed'));

    xarResponseRedirect(xarModURL('xproject', 'admin', 'display', array('projectid' => $memberinfo['projectid'])));

    return true;
}

?>
