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
function dossier_logsapi_create($args)
{
    extract($args);
    
    if (!isset($ownerid) || !is_numeric($ownerid)) {
        $ownerid = xarSessionGetVar('uid');
    }

    $invalid = array();
    if (!isset($contactid) || !is_numeric($contactid)) {
        $invalid[] = 'contactid';
    }
    if (!isset($logdate) || !is_string($logdate)) {
        $logdate = date('Y-m-d H:i:s');
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'logs', 'create', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('AddDossierLog', 1, 'Log', "All:All:All:All")) {
        $msg = xarML('Not authorized to add #(1) items',
                    'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $logstable = $xartable['dossier_logs'];

    $nextId = $dbconn->GenId($logstable);
    
    $createdate = date("Y-m-d H:i:s");

    $query = "INSERT INTO $logstable (
                  logid,
                  contactid,
                  ownerid,
                  logtype,
                  logdate,
                  createdate,
                  notes)
            VALUES (?,?,?,?,?,?,?)";

    $bindvars = array(
              $nextId,
              $contactid,
              $ownerid,
              $logtype,
              $logdate,
              $createdate,
              $notes);
              
    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

    $logid = $dbconn->PO_Insert_ID($logstable, 'logid');

    return $logid;
}

?>
