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
 
function xtasks_adminapi_updateassign($args)
{
    extract($args);
    
    if(!isset($assigner) || !is_numeric($assigner)) {
        $assigner = xarModGetUserVar('xproject', 'mymemberid'); // xarSessionGetVar('uid'); // 
    }

    $invalid = array();
    if (!isset($taskid) || !is_numeric($taskid)) {
        $invalid[] = 'Task ID';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'updateassign', 'xtasks');
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

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xtasktable = $xartable['xtasks'];

    $query = "UPDATE $xtasktable
            SET owner = ?,
                assigner = ?,
                date_end_planned = ?,
                description = ?,
                date_changed = NOW()
            WHERE taskid = ?";

    $bindvars = array(
                    $owner ? $owner : 0,
                    $assigner ? $assigner : 0,
                    $date_end_planned ? $date_end_planned : NULL,
                    $description,
                    $taskid);
              
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;
    
    $mymemberid = xarModGetUserVar('xproject', 'mymemberid');
    if(!empty($item['owner']) && $item['owner'] != $mymemberid) {
        xarModAPIFunc('xtasks', 'user', 'notify', array('contacttype' => 779, 'owner' => $item['owner'], 'taskid' => $taskid, 'action' => "ASSIGN"));
    }
    if(!empty($owner) && $owner != $mymemberid) {
        xarModAPIFunc('xtasks', 'user', 'notify', array('contacttype' => 779, 'owner' => $owner, 'taskid' => $taskid, 'action' => "ASSIGN"));
    }

    return true;
}
?>