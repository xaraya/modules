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
function labaccounting_journaltransactionsapi_getall($args)
{
    extract($args);
    
    if (!isset($orderby)) {
        $orderby = "";
    }    
    if (empty($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'journals', 'getall', 'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    if(isset($journalidlist) and count($journalidlist) == 1) {
        $journalid = $journalidlist[0];
        $journalidlist = NULL;
    }

    if (!xarSecurityCheck('ViewLedger', 0, 'Item', "All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $beginningbalance = 0.00;
    if(isset($yearshown) && $yearshown > 0 and isset($journalid) && $journalid > 0) {
        $startmonth = (isset($monthshown) && $monthshown > 0) ? $monthshown : 1;
        $balancedate = xarLocaleFormatDate("%Y-%m-%d %H:%M:%S",mktime(0,0,0,$startmonth,1,$yearshown));
        $beginningbalance = xarModAPIFunc('labaccounting', 'journaltransactions', 'getbalance',
                                    array('journalid' => $journalid,
                                        'balancedate' => $balancedate));
    }
    
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $journals_table = $xartable['labaccounting_journals'];
    $journaltransactions_table = $xartable['labaccounting_journaltransactions'];
    $ledgertransactions_table = $xartable['labaccounting_ledgertransactions'];

    $sql = "SELECT DISTINCT 
                    a.transactionid,
                    a.journalid,
                    b.account_title,
                    b.journaltype,
                    a.transtype,
                    a.creatorid,
                    a.title,
                    a.details,
                    a.source,
                    a.sourceid,
                    a.transnum,
                    a.amount,
                    a.transdate,
                    a.isinvoice,
                    a.verified,
                    a.cleared,
                    a.status
            FROM $journaltransactions_table a, $journals_table b
            WHERE b.journalid = a.journalid";
            
    $whereclause = array();
            
    if(isset($journalid) && $journalid > 0) {
        $whereclause[] = "a.journalid=".$journalid;
    }
    if(isset($journalidlist) && is_array($journalidlist)) {
        $whereclause[] = "a.journalid IN (".implode(",", $journalidlist).")";
    }
    if (isset($creatorid) && $creatorid > 0) {
        $whereclause[] = "a.creatorid=".$creatorid;
    }
    if (isset($monthshown) && $monthshown > 0) {
        $whereclause[] = "MONTH(a.transdate)=".$monthshown;
    }
    if (isset($yearshown) && $yearshown > 0) {
        $whereclause[] = "YEAR(a.transdate)=".$yearshown;
    }
    
    if(count($whereclause) > 0) $sql .= " AND ".implode(" AND ", $whereclause);

    $sql .= " ORDER BY a.transdate, a.amount DESC, a.journalid, a.title";
    
    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1);
    
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR'. $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR: ',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $items = array();
    $runningbalance = $beginningbalance;
    $totalcredits = 0;
    $totaldebits = 0;
    for (; !$result->EOF; $result->MoveNext()) {
        list($transactionid,
            $journalid,
            $journal_title,
            $journaltype,
            $transtype,
            $creatorid,
            $title,
            $details,
             $source,
             $sourceid,
            $transnum,
            $amount,
            $transdate,
            $isinvoice,
            $verified,
            $cleared,
            $status) = $result->fields;
        if (xarSecurityCheck('ReadLedger', 0, 'Item', "All:All:$journalid")) {
            $runningbalance += $amount;
            if($amount > 0) {
                $totalcredits += $amount;
            } elseif ($amount < 0) {
                $totaldebits += $amount;
            }
            $items[$transactionid] = array(
                    'transactionid'    => $transactionid,
                    'journalid'         => $journalid,
                    'journal_title'     => $journal_title,
                    'journaltype'       => $journaltype,
                    'transtype'         => $transtype,
                    'creatorid'         => $creatorid,
                    'title'             => $title,
                    'details'           => $details,
                    'source'            => $source,
                    'sourceid'          => $sourceid,
                    'transnum'          => $transnum,
                    'amount'            => $amount,
                    'balance'           => $runningbalance,
                    'totalcredits'      => $totalcredits,
                    'totaldebits'       => $totaldebits,
                    'transdate'         => $transdate,
                    'isinvoice'         => $isinvoice,
                    'verified'          => $verified,
                    'cleared'           => $cleared,
                    'status'            => $status);
        }
    }

    $result->Close();
    
    return $items;
}

?>