<?php

function xtasks_admin_migrate($args)
{
    extract($args);
    
    if (!xarVarFetch('taskcheck', 'str', $taskcheck, $taskcheck, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('submit', 'str', $submit, $submit, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('taskfocus', 'int', $taskfocus, $taskfocus, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('taskid', 'int', $taskid, $taskid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('taskoption', 'str', $taskoption, $taskoption, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projectid', 'int', $projectid, $projectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('parentid', 'int', $parentid, $parentid, XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;

    if($newtaskid = xarModAPIFunc('xtasks',
                                'admin',
                                'migrate',
                                array('taskid'        => $taskid,
                                    'projectid'    => $projectid,
                                    'parentid'        => $parentid,
                                    'taskoption'    => $taskoption,
                                    'taskcheck'        => $taskcheck,
                                    'submit'         => $submit,
                                    'taskfocus'        => $taskfocus))) {

        xarSessionSetVar('statusmsg', xarML('Tasks(s) Migrated'));
    }

    xarResponseRedirect(xarModURL('xtasks',
                        'user',
                        'display',
                        array('projectid' => $projectid,
                                'taskid' => $newtaskid)));

    return true;
}

?>