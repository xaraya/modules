<?php

function dossier_logsapi_countiincompany($args)
{
	extract($args);
    
    if (!isset($sortby)) {
        $sortby = "sortcompany";
    }
	
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();
    
    $logstable = $xartable['dossier_logs'];

    $contactstable = $xartable['dossier_contacts'];

    $sql = "SELECT COUNT(1)
            FROM $logstable a, $contactstable b
            WHERE a.contactid = b.contactid";
    $whereclause = array();
    
    $whereclause[] = "b.company = '".$company."'";
    
    if(!empty($maxdate)) {
        $whereclause[] = "logdate < '".date("Y-m-d H:i:s",strtotime($maxdate))."'";
    }
    if(!empty($mindate)) {
        $whereclause[] = "logdate >= '".date("Y-m-d H:i:s",strtotime($mindate))."'";
    }
    if(!empty($logtype)) {
        if($logtype == "individual") {
            $whereclause[] = "logtype != 'Broadcast Email'";
        } else {
            $whereclause[] = "logtype = '".$logtype."'";
        }
    }
    if(count($whereclause) > 0) {
        $sql .= " WHERE ".implode(" AND ", $whereclause);
    }
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}
?>
