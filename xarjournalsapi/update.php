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
 
function labaccounting_journalsapi_update($args)
{
    extract($args);

    $invalid = array();
    if (!isset($journalid) || !is_numeric($journalid)) {
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
                            'journals',
                            'get',
                            array('journalid' => $journalid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditLedger', 1, 'Item', "$item[account_title]:All:$journalid")) {
        return;
    }
    if (!xarSecurityCheck('EditLedger', 1, 'Item', "$account_title:All:$journalid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $journals_table = $xartable['labaccounting_journals'];

    $query = "UPDATE $journals_table
            SET parentid = ?,
                owneruid = ?,
                contactid = ?,
                currency = ?,
                account_title = ?,
                journaltype = ?,
                agentuid = ?,
                acctnum = ?,
                acctlogin = ?,
                accturl = ?,
                acctpwd = ?,
                notes = ?,
                opendate = ?,
                closedate = ?,
                billdate = ?,
                status = ?,
                invoicefreq = ?,
                invoicefrequnits = ?
            WHERE journalid = ?";

    $bindvars = array(
                     $parentid ? $parentid : 0,
                    $owneruid ? $owneruid : 0,
                    $contactid ? $contactid : 0,
                    $currency,
                    $account_title,
                    $journaltype,
                    $agentuid ? $agentuid : 0,
                    $acctnum,
                    $acctlogin,
                    $accturl,
                    $acctpwd,
                    $notes,
                    $opendate,
                    $closedate,
                    $billdate,
                    $status,
                    $invoicefreq,
                    $invoicefrequnits,
                    $journalid);
              
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    return true;
}
?>