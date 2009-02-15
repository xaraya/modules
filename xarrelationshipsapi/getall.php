<?php

function dossier_relationshipsapi_getall($args)
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
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'relationships', 'getall', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $items = array();

    if (!xarSecurityCheck('ReadDossierLog', 0, 'Log')) {//TODO: security
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
    
    $relationshipstable = $xartable['dossier_relationships'];

    $contactstable = $xartable['dossier_contacts'];

    $sql = "SELECT a.relationshipid,
                  a.connectedid,
                  b.sortname,
                  b.sortcompany,
                  b.userid,
                  a.relationship,
                  a.dateadded,
                  a.private,
                  a.notes
            FROM $relationshipstable a, $contactstable b
            WHERE b.contactid = a.connectedid";
            
    $whereclause = array();
    if(!empty($contactid)) {
        $whereclause[] = "a.contactid = '".$contactid."'";
    }
    if(!empty($private)) {
        $whereclause[] = "a.private = '".$private."'";
    }
    if(!empty($relationship)) {
        $whereclause[] = "a.relationship = '".$relationship."'";
    }
    if(count($whereclause) > 0) {
        $sql .= " AND ".implode(" AND ", $whereclause);
    }
    
    $sql .= " ORDER BY b.sortname";

    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1);
    
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($relationshipid,
              $connectedid,
              $sortname,
              $sortcompany,
              $userid,
              $relationship,
              $dateadded,
              $private,
              $notes) = $result->fields;
        $items[] = array('relationshipid'   => $relationshipid,
                          'contactid'       => $contactid,
                          'connectedid'       => $connectedid,
                          'sortname'        => $sortname,
                          'sortcompany'     => $sortcompany,
                          'userid'         => $userid,
                          'relationship'    => $relationship,
                          'dateadded'       => $dateadded,
                          'private'         => $private,
                          'notes'           => $notes);
    }

    $result->Close();

    return $items;
}

?>
