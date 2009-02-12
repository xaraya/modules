<?php

function labaccounting_ledgersapi_getnextacctnum($args) {

    extract($args);
    
    $invalid = array();

    if (!isset($chartacctnum) || empty($chartacctnum)) {
        $invalid[] = "chartacctnum";
    }

    if (!isset($accttype) || empty($accttype)) {
        $invalid[] = "accttype";
    }
    
    if(count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(", ", $invalid), 'ledgers', 'getnextacctnum', 'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $ledgers_table = $xartable['labaccounting_ledgers'];

    $query = "SELECT MAX(chartacctnum)
            FROM $ledgers_table
            WHERE chartacctnum = ?
            AND accttype = ?
            GROUP BY accttype";
    $result = &$dbconn->Execute($query,array($chartacctnum, $accttype));

    if (!$result) return;

    if ($result->EOF) {
        $lastchartacctnum = $chartacctnum;
    } else {
        list($lastchartacctnum) = $result->fields;
    }

    $result->Close();
    
    if($lastchartacctnum < $chartacctnum) {
        $nextacctnum = $chartacctnum;
    } else {
        $nextacctnum = $lastchartacctnum + 1;
    }
    
    return $nextacctnum;  
}

?>