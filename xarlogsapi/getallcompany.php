<?php

function dossier_logsapi_getallcompany($args)
{
    extract($args);
    
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($company) || !is_string($company)) {
        $invalid[] = 'company';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'worklog', 'getall', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $items = array();

    if (!xarSecurityCheck('TeamDossierAccess', 0, 'Contact', "All:All:All:All")) {//TODO: security
        /* FAIL SILENTLY
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

    $contactstable = $xartable['dossier_contacts'];

    $sql = "SELECT a.logid,
                  a.contactid,
                  b.sortname,
                  b.sortcompany,
                  a.ownerid,
                  a.logtype,
                  a.logdate,
                  a.createdate,
                  a.notes
            FROM $logstable a, $contactstable b
            WHERE a.contactid = b.contactid";
            
    $whereclause = array();

    $whereclause[] = "b.company = ?";
    $bindvars = array($company);
    
    if(!empty($maxdate)) {
        $whereclause[] = "a.logdate < ?";
        $bindvars[] = date("Y-m-d H:i:s", strtotime($maxdate));
    }
    if(!empty($mindate)) {
        $whereclause[] = "a.logdate >= ?";
        $bindvars[] = date("Y-m-d H:i:s", strtotime($mindate));
    }
    if(!empty($logtype)) {
        $whereclause[] = "a.logtype = ?";
        $bindvars = array($logtype);
    }
    if(count($whereclause) > 0) {
        $sql .= " AND ".implode(" AND ", $whereclause);
    }
    
    $sql .= " ORDER BY a.logdate DESC";

    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1,$bindvars);
    
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($logid,
              $contactid,
              $sortname,
              $sortcompany,
              $ownerid,
              $logtype,
              $logdate,
              $createdate,
              $notes) = $result->fields;
        $items[] = array('logid'        => $logid,
                          'contactid'   => $contactid,
                          'sortname'    => $sortname,
                          'sortcompany' => $sortcompany,
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
