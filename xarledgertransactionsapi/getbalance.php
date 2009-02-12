<?php

function labaccounting_ledgertransactionsapi_getbalance($args)
{
    extract($args);

    $invalid = array();
    if (!isset($ledgerid) || !is_numeric($ledgerid)) {
        $invalid[] = 'ledgerid';
    }
    if (!isset($balancedate) || empty($balancedate)) {
        $invalid[] = 'balancedate';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'ledgertransactions', 'getbalance', 'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $ledgertransactions_table = $xartable['labaccounting_ledgertransactions'];

    $query = "SELECT SUM(amount)
            FROM $ledgertransactions_table
            WHERE transdate < ?
            GROUP BY ledgerid = ?";
    $result = &$dbconn->Execute($query,array($ledgerid,$balancedate));

    if (!$result) return;

    list($balance) = $result->fields;

    $result->Close();
/*
    if (!xarSecurityCheck('ReadXTask', 1, 'Item', "$task_name:All:$taskid")) {
        return;
    }
*/

    return $balance;
}

?>