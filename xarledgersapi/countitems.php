<?php

function labaccounting_ledgersapi_countitems($args)
{
	extract($args);
	
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $ledgers_table = $xartable['labaccounting_ledgers'];

    $sql = "SELECT COUNT(1) FROM $ledgers_table";
            
    $whereclause = array();
            
    if(isset($ownerid)) {
        $whereclause[] = "ownerid=".$ownerid;
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