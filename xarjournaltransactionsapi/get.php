<?php

function labaccounting_journaltransactionsapi_get($args)
{
    extract($args);

    if (!isset($transactionid) || !is_numeric($transactionid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'transactionid', 'journaltransactions', 'get', 'labaccounting');
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
            WHERE transactionid = ?";
    $result = &$dbconn->Execute($query,array($transactionid));

    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

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

    if (!xarSecurityCheck('ReadJournal', 1, 'Item', "All")) {
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