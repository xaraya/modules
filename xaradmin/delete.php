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
    list($taskid,
         $objectid,
         $confirm) = xarVarCleanFromInput('taskid',
                                          'objectid',
                                          'confirm');

    extract($args);

     if (!empty($objectid)) {
         $taskid = $objectid;
     }
    $task = xarModAPIFunc('xtasks',
                         'user',
                         'get',
                         array('taskid' => $taskid));

    if (!isset($task) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('DeleteXTask',1,'Item',$taskid)) return;

    if (empty($confirm)) {
        xarModLoad('xtasks','user');
        $data = xarModAPIFunc('xtasks','user','menu');

        $data['taskid'] = $taskid;

        $data['name'] = xarVarPrepForDisplay($task['task_name']);
        $data['confirmbutton'] = xarML('Confirm');

        $data['authid'] = xarSecGenAuthKey();

        return $data;
    }
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('xtasks',
                     'admin',
                     'delete',
                     array('taskid' => $taskid))) {
        return;
    }
    xarSessionSetVar('statusmsg', xarML('Task Deleted'));

    xarResponseRedirect(xarModURL('xtasks', 'admin', 'view'));

    return true;
}

?>
