<?php

function labaccounting_journaltransactionsapi_getbalance($args)
{
	extract($args);

    $invalid = array();
    if (!isset($journalid) || !is_numeric($journalid) ) {
        $invalid[] = 'journalid';
    }
    if (!isset($balancedate) || empty($balancedate)) {
        $invalid[] = 'balancedate';
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

    $sql = "SELECT SUM(amount) FROM $transactions_table";
            
    $whereclause = array();
    $whereclause[] = "journalid = ?";
    $bindvars = array($journalid);
            
    if (isset($balancedate) && !empty($balancedate)) {
        $whereclause[] = "transdate <= ?";
        $bindvars[] = $balancedate;
    }
    
    if(count($whereclause) > 0) $sql .= " WHERE ".implode(" AND ", $whereclause);
    
    $result = $dbconn->Execute($sql, $bindvars);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    list($balance) = $result->fields;

    $result->Close();

    return $balance;
}
?>