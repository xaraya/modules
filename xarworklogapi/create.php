<?php
/**
 * Administration System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xproject module
 * @author Chad Kraeft <stego@xaraya.com>
*/
function xtasks_worklogapi_create($args)
{
    extract($args);
    
    if (!isset($ownerid) || !is_numeric($ownerid)) {
        $ownerid = xarSessionGetVar('uid');
    }

    $invalid = array();
    if (!isset($taskid) || !is_numeric($taskid)) {
        $invalid[] = 'taskid';
    }
    if (!isset($eventdate) || !is_string($eventdate)) {
        $invalid[] = 'eventdate';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'worklog', 'create', 'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('RecordWorkLog', 1, 'Item', "All:All:All")) {
        $msg = xarML('Not authorized to add #(1) items',
                    'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $worklogtable = $xartable['xtasks_worklog'];

    $nextId = $dbconn->GenId($worklogtable);

    $query = "INSERT INTO $worklogtable (
                  worklogid,
                  taskid,
                  ownerid,
                  eventdate,
                  hours,
                  notes)
            VALUES (?,?,?,?,?,?)";

    $bindvars = array(
              $nextId,
              $taskid,
              $ownerid,
              $eventdate,
              $hours,
              $notes);
              
    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

    $worklogid = $dbconn->PO_Insert_ID($worklogtable, 'worklogid');

    return $worklogid;
}

?>