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
function labaccounting_journaltransactionsapi_getinvoice($args)
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

    if (!xarSecurityCheck('ViewJournal', 0, 'Item', "All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
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
            WHERE b.journalid = a.journalid
						AND a.isinvoice = 0";
            
    $whereclause = array();
    $whereclause[] = "a.journalid = ?";
    
    $bindvars = array($journalid);
                
    if(isset($journalidlist) && is_array($journalidlist)) {
        $whereclause[] = "a.journalid IN (".implode(",", $journalidlist).")";
    }
    if (isset($creatorid) && $creatorid > 0) {
        $whereclause[] = "a.creatorid = ?";
        $bindvars[] = $creatorid;
    }
    if (isset($startdate) && !empty($startdate)) {
        $whereclause[] = "a.transdate > ?";
        $bindvars[] = $startdate;
    }
    if (isset($enddate) && !empty($enddate)) {
        $whereclause[] = "a.transdate <= ?";
        $bindvars[] = $enddate;
    }
    
    if(count($whereclause) > 0) $sql .= " AND ".implode(" AND ", $whereclause);

    $sql .= " ORDER BY a.transdate, a.amount, a.journalid, a.title";
//echo $sql."<pre>"; print_r($bindvars); die("</pre>");
    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1, $bindvars);
    
    if ($dbconn->ErrorNo() != 0) return;

    $items = array();

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
        if (xarSecurityCheck('ReadJournal', 0, 'Item', "All:All:$journalid")) {
            
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