<?php
/**
 * Administration System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage labAccounting Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author St.Ego
*/
function labaccounting_ledgertransactionsapi_create($args)
{
    extract($args);

    $invalid = array();
    if (!isset($ledgerid) || !is_numeric($ledgerid)) {
        $invalid[] = 'ledgerid';
    }
    if (!isset($title) || !is_string($title)) {
        $invalid[] = 'title';
    }
    if (!isset($journaltransid) || !is_numeric($journaltransid)) {
        $journaltransid = 0;
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'ledgertransactions', 'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (!isset($creatorid) || !is_string($creatorid)) {
        $creatorid = xarSessionGetVar('uid');
    }
    
    if (!xarSecurityCheck('AddLedger', 1, 'Item', "$ledgerid")) {
        $msg = xarML('Not authorized to add #(1) items',
                    'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $ledgertransaction_table = $xartable['labaccounting_ledgertransactions'];

    $nextId = $dbconn->GenId($ledgertransaction_table);

    $query = "INSERT INTO $ledgertransaction_table (
                  transactionid,
                   ledgerid,
                   journaltransid,
                   creatorid,
                   title,
                   details,
                   transnum,
                   amount,
                   status,
                   transdate)
                VALUES (?,?,?, ?,?, ?,?,?, ?,?)";
            
    $bindvars = array(
                    $nextId,
                    $ledgerid,
                    $journaltransid,
                    $creatorid,
                    $title,
                    $details,
                    $transnum,
                    $amount,
                    $status,
                    $transdate);
    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

    $transactionid = $dbconn->PO_Insert_ID($ledgertransaction_table, 'transactionid');

    return $ledgerid;
}

?>