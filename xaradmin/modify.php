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
 * Modify a task
 *
 */
function tasks_admin_modify($args)
{
    $data=array();
    if (!xarVarFetch('id', 'int:1', $id)) return;

    extract($args);

    $task = xarModAPIFunc('tasks','user','get', array('id' => $id));

    if ($task == false) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>' . xarML("No such item"));
        return false;
    }

//     if (!xarSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_EDIT)) {
//         xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>' . _TASKS_NOAUTH);
//         return;
//     }
    $statusoptions = xarModAPIFunc('tasks','user','getstatusoptions');
    $data['statusoptions'] = $statusoptions;
    $prioritydropdown = xarModAPIFunc('tasks','user','getpriorities');
    $data['prioritydropdown'] = $prioritydropdown;

    $data['id'] = $id;

    $dateformatlist = xarModAPIFunc('tasks','user','dateformatlist');
    $dateformat = $dateformatlist[xarModGetVar('tasks', 'dateformat')];
    $formsize = strlen($dateformat) * 2;
    $oneday = 60 * 60 * 24;
    $onemonth = $oneday * 30;
    $rangestart = time() - $onemonth;
    $rangeend = time() + $onemonth;
    $datedropdown = array();
    for($x = $rangestart; $x <= $rangeend;) {
        $datedropdown[] = array('id' => date("Ymd",$x),
                                'name' => strftime($dateformat,$x));
        $x += $oneday;
    }
    $data['start_planned_dropdown'] = $datedropdown;
    $data['start_actual_dropdown'] = $datedropdown;
    $data['end_planned_dropdown']=$datedropdown;
    $data['end_actual_dropdown'] = $datedropdown;
    $data['submitbutton'] = xarML("Update task");
    $data['task']=$task;
    return $data;
}

?>