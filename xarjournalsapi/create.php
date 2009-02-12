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
function labaccounting_journalsapi_create($args)
{
    extract($args);

    $invalid = array();
    if (!isset($account_title) || !is_string($account_title)) {
        $invalid[] = 'account_title';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'journals', 'create', 'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (!isset($owneruid) || !is_string($owneruid)) {
        $owneruid = xarSessionGetVar('uid');
    }
    
    if (!xarSecurityCheck('AddJournal', 1, 'Item', "$account_title:All:All")) {
        $msg = xarML('Not authorized to add #(1) items',
                    'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $labaccounting_table = $xartable['labaccounting_journals'];

    $nextId = $dbconn->GenId($labaccounting_table);

    $query = "INSERT INTO $labaccounting_table (
                    journalid,
                    parentid,
                    owneruid,
                    contactid,
                    currency,
                    
                    account_title,
                    journaltype,
                    agentuid,
                    acctnum,
                    acctlogin,
                    
                    accturl,
                    acctpwd,
                    notes,
                    opendate,
                    closedate,
                    
                    billdate,
                    status,
                    invoicefreq,
                    invoicefrequnits)
                VALUES (?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?)";
            
    $bindvars = array(
                    $nextId,
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
                    $invoicefrequnits);
    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

// PRIVATE INITIALLY SET BASED ON USER PREFERENCE


    $journalid = $dbconn->PO_Insert_ID($labaccounting_table, 'journalid');

    return $journalid;
}

?>