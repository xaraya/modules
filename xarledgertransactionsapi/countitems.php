<?php

function labaccounting_ledgertransactionsapi_countitems($args)
{
	extract($args);
	
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $ledgers_table = $xartable['labaccounting_ledgertransactions'];

    $sql = "SELECT COUNT(1) FROM $ledgertransactions_table";
            
    $whereclause = array();
            
    if(isset($ledgerid)) {
        $whereclause[] = "ledgerid=".$ledgerid;
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