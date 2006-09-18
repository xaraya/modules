<?php
/**
 * XTasks Module - A task management module
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

function xtasks_adminapi_inherit($args)
{
    extract($args);

    $invalid = array();
    if (!isset($taskid) || !is_numeric($taskid)) {
        $invalid[] = 'Sub-Task ID';
    }
    if (!isset($parentid) || !is_numeric($parentid)) {
        $invalid[] = 'Parent Task ID';
    }
    if (count($invalid) > 0) {
        if($parentid == $taskid) {
            $invalid[] = 'Relation to Self';
        }
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update', 'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $parentinfo = xarModAPIFunc('xtasks',
                            'user',
                            'get',
                            array('taskid' => $parentid));

    if (!isset($taskinfo) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('EditXTask', 1, 'Item', "$parentinfo[task_name]:All:$parentid")) {
        return;
    }

    $taskinfo = xarModAPIFunc('xtasks',
                            'user',
                            'get',
                            array('taskid' => $taskid));

    if (!isset($taskinfo) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('EditXTask', 1, 'Item', "$taskinfo[task_name]:All:$taskid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xtasktable = $xartable['xtasks'];

    $query = "UPDATE $xtasktable
            SET parentid = ?
            WHERE taskid = ?";

    $bindvars = array(
                    $parentid ? $parentid : 0,
                    $taskid);

    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    return true;
}
?>