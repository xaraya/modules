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
function xtasks_reminders_update($args)
{
    extract($args);

    if (!xarVarFetch('reminderid', 'id', $reminderid)) return;
    if (!xarVarFetch('reminder_name', 'str:1:', $reminder_name, $reminder_name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projectid', 'id', $projectid, $projectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str::', $status, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sequence', 'int::', $sequence, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('description', 'str::', $description, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('relativeurl', 'str::', $relativeurl, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;

    if(!xarModAPIFunc('xtasks',
                    'reminders',
                    'update',
                    array('reminderid'            => $reminderid,
                        'reminder_name'         => $reminder_name,
                        'status'            => $status,
                        'description'       => $description,
                        'relativeurl'          => $relativeurl))) {
        return;
    }


    xarSessionSetVar('statusmsg', xarML('Page Updated'));

    xarResponseRedirect(xarModURL('xtasks', 'admin', 'display', array('projectid' => $projectid, 'mode' => "reminders")));

    return true;
}

?>