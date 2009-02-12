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
 
function labaccounting_ledgertransactionsapi_update($args)
{
    extract($args);

    $invalid = array();
    if (!isset($transactionid) || !is_numeric($transactionid)) {
        $invalid[] = 'Transaction ID';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update', 'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('labaccounting',
                            'ledgertransactions',
                            'get',
                            array('transactionid' => $transactionid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditLedger', 1, 'Item', $item['ledgerid'])) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $ledgertransactions_table = $xartable['labaccounting_ledgertransactions'];

    $query = "UPDATE $ledgertransactions_table
                SET title = ?,
                       details = ?,
                       transnum = ?,
                       amount = ?,
                       status = ?,
                       transdate = ?
                WHERE transactionid = ?";

    $bindvars = array(
                     $title,
                     $details,
                     $transnum,
                     $amount,
                     $status,
                     $transdate,
                     $transactionid);
              
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    return true;
}
?>