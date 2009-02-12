<?php

function labaccounting_journaltransactionsapi_getprevinvoice($args)
{
    extract($args);

    if (!isset($nextinvoice['transdate']) || empty($nextinvoice['transdate'])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'transdate', 'journaltransactions', 'get', 'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $transactions_table = $xartable['labaccounting_journaltransactions'];

    $query = "SELECT transactionid,
                    journalid,
                    transtype,
                    creatorid,
                    title,
                    details,
                   source,
                   sourceid,
                    transnum,
                    amount,
                    transdate,
                    isinvoice,
                    verified,
                    cleared,
                    status
            FROM $transactions_table
            WHERE journalid = ?
            AND transdate < ?
            AND isinvoice = 1
            ORDER BY transdate DESC";
    $result = &$dbconn->Execute($query,array($nextinvoice['journalid'], $nextinvoice['transdate']));
//echo $query." ".$nextinvoice['journalid']." ".$nextinvoice['transdate'];
    if (!$result) return;

    if ($result->EOF) return;

    list($transactionid,
        $journalid,
        $transtype,
        $creatorid,
        $title,
        $details,
         $source,
         $sourceid,
        $transnum,
        $amount,
        $transdate,
        $isinvoice,
        $verified,
        $cleared,
        $status) = $result->fields;
        
    $result->Close();

    if (!xarSecurityCheck('ReadJournal', 1, 'Item', "All:All:$journalid")) {
        return;
    }

    $item = array('transactionid'   => $transactionid,
                'journalid'         => $journalid,
                'transtype'         => $transtype,
                'creatorid'         => $creatorid,
                'title'             => $title,
                'details'           => $details,
                'source'            => $source,
                'sourceid'          => $sourceid,
                'transnum'          => $transnum,
                'amount'            => $amount,
                'transdate'         => $transdate,
                'isinvoice'         => $isinvoice,
                'verified'          => $verified,
                'cleared'           => $cleared,
                'status'            => $status);

    return $item;
}

?>