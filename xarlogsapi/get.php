<?php

function dossier_logsapi_get($args)
{
    extract($args);

    if (!isset($logid) || !is_numeric($logid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'logid', 'logs', 'get', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $logstable = $xartable['dossier_logs'];

    $query = "SELECT logid,
                  contactid,
                  ownerid,
                  logtype,
                  logdate,
                  createdate,
                  notes
            FROM $logstable
            WHERE logid = ?";
    $result = &$dbconn->Execute($query,array($logid));

    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    list($logid,
          $contactid,
          $ownerid,
          $logtype,
          $logdate,
          $createdate,
          $notes) = $result->fields;

    $result->Close();

    if (!xarSecurityCheck('ReadDossierLog', 1, 'Log', "All:All:All:All")) {
        $msg = xarML('Not authorized to view reminders.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'AUTH_FAILED',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $item = array('logid'       => $logid,
                  'contactid'   => $contactid,
                  'ownerid'     => $ownerid,
                  'logtype'     => $logtype,
                  'logdate'     => $logdate,
                  'createdate'  => $createdate,
                  'notes'       => $notes);

    return $item;
}

?>
