<?php

function dossier_remindersapi_get($args)
{
    extract($args);

    if (!isset($reminderid) || !is_numeric($reminderid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'reminder ID', 'reminders', 'get', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $reminderstable = $xartable['dossier_reminders'];

    $query = "SELECT reminderid,
                  contactid,
                  ownerid,
                  reminderdate,
                  warningtime,
                  notes
            FROM $reminderstable
            WHERE reminderid = ?";
    $result = &$dbconn->Execute($query,array($reminderid));

    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    list($reminderid,
          $contactid,
          $ownerid,
          $reminderdate,
          $warningtime,
          $notes) = $result->fields;

    $result->Close();

    if (!xarSecurityCheck('UseDossierReminders', 1, 'Reminders', "All:All:All:All")) {
        $msg = xarML('Not authorized to view this reminder.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'AUTH_FAILED',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $item = array('reminderid'      => $reminderid,
                  'contactid'       => $contactid,
                  'ownerid'         => $ownerid,
                  'reminderdate'    => $reminderdate,
                  'warningtime'     => $warningtime,
                  'notes'           => $notes);

    return $item;
}

?>
