<?php

function xproject_admin_migrate($args)
{
    extract($args);
    if (!xarVarFetch('taskcheck', 'str::', $taskcheck, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('submit', 'str::', $submit, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('taskfocus', 'str::', $taskfocus, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('taskid', 'str::', $taskid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('taskoption', 'str::', $taskoption, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projectid', 'str::', $projectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('parentid', 'str::', $parentid, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;

    if($newtaskid = xarModAPIFunc('xproject',
                                'admin',
                                'migrate',
                                array('taskid'     => $taskid,
                                    'projectid'    => $projectid,
                                    'parentid'     => $parentid,
                                    'taskoption'   => $taskoption,
                                    'taskcheck'    => $taskcheck,
                                    'submit'       => $submit,
                                    'taskfocus'    => $taskfocus))) {

        xarSessionSetVar('statusmsg', xarML('Project(s) Migrated'));
    }

    xarResponseRedirect(xarModURL('xproject',
                        'user',
                        'display',
                        array('projectid' => $projectid,
                                'taskid' => $newtaskid)));

    return true;
}

?>