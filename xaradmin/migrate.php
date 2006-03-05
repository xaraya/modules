<?php
/**
 * Tasks module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Tasks Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Tasks Module Development Team
 */
/**
 * Migrate a task
 * @author Chad Kraeft
 *
 */
function tasks_admin_migrate($args)
{
    if (!xarVarFetch('submit',      'str:1', $submit,      '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('taskcheck',   'array', $taskcheck,   array(), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('taskfocus',   'str:1', $taskfocus,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('taskoption',  'int:1', $taskoption,  '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('id',          'int:1', $id,          0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modname',     'str:1', $modname,     '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid',    'int:1', $objectid,    0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('parentid',    'int:1', $parentid,    0, XARVAR_NOT_REQUIRED)) return;

    extract($args);

    if($newid = xarModAPIFunc('tasks',
                                'admin',
                                'migrate',
                                array('id'        => $id,
                                    'modname'        => $modname,
                                    'objectid'        => $objectid,
                                    'parentid'        => $parentid,
                                    'taskoption'    => $taskoption,
                                    'taskcheck'        => $taskcheck,
                                    'submit'         => $submit,
                                    'taskfocus'        => $taskfocus))) {

        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>' . xarML("Task migration successfull"));
    }

    if(empty($newid) || $newid == 0) {
        xarResponseRedirect(xarModURL('tasks','user','view'));
    } else {
        xarResponseRedirect(xarModURL('tasks','user','display',
                            array('id' => $newid,
                                    'modname' => $modname,
                                    'objectid' => $objectid,
                                    '' => '#tasklist')));
    }

    return true;
}

?>