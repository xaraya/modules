<?php

function dossier_relationshipsapi_countitems($args)
{
	extract($args);
    
    if (!isset($sortby)) {
        $sortby = "sortcompany";
    }
	
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();
    
    $relationshipstable = $xartable['dossier_relationships'];

    $sql = "SELECT COUNT(1)
            FROM $relationshipstable";
    $whereclause = array();
    if(!empty($contactid)) {
        $whereclause[] = "contactid = '".$contactid."'";
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
