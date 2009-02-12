<?php

function labaccounting_ledgertransactionsapi_get($args)
{
    extract($args);

    if (!isset($transactionid) || !is_numeric($transactionid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item ID', 'ledgertransactions', 'get', 'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $ledgertransactions_table = $xartable['labaccounting_ledgertransactions'];

    $query = "SELECT transactionid,
                   ledgerid,
                   journaltransid,
                   creatorid,
                   title,
                   details,
                   transnum,
                   amount,
                   status,
                   transdate
            FROM $ledgertransactions_table
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
         $ledgerid,
         $journaltransid,
         $creatorid,
         $title,
         $details,
         $transnum,
         $amount,
         $status,
         $transdate) = $result->fields;

    $result->Close();
/*
    if (!xarSecurityCheck('ReadXTask', 1, 'Item', "$task_name:All:$taskid")) {
        return;
    }
*/
    $item = array('transactionid'   => $transactionid,
                'ledgerid'          => $ledgerid,
                'journaltransid'          => $journaltransid,
                'creatorid'         => $creatorid,
                'title'             => $title,
                'details'           => $details,
                'transnum'          => $transnum,
                'amount'            => $amount,
                'status'            => $status,
                'transdate'         => $transdate);

    return $item;
}

?>