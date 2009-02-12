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
function labaccounting_ledgertransactionsapi_getall($args)
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
    if (!isset($yearshown)) {
        $yearshown = date('Y');
    }

    $invalid = array();
    if (!isset($ledgerid) || !is_numeric($ledgerid)) {
        $invalid[] = 'ledgerid';
    }
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

    if (!xarSecurityCheck('ViewLedger', 0, 'Item', "All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $ledgertransactions_table = $xartable['labaccounting_ledgertransactions'];

    $sql = "SELECT transactionid,
                   ledgerid,
                   journaltransid,
                   creatorid,
                   title,
                   details,
                   transnum,
                   amount,
                   status,
                   transdate
            FROM $ledgertransactions_table";
            
    $whereclause = array();
            
    if(isset($ledgerid) && $ledgerid > 0) {
        $whereclause[] = "ledgerid=".$ledgerid;
    }
    if (isset($monthshown) && $monthshown > 0) {
        $startdate = date("Y-m-d H:i:s",mktime(0, 0, 0, 1, $monthshown, $yearshown));
        $enddate = date("Y-m-d H:i:s",mktime(0, 0, 0, -1, $monthshown + 1, $yearshown));
        $whereclause[] = "transdate < '".$enddate."'";
        $whereclause[] = "transdate > '".$startdate."'";
    } else {
        $startdate = date("Y-m-d H:i:s",mktime(0, 0, 0, 1, 1, $yearshown));
        $enddate = date("Y-m-d H:i:s",mktime(0, 0, 0, -1, 1, $yearshown + 1));
        $whereclause[] = "transdate < '".$enddate."'";
        $whereclause[] = "transdate > '".$startdate."'";
    }
    
    if(count($whereclause) > 0) $sql .= " WHERE ".implode(" AND ", $whereclause);
    
    $sql .= " ORDER BY transdate";

    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1);
    
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR'. $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR: ',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $items = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($transactionid,
             $ledgerid,
             $journaltransid,
             $creatorid,
             $title,
             $details,
             $transnum,
             $amount,
             $status,
             $transdate) = $result->fields;
             
        $items[] = array('transactionid'    => $transactionid,
                        'ledgerid'          => $ledgerid,
                        'journaltransid'          => $journaltransid,
                        'creatorid'         => $creatorid,
                        'title'             => $title,
                        'details'           => $details,
                        'transnum'          => $transnum,
                        'amount'            => $amount,
                        'status'            => $status,
                        'transdate'         => $transdate);
                            
    }

    $result->Close();

    return $items;
}

?>