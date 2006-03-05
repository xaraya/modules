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
 * @author Chad Kraeft
 */
/**
 * Update task
 *
 */
function tasks_admin_update($args)
{
    if (!xarVarFetch('id', 'str:1:', $id, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name', 'str:1:', $name, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('priority', 'str:1:', $priority, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str:1:', $status, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('description', 'str:1:', $description, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('private', 'str:1:', $private, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('owner', 'str:1:', $owner, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('assigner', 'str:1:', $assigner, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_start_planned', 'str:1:', $date_start_planned, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_start_actual', 'str:1:', $date_start_actual, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_end_planned', 'str:1:', $date_end_planned, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_end_actual', 'str:1:', $date_end_actual, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours_planned', 'str:1:', $hours_planned, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours_spent', 'str:1:', $hours_spent, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours_remaining', 'str:1:', $hours_remaining, NULL, XARVAR_NOT_REQUIRED)) return;

    extract($args);

    // SECAUTH KEY CHECK REMOVED DUE TO MULTIPLE FORM OCCURRENCES CONFLICTING ON KEY USAGE
    // PERMISSIONS CHECK SHOULD BE SUFFICIENT TO PREVENT MALICIOUS USAGE

    if($returnid = xarModAPIFunc('tasks',
                                'admin',
                                'update',
                                array('id'               => $id,
                                    'name'               => $name,
                                    'status'             => $status,
                                    'priority'           => $priority,
                                    'description'        => $description,
                                    'private'            => $private,
                                    'owner'              => $owner,
                                    'assigner'           => $assigner,
                                    'date_start_planned' => $date_start_planned,
                                    'date_start_actual'  => $date_start_actual,
                                    'date_end_planned'   => $date_end_planned,
                                    'date_end_actual'    => $date_end_actual,
                                    'hours_planned'      => $hours_planned,
                                    'hours_spent'        => $hours_spent,
                                    'hours_remaining'    => $hours_remaining))) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>' . xarML("Tasks updated"));
    }
    xarRedirect(xarModURL('tasks', 'user', 'display', array('id' => $returnid,
                                                            '' => '#tasklist')));

    return true;
}

?>