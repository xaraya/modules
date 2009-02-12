<?php

function labaccounting_journalsapi_countitems($args)
{
	extract($args);
	
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $labaccounting_table = $xartable['labaccounting_journals'];

    $sql = "SELECT COUNT(1) FROM $labaccounting_table";
            
    $whereclause = array();
            
    if(isset($owneruid)) {
        $whereclause[] = "owneruid=".$owneruid;
    }       
    
    if (!empty($parentid)) {
        $whereclause[] = "parentid=".$parentid;
    }
    
    if(count($whereclause) > 0) $sql .= " WHERE ".implode(" AND ", $whereclause);
    
    
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}
?>