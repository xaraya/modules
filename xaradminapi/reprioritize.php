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
 
function xtasks_adminapi_reprioritize($args)
{
    extract($args);

    $invalid = array();
    if (!isset($taskid) || !is_numeric($taskid)) {
        $invalid[] = 'Task ID';
    }
    if (!isset($mode) || !is_string($mode)) {
        $invalid[] = 'mode';
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

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xtasktable = $xartable['xtasks'];

    if($mode == "incr") {
        $query = "UPDATE $xtasktable
                SET priority = priority + 1
                WHERE taskid = ?";
    } else {
        $query = "UPDATE $xtasktable
                SET priority = priority - 1
                WHERE taskid = ?";
    }
    
    $bindvars = array($taskid);
              
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;
    
    $mymemberid = xarModGetUserVar('xproject', 'mymemberid');
    if(!empty($item['owner']) && $item['owner'] != $mymemberid) {
        xarModAPIFunc('xtasks', 'user', 'notify', array('owner' => $item['owner'], 'taskid' => $taskid, 'action' => "PRIORITY"));
    }

    return true;
}
?>