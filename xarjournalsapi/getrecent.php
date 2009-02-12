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
function labaccounting_journalsapi_getrecent($args)
{
    extract($args);
    
    if (!isset($orderby)) {
        $orderby = "";
    }    
    if (empty($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = 3;
    }
    if (!isset($owneruid)) {
        $owneruid = xarUserGetVar('uid');
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

    if (!xarSecurityCheck('ViewLedger', 0, 'Item', "All:All:All")) {//TODO: security
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

    $sql = "SELECT DISTINCT 
                    b.journalid,
                    b.account_title,
                    b.journaltype,
                    b.agentuid
            FROM $journaltransactions_table a, $journals_table b
            WHERE b.journalid = a.journalid";
            
    $whereclause = array();

    if(isset($journalidlist) && is_array($journalidlist)) {
        $whereclause[] = "a.journalid IN (".implode(",", $journalidlist).")";
    }
    if (isset($owneruid) && $owneruid > 0) {
        $whereclause[] = "a.owneruid=".$owneruid;
    }
    
    if(count($whereclause) > 0) $sql .= " AND ".implode(" AND ", $whereclause);

    $sql .= " ORDER BY a.transdate DESC, a.amount, a.journalid, a.title";
    
    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1);
    
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR'. $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR: ',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $items = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($journalid,
            $account_title,
            $journaltype,
            $agentuid) = $result->fields;
        if (xarSecurityCheck('ReadLedger', 0, 'Item', "All:All:$journalid")) {
            
            $items[$journalid] = array(
                    'journalid'         => $journalid,
                    'account_title'     => $account_title,
                    'journaltype'       => $journaltype,
                    'agentuid'          => $agentuid);
        }
    }

    $result->Close();

    return $items;
}

?>