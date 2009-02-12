<?php

function labaccounting_journaltransactionsapi_countitems($args)
{
	extract($args);

    $invalid = array();
    if (
        (
            !isset($journalid) || !is_numeric($journalid)
        ) && (
            !isset($journalidlist) || !is_array($journalidlist)
        )
       ) {
        $invalid[] = 'journalid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'journaltransactions', 'countitems', 'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
	
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();
    
    $transactions_table = $xartable['labaccounting_journaltransactions'];

    $sql = "SELECT COUNT(1) FROM $transactions_table";
            
    $whereclause = array();
            
    if(isset($journalid)) {
        $whereclause[] = "journalid=".$journalid;
    }        
    if(isset($journalidlist)) {
        $whereclause[] = "journalid IN (".implode(",", $journalidlist).")";
    } 
    if (isset($monthshown) && $monthshown > 0) {
        $whereclause[] = "MONTH(transdate)=".$monthshown;
    }
    if (isset($yearshown) && $yearshown > 0) {
        $whereclause[] = "YEAR(transdate)=".$yearshown;
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