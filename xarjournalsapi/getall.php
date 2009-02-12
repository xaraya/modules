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
function labaccounting_journalsapi_getall($args)
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
                    join(', ',$invalid), 'user', 'getall', 'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
/*
    if (!xarSecurityCheck('ViewLedger', 0, 'Item', "All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
*/
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $journals_table = $xartable['labaccounting_journals'];
    $journaltransactions_table = $xartable['labaccounting_journaltransactions'];

    $sql = "SELECT a.journalid,
                    a.parentid,
                    a.owneruid,
                    a.contactid,
                    a.currency,
                    
                    a.account_title,
                    a.journaltype,
                   CASE 
                        WHEN a.journaltype = 'master' THEN 1
                        WHEN a.journaltype = 'ecommerce' THEN 2
                        WHEN a.journaltype = 'roles' THEN 3
                        WHEN a.journaltype = 'dossier' THEN 4
                        WHEN a.journaltype = 'assets' THEN 5
                        WHEN a.journaltype = 'liabilities' THEN 6
                        WHEN a.journaltype = 'revenue' THEN 7
                        WHEN a.journaltype = 'cogs' THEN 8
                        WHEN a.journaltype = 'expenses' THEN 9
                        WHEN a.journaltype = 'private' THEN 10
                        WHEN a.journaltype = '' THEN 11
                        ELSE 3
                    END AS journalorder,
                    a.agentuid,
                    a.acctnum,
                    
                    a.acctlogin,
                    a.accturl,
                    a.acctpwd,
                    a.notes,
                    a.opendate,
                    
                    a.closedate,
                    a.billdate,
                    a.status,
                   CASE 
                        WHEN a.status = 'Active' THEN 1
                        WHEN a.status = 'Pending' THEN 2
                        WHEN a.status = 'Archived' THEN 3
                        WHEN a.status = '' THEN 4
                        ELSE 5
                    END AS statusorder,
                    a.invoicefreq,
                    a.invoicefrequnits,
                    SUM(b.amount)
            FROM $journals_table a
            LEFT JOIN $journaltransactions_table b
            ON b.journalid = a.journalid";
            
    $whereclause = array();
    $bindvars = array();
            
    if(isset($owneruid) && $owneruid > 0) {
        $whereclause[] = "a.owneruid = ?";
        $bindvars[] = $owneruid;
    }
            
    if(isset($agentuid) && $agentuid > 0) {
        $whereclause[] = "a.agentuid = ?";
        $bindvars[] = $agentuid;
    }
            
    if(isset($contactid) && $contactid > 0) {
        $whereclause[] = "a.contactid = ?";
        $bindvars[] = $contactid;
    }
            
    if(isset($statuslist) && is_array($statuslist) && count($statuslist) > 0 && !in_array("All", $statuslist)) {
        $whereclause[] = "a.status IN ('".implode("','",$statuslist)."')";
//        $bindvars[] = "'".implode("','",$statuslist)."'";
    }
            
    if(isset($journaltype) && $journaltype != "all" && $journaltype != "master") {
        $whereclause[] = "a.journaltype = ?";
        $bindvars[] = $journaltype;
    }
    if (!empty($parentid)) {
        $whereclause[] = "a.parentid = ?";
        $bindvars[] = $parentid;
    } elseif(isset($journaltype) && $journaltype == "master") {
        $whereclause[] = "a.parentid = ?";
        $bindvars[] = 0;
    }
    
    if(isset($q) && !empty($q)) {
        $whereclause[] = "a.account_title LIKE '%".$q."%'";
    }
    
    if(count($whereclause) > 0) $sql .= " WHERE ".implode(" AND ", $whereclause);
    
    $sql .= " GROUP BY a.journalid";
    $sql .= " ORDER BY journalorder, journalorder, statusorder, a.parentid";
    
    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1, $bindvars);
    
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR'. $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR: ',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $items = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($journalid,
            $parentid,
            $owneruid,
            $contactid,
            $currency,
            
            $account_title,
            $journaltype,
            $journalorder,
            $agentuid,
            $acctnum,

            $acctlogin,
            $accturl,
            $acctpwd,
            $notes,
            $opendate,
            
            $closedate,
            $billdate,
            $status,
            $statusorder,
            $invoicefreq,
            $invoicefrequnits,
            $balance) = $result->fields;
        if (xarSecurityCheck('JournalClient', 0, 'Journals', $journaltype)) {
            
            $journalinfo = array('journalid'        => $journalid,
                                'parentid'          => $parentid,
                                'owneruid'          => $owneruid,
                                'contactid'         => $contactid,
                                'currency'          => $currency,
                                'account_title'     => $account_title,
                                'journaltype'       => $journaltype,
                                'agentuid'          => $agentuid,
                                'acctnum'           => $acctnum,
                                'acctlogin'         => $acctlogin,
                                'accturl'           => $accturl,
                                'acctpwd'           => $acctpwd,
                                'notes'             => $notes,
                                'opendate'          => $opendate,
                                'closedate'         => $closedate,
                                'billdate'          => $billdate,
                                'status'            => $status,
                                'invoicefreq'       => $invoicefreq,
                                'invoicefrequnits'  => $invoicefrequnits,
                                'balance'           => $balance,
                                'subjournals'       => array() );
            
            if($parentid > 0 && isset($nested) && $nested == true && isset($items[$parentid])) {
                $items[$parentid]['subjournals'][$journalid] = $journalinfo;
            } else {
                $items[$journalid] = $journalinfo;
            }
        }
    }

    $result->Close();
    
    return $items;
}

?>