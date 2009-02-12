<?php

function labaccounting_ledgersapi_get($args)
{
    extract($args);

    if (!isset($ledgerid) || !is_numeric($ledgerid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item ID', 'ledgers', 'get', 'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $ledgers_table = $xartable['labaccounting_ledgers'];

    $query = "SELECT ledgerid,
                      parentid,
                      ownerid,
                      accttype,
                      chartacctnum,
                      account_title,
                      normalbalance,
                      notes
            FROM $ledgers_table
            WHERE ledgerid = ?";
    $result = &$dbconn->Execute($query,array($ledgerid));

    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    list($ledgerid,
        $parentid,
        $ownerid,
        $accttype,
        $chartacctnum,
        $account_title,
        $normalbalance,
        $notes) = $result->fields;

    $result->Close();

    if (!xarSecurityCheck('ReadLedger', 1, 'Item', "$account_title:All:$ledgerid")) {
        return;
    }

    $item = array('ledgerid'        => $ledgerid,
                'parentid'          => $parentid,
                'ownerid'           => $ownerid,
                'accttype'          => $accttype,
                'chartacctnum'      => $chartacctnum,
                'account_title'     => $account_title,
                'normalbalance'     => $normalbalance,
                'notes'             => $notes);

    return $item;
}

?>