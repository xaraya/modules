<?php

function dossier_logsapi_getallcontact($args)
{
    extract($args);

    $invalid = array();
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'worklog', 'getall', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $items = array();

    if (!xarSecurityCheck('ReadDossierLog', 0, 'Log', "All:All:All:All")) {//TODO: security
        /* Fail silently
        $msg = xarML('Not authorized to access #(1) items',
                    'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
        */
        return $items;
    }
    
    if(!empty($maxdate) && !empty($ttldays)) {
        $mindate = date("Y-m-d", strtotime($maxdate) - ($ttldays * 3600 * 24) );
    }
    
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();
    
    $logstable = $xartable['dossier_logs'];

    $sql = "SELECT logid,
                  contactid,
                  ownerid,
                  logtype,
                  logdate,
                  createdate,
                  notes
            FROM $logstable
            WHERE contactid = $contactid";
    
    $sql .= " ORDER BY logdate DESC";

    $result = $dbconn->Execute($sql);

    if (!$result) return;
    
    $items = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($logid,
              $contactid,
              $ownerid,
              $logtype,
              $logdate,
              $createdate,
              $notes) = $result->fields;
        $items[] = array('logid'        => $logid,
                          'contactid'   => $contactid,
                          'ownerid'     => $ownerid,
                          'logtype'     => $logtype,
                          'logdate'     => $logdate,
                          'createdate'  => $createdate,
                          'notes'       => $notes);
    }

    $result->Close();

    return $items;
}

?>
