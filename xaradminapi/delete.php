<?php
/**
 * XProject Module - A simple project management module
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
function xtasks_adminapi_delete($args)
{
    extract($args);

    // if we're coming via a hook call
    if (isset($objectid)) {
    // TODO: cfr. hitcount delete stuff, once we enable item delete hooks
        // Return the extra info
        if (!isset($extrainfo)) {
            $extrainfo = array();
        }
        return $extrainfo;
    }
    
    if (!isset($taskid) || !is_numeric($taskid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item ID', 'admin', 'delete', 'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // does it exist ?
    $taskinfo = xarModAPIFunc('xtasks',
                            'user',
                            'get',
                            array('taskid' => $taskid));

    if (!isset($taskinfo) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('DeleteXTask', 1, 'Item', "$taskinfo[task_name]:All:$taskid")) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xtasks', $projectid);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    // kill existing task hours to start the process of removing them from parent tasks and assoc project
    xarModAPIFunc('xtasks', 'admin', 'updatehours',
                array('taskid' => $taskid,
                    'hours_planned_delta' => 0 - $taskinfo['hours_planned'],
                    'hours_spent_delta' => 0 - $taskinfo['hours_spent'],
                    'hours_remaining_delta' => 0 - $taskinfo['hours_remaining']));
    
    $mymemberid = xarModGetUserVar('xproject', 'mymemberid');
    if(!empty($taskinfo['owner'])) {
        xarModAPIFunc('xtasks', 'user', 'notify', array('contacttype' => 779, 'owner' => $taskinfo['owner'], 'taskid' => $taskid, 'action' => "DELETE"));
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $xtasks_table = $xartable['xtasks'];

    // does it have children ?
    $sql = "DELETE FROM $xtasks_table
            WHERE taskid = " . $taskid;
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $item['module'] = 'xtasks';
    $item['itemid'] = $taskid;
    xarModCallHooks('item', 'delete', $taskid, $item);

    // Let the calling process know that we have finished successfully
    return true;
}

?>
