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
function xtasks_worklog_update($args)
{
    extract($args);

    if (!xarVarFetch('worklogid', 'id', $worklogid)) return;
    if (!xarVarFetch('eventdate', 'str:1:', $eventdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours', 'int::', $hours, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notes', 'str::', $notes, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;

    $worklog = xarModAPIFunc('xtasks', 'worklog', 'get', array('worklogid' => $worklogid));

    if(!$worklog) return;

    if(!xarModAPIFunc('xtasks',
                    'worklog',
                    'update',
                    array('worklogid'    => $worklogid,
                        'eventdate'        => $eventdate,
                        'hours'         => $hours,
                        'notes'          => $notes))) {
        return;
    }


    xarSessionSetVar('statusmsg', xarML('Work Record Updated'));

    xarResponseRedirect(xarModURL('xtasks', 'admin', 'display', array('taskid' => $worklog['taskid'], 'mode' => "worklog")));

    return true;
}

?>