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
function labaccounting_ledgersapi_getall($args)
{
    extract($args);
    
    if (!isset($orderby)) {
        $orderby = "";
    }  
    if (!isset($active)) {
        $active = "";
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

    if (!xarSecurityCheck('ViewLedger', 0, 'Item', "All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $object = xarModAPIFunc('dynamicdata','user','getitems',
                          array('module'    => 'labaccounting',
                                'itemtype'  => 5,
                                'numitems'  => -1,
                                'where'     => ($active ? "active eq 1" : NULL),
                                'startnum'  => 0,
                                'sort'    => 'accountnumstart',
                                'getobject' => 1));
                                
    if($object === false) return;
    
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $ledgers_table = $xartable['labaccounting_ledgers'];

    $sql = "SELECT ledgerid,
                   parentid,
                   ownerid,
                   accttype,
                   chartacctnum,
                   CASE 
                        WHEN length(chartacctnum) > 0 THEN chartacctnum
                        ELSE 100000
                    END AS chartorder,
                   account_title,
                   normalbalance,
                   notes
            FROM $ledgers_table";
            
    $whereclause = array();
    $bindvars = array();
            
    if(isset($owneruid) && $owneruid > 0) {
        $whereclause[] = "owneruid=?";
        $bindvars[] = $owneruid;
    }
    if (!empty($parentid)) {
        $whereclause[] = "parentid=?";
        $bindvars[] = $parentid;
    }
    if (isset($accttype) && !empty($accttype)) {
        $whereclause[] = "accttype=?";
        $bindvars[] = $accttype;
    }
    
    if(count($whereclause) > 0) $sql .= " WHERE ".implode(" AND ", $whereclause);
    
    $sql .= " ORDER BY chartorder, accttype";

    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1, $bindvars);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR'. $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR: ',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $items = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($ledgerid,
             $parentid,
             $ownerid,
             $accttype,
             $chartacctnum,
             $chartacctnum2,
             $account_title,
             $normalbalance,
             $notes) = $result->fields;
        if (xarSecurityCheck('ReadLedger', 0, 'Item', "$account_title:All:$ledgerid")) {
    
            $chartitemlist = xarModAPIFunc('dynamicdata','user','getitems',
                                  array('module'    => 'labaccounting',
                                        'itemtype'  => 5,
                                        'where'     => "accountnumstart le ".$chartacctnum,
                                        'sort'      => "accountnumstart DESC",
                                        'numitems'  => 1,
                                        'startnum'  => 1));
                                
            $chartiteminfo = array_shift($chartitemlist);
                                        
            $items[] = array('ledgerid' => $ledgerid,
                            'parentid' => $parentid,
                            'ownerid' => $ownerid,
                            'accttype' => $accttype,
                            'chartacctnum' => $chartacctnum,
                            'chartaccttype' => $chartiteminfo['accttype'],
                            'account_title' => $account_title,
                            'normalbalance' => $normalbalance,
                            'notes' => $notes);
        }
    }

    $result->Close();

    return $items;
}

?>