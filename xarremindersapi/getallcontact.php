<?php

function dossier_remindersapi_getallcontact($args)
{
    extract($args);

    $invalid = array();
    if (!isset($contactid) || !is_numeric($contactid)) {
        $invalid[] = 'contactid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'reminders', 'getall', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
/*
    if (!xarSecurityCheck('UseReminders', 0, 'Item', "All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
*/
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $reminderstable = $xartable['dossier_reminders'];

    $sql = "SELECT reminderid,
                  contactid,
                  ownerid,
                  reminderdate,
                  warningtime,
                  notes
            FROM $reminderstable
            WHERE contactid = $contactid
            ORDER BY reminderdate";

    $result = $dbconn->Execute($sql);

    if (!$result) return;
    
    $items = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($reminderid,
              $contactid,
              $ownerid,
              $reminderdate,
              $warningtime,
              $notes) = $result->fields;
        if (xarSecurityCheck('UseDossierReminders', 0, 'Reminders', "All:All:All:All")) {
            $items[] = array('reminderid'       => $reminderid,
                              'contactid'       => $contactid,
                              'ownerid'         => $ownerid,
                              'reminderdate'    => $reminderdate,
                              'warningtime'     => $warningtime,
                              'notes'           => $notes);
        }
    }

    $result->Close();

    return $items;
}

?>
