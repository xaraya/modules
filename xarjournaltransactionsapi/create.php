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
function labaccounting_journaltransactionsapi_create($args)
{
    extract($args);

    $invalid = array();
    if (!isset($title) || !is_string($title)) {
        $invalid[] = 'title';
    }
    if (!isset($journalid) || !is_numeric($journalid)) {
        $invalid[] = 'journalid';
    }
    
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'journals', 'create', 'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (!isset($creatorid) || !is_string($creatorid)) {
        $creatorid = xarSessionGetVar('uid');
    }
    
    if (!xarSecurityCheck('AddJournal', 1, 'Item', "$title:All:All")) {
        $msg = xarML('Not authorized to add #(1) items',
                    'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $transactions_table = $xartable['labaccounting_journaltransactions'];

    $nextId = $dbconn->GenId($transactions_table);

    $query = "INSERT INTO $transactions_table (
                    transactionid,
                    journalid,
                    transtype,
                    
                    creatorid,
                    title,
                    details,
                    
                    transnum,
                    amount,
                    transdate,
                    
                   source,
                   sourceid,
                    
                    isinvoice,
                    verified,
                    cleared,
                    status)
                VALUES (?,?,?, ?,?,?, ?,?,?, ?,?, ?,?,?,?)";
            
    $bindvars = array(
                    $nextId,
                    $journalid,
                    $transtype,
                    
                    $creatorid ? $creatorid : 0,
                    $title,
                    $details,
                    
                    $transnum,
                    $amount,
                    $transdate,
                    
                    $source,
                    $sourceid,
                    
                    $isinvoice,
                    $verified,
                    $cleared,
                    $status);
    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

// PRIVATE INITIALLY SET BASED ON USER PREFERENCE


    $journalid = $dbconn->PO_Insert_ID($transactions_table, 'transactionid');

    return $journalid;
}

?>