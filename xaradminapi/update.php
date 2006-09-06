<?php
/**
 * XTasks Module - A task management module
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
 
function xtasks_adminapi_update($args)
{
    extract($args);
    
    if(!isset($assigner) || empty($assigner) || $assigner == 0) {
        $assigner = xarSessionGetVar('uid');
    }

    $invalid = array();
    if (!isset($taskid) || !is_numeric($taskid)) {
        $invalid[] = 'Task ID';
    }
    if (!isset($task_name) || !is_string($task_name)) {
        $invalid[] = 'task_name';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update', 'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('xtasks',
                            'user',
                            'get',
                            array('taskid' => $taskid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditXTask', 1, 'Item', "$item[task_name]:All:$taskid")) {
        return;
    }
    if (!xarSecurityCheck('EditXTask', 1, 'Item', "$task_name:All:$taskid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xtasktable = $xartable['xtasks'];

    $query = "UPDATE $xtasktable
            SET parentid = ?,
                  projectid = ?,
                  task_name = ?,
                  status = ?,
                  priority = ?,
                  importance = ?,
                  description = ?,
                  private = ?,
                  owner = ?,
                  assigner = ?,
                  groupid = ?,
                  date_approved = ?,
                  date_changed = NOW(),
                  date_start_planned = ?,
                  date_start_actual = ?,
                  date_end_planned = ?,
                  date_end_actual = ?,
                  hours_planned = ?,
                  hours_spent = ?,
                  hours_remaining = ?
            WHERE taskid = ?";

    $bindvars = array(
                    $parentid ? $parentid : 0,
                    $projectid ? $projectid : 0,
                    $task_name,
                    $status,
                    $priority,
                    $importance,
                    $description,
                    $private ? $private : 0,
                    $owner ? $owner : 0,
                    $assigner ? $assigner : 0,
                    $groupid ? $groupid : 0,
                    $date_approved ? $date_approved : NULL,
                    $date_start_planned ? $date_start_planned : NULL,
                    $date_start_actual ? $date_start_actual : NULL,
                    $date_end_planned ? $date_end_planned : NULL,
                    $date_end_actual ? $date_end_actual : NULL,
                    $hours_planned,
                    $hours_spent,
                    $hours_remaining,
                    $taskid);
              
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    $item['module'] = 'xtasks';
    $item['itemid'] = $taskid;
    $item['name'] = $task_name;
    xarModCallHooks('item', 'update', $taskid, $item);

    return true;
}
?>