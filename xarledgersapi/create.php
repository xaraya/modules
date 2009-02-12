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
function labaccounting_ledgersapi_create($args)
{
    extract($args);

    $invalid = array();
    if (!isset($account_title) || !is_string($account_title)) {
        $invalid[] = 'account_title';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'ledgers', 'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (!isset($ownerid) || !is_string($ownerid)) {
        $ownerid = xarSessionGetVar('uid');
    }
    
    if (!xarSecurityCheck('AddLedger', 1, 'Item', "$account_title:All:All")) {
        $msg = xarML('Not authorized to add #(1) items',
                    'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }
    
    $nextchartacctnum = xarModAPIFunc('labaccounting', 'ledgers', 'getnextacctnum', array('chartacctnum' => $chartacctnum, 'accttype' => $accttype));

    if($nextchartacctnum == false) return;
    
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $labaccountingtable = $xartable['labaccounting_ledgers'];

    $nextId = $dbconn->GenId($labaccountingtable);

    $query = "INSERT INTO $labaccountingtable (
                  ledgerid,
                  parentid,
                  ownerid,
                  accttype,
                  chartacctnum,
                  account_title,
                  normalbalance,
                  notes)
                VALUES (?,?,?,?,?,?,?,?)";
            
    $bindvars = array(
                    $nextId,
                    $parentid ? $parentid : 0,
                    $ownerid ? $ownerid : 0,
                    $accttype,
                    $nextchartacctnum,
                    $account_title,
                    $normalbalance,
                    $notes);
    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

    $ledgerid = $dbconn->PO_Insert_ID($labaccountingtable, 'ledgerid');

    return $ledgerid;
}

?>