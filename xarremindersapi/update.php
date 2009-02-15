<?php

function dossier_remindersapi_update($args)
{
    extract($args);

    $invalid = array();
    if (!isset($reminderid) || !is_numeric($reminderid)) {
        $invalid[] = 'reminder ID';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'reminders', 'update', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('dossier',
                            'reminders',
                            'get',
                            array('reminderid' => $reminderid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('UseDossierReminders', 1, 'Reminders')) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $reminderstable = $xartable['dossier_reminders'];

    $query = "UPDATE $reminderstable
            SET ownerid =?,
                  reminderdate =?,
                  notes =?
            WHERE reminderid = ?";

    $bindvars = array(
              $ownerid,
              $reminderdate,
              $notes,
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
