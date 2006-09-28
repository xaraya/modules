<?php

function xtasks_remindersapi_update($args)
{
    extract($args);

    $invalid = array();
    if (!isset($reminderid) || !is_numeric($reminderid)) {
        $invalid[] = 'reminder ID';
    }
    if (!isset($ownerid) || !is_numeric($ownerid)) {
        $invalid[] = 'owner ID';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'reminders', 'update', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('xtasks',
                            'reminders',
                            'get',
                            array('reminderid' => $reminderid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('UseReminders', 1, 'Item', "All:All:All")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $reminderstable = $xartable['xtasks_reminders'];

    $query = "UPDATE $reminderstable
            SET ownerid =?,
                  eventdate =?,
                  reminder =?
            WHERE reminderid = ?";

    $bindvars = array(
              $ownerid,
              $eventdate,
              $reminder,
              $reminderid);
              
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) { // return;
        $msg = xarML('SQL: #(1)',
            $dbconn->ErrorMsg());
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    return true;
}
?>