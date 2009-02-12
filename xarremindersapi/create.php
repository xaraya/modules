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
function dossier_remindersapi_create($args)
{
    extract($args);

    $invalid = array();
    if (!isset($contactid) || !is_numeric($contactid)) {
        $invalid[] = 'contactid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'reminders', 'create', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('UseDossierReminders', 1, 'Reminders', "All:All:All:All")) {
        $msg = xarML('Not authorized to add #(1) items',
                    'reminder');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $remindertable = $xartable['dossier_reminders'];

    $nextId = $dbconn->GenId($remindertable);

    $query = "INSERT INTO $remindertable (
                  reminderid,
                  contactid,
                  ownerid,
                  reminderdate,
                  warningtime,
                  notes)
            VALUES (?,?,?,?,?,?)";

    $bindvars = array(
              $nextId,
              $contactid,
              $ownerid,
              $reminderdate,
              $warningtime,
              $notes);
              
    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

    $reminderid = $dbconn->PO_Insert_ID($remindertable, 'reminderid');

    return $reminderid;
}

?>
