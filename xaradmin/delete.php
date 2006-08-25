<?php
/**
 * XProject Module - A simple task management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xTasks Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
function xtasks_admin_delete($args)
{
    
    if (!xarVarFetch('taskid', 'id', $taskid)) return;
    if (!xarVarFetch('action', 'str', $action, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'isset', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str', $returnurl, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'isset', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);

    if (!empty($objectid)) {
        $taskid = $objectid;
    }
    if (!isset($returnurl)) {
        $returnurl = $_SERVER['HTTP_REFERER'];
    }
    $task = xarModAPIFunc('xtasks',
                         'user',
                         'get',
                         array('taskid' => $taskid));

    if (!isset($task) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('DeleteXTask',1,'Item',$taskid)) return;

    if (empty($confirm)) {
        xarModLoad('xtasks','user');
        $data = xarModAPIFunc('xtasks','admin','menu');

        $data['taskid'] = $taskid;
        $data['status'] = $task['status'];
        $data['returnurl'] = $returnurl;

        $data['name'] = xarVarPrepForDisplay($task['task_name']);
        $data['confirmbutton'] = xarML('Confirm');

        $data['authid'] = xarSecGenAuthKey();

        return $data;
    }
    if (!xarSecConfirmAuthKey()) return;
    
    if($action == "delete") {
        if (!xarModAPIFunc('xtasks',
                         'admin',
                         'delete',
                         array('taskid' => $taskid))) {
            return;
        }
        xarSessionSetVar('statusmsg', xarML('Task Deleted'));
    } else {
        if (!xarModAPIFunc('xtasks',
                         'admin',
                         'close',
                         array('taskid' => $taskid,
                               'actual_end_date' => $actual_end_date))) {
            return;
        }
        xarSessionSetVar('statusmsg', xarML('Task Closed'));
    }

    xarResponseRedirect($returnurl);

    return true;
}

?>
