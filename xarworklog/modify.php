<?php
/**
 * xTasks Module - Project ToDo management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xTasks Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
function xtasks_worklog_modify($args)
{
    extract($args);

    if (!xarVarFetch('worklogid',     'id',     $worklogid,     $worklogid,     XARVAR_NOT_REQUIRED)) return;

    if (!xarModAPILoad('xtasks', 'user')) return;

    $item = xarModAPIFunc('xtasks',
                         'worklog',
                         'get',
                         array('worklogid' => $worklogid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('AuditWorklog', 1, 'Item', "All:All:All")) {
        return;
    }

    $taskinfo = xarModAPIFunc('xtasks',
                          'user',
                          'get',
                          array('taskid' => $item['taskid']));

    $data = xarModAPIFunc('xtasks','admin','menu');

    $data['worklogid'] = $item['worklogid'];

    $data['authid'] = xarSecGenAuthKey();

    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update'));

    $data['item'] = $item;

    $data['taskinfo'] = $taskinfo;

    return $data;
}

?>