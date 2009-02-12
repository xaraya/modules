<?php
/**
 * XTasks Module - A task management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xTasks Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
 
function labaccounting_ledgersapi_update($args)
{
    extract($args);

    $invalid = array();
    if (!isset($ledgerid) || !is_numeric($ledgerid)) {
        $invalid[] = 'Ledger ID';
    }
    if (!isset($account_title) || !is_string($account_title)) {
        $invalid[] = 'account_title';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update', 'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('labaccounting',
                            'ledgers',
                            'get',
                            array('ledgerid' => $ledgerid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditLedger', 1, 'Item', "$item[account_title]:All:$ledgerid")) {
        return;
    }
    if (!xarSecurityCheck('EditLedger', 1, 'Item', "$account_title:All:$ledgerid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $ledgers_table = $xartable['labaccounting_ledgers'];

    $query = "UPDATE $ledgers_table
            SET parentid = ?,
                  ownerid = ?,
                  accttype = ?,
                  chartacctnum = ?,
                  account_title = ?,
                  normalbalance = ?,
                  notes = ?
            WHERE ledgerid = ?";

    $bindvars = array(
                     $parentid,
                     $ownerid,
                     $accttype,
                     $chartacctnum,
                     $account_title,
                     $normalbalance,
                     $notes,
                     $ledgerid);
              
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    return true;
}
?>