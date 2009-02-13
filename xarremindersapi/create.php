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
function xtasks_remindersapi_create($args)
{
    extract($args);

    $invalid = array();
    if (!isset($taskid) || !is_numeric($taskid)) {
        $invalid[] = 'taskid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'reminders', 'create', 'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('UseReminders', 1, 'Item', "All:All:All")) {
        $msg = xarML('Not authorized to add #(1) items',
                    'reminder');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $remindertable = $xartable['xtasks_reminders'];

    $nextId = $dbconn->GenId($remindertable);

    $query = "INSERT INTO $remindertable (
                  reminderid,
                  taskid,
                  ownerid,
                  eventdate,
                  reminder)
            VALUES (?,?,?,?,?)";

    $bindvars = array(
              $nextId,
              $taskid,
              $ownerid,
              $eventdate,
              $reminder);
              
    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

    $reminderid = $dbconn->PO_Insert_ID($remindertable, 'reminderid');

    return $reminderid;
}

?>