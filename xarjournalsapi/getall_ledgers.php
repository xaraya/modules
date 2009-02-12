<?php

/**
 *
 *
 * Administration System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage labaccounting module
 * @author Chad Kraeft <stego@xaraya.com>
*/
function labaccounting_journalsapi_getall_ledgers($args)
{
    extract($args);

    $invalid = array();
    if (!isset($journalid) || !is_numeric($journalid)) {
        $invalid[] = 'journalid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'journals', 'getall_ledgers', 'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if (!xarSecurityCheck('ManageJournal', 0, 'Journals', "All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();
    
    $ledgers_table = $xartable['labaccounting_ledgers'];
    $journalXledger_table = $xartable['labaccounting_journalXledger'];

    $sql = "SELECT a.journalid,
                    a.ledgerid,
                    a.normalbalance,
                   b.parentid,
                   b.ownerid,
                   b.accttype,
                   b.chartacctnum,
                   CASE 
                        WHEN length(b.chartacctnum) > 0 THEN b.chartacctnum
                        ELSE 100000
                    END AS chartorder,
                   b.account_title,
                   b.notes
            FROM $journalXledger_table a, $ledgers_table b
            WHERE b.ledgerid = a.ledgerid
            AND a.journalid = $journalid
            ORDER BY chartorder, b.account_title";

    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR'. $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR: ',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $items = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($journalid,
            $ledgerid,
            $normalbalance,
            $parentid,
            $ownerid,
            $accttype,
            $chartacctnum,
            $chartorder,
            $title,
            $notes) = $result->fields;
        if (xarSecurityCheck('ManageJournal', 0, 'Journals', "All:All:All")) {
            
            $items[] = array('journalid'        => $journalid,
                            'ledgerid'          => $ledgerid,
                            'normalbalance'     => $normalbalance,
                            'parentid'          => $parentid,
                            'ownerid'           => $ownerid,
                            'accttype'          => $accttype,
                            'chartacctnum'      => $chartacctnum,
                            'chartorder'        => $chartorder,
                            'title'             => $title,
                            'notes'             => $notes);
        }
    }

    $result->Close();

    return $items;
}

?>