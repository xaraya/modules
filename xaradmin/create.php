<?php
/**
 * Create a task
 *
 */
function tasks_admin_create($args)
{
    if (!xarVarFetch('parentid',    'int:1', $parentid,    0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modname',     'str:1', $modname,     '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid',    'int:1', $objectid,    0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name',        'str:1', $name,        '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('description', 'str:1', $description, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status',      'int:1', $status,      0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('priority',     'int:1', $priority,   0, XARVAR_NOT_REQUIRED)) return;

    extract($args);

    $returnid = xarModAPIFunc('tasks','admin','create',
                        array('parentid'     => $parentid,
                              'modname'         => $modname,
                              'objectid'         => $objectid,
                              'name'             => $name,
                              'status'         => $status,
                              'priority'         => $priority,
                              'description'    => $description,
                              'private' => 0));

    if ($returnid != false) {
        // Success
        //xarSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_TASKCREATED);
    }

    xarResponseRedirect(xarModURL('tasks', 'user', 'display', array('id' => $returnid,
    																'parentid' => $parentid,
                                                            '' => '#addtask')));
    return true;
}

?>