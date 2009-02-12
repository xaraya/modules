<?php

function accessmethods_userapi_countitems($args)
{
	extract($args);
	
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $accessmethodstable = $xartable['accessmethods'];

    $sql = "SELECT COUNT(1)
            FROM $accessmethodstable";

    $whereclause = array();
    if(!empty($clientid)) {
        $whereclause[] = "clientid = '".$clientid."'";
    }
    if(!empty($accesstype)) {
        $whereclause[] = "accesstype = '".$accesstype."'";
    }
    if(!empty($sla)) {
        $whereclause[] = "sla = '".$sla."'";
    }
    if(!empty($webmasterid)) {
        $whereclause[] = "webmasterid = '".$webmasterid."'";
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
