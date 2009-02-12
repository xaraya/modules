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
function labaccounting_journalsapi_addledger($args)
{
    extract($args);

    $invalid = array();
    if (!isset($journalid) || !is_numeric($journalid)) {
        $invalid[] = 'journalid';
    }
    if (!isset($ledgerid) || !is_numeric($ledgerid)) {
        $invalid[] = 'ledgerid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'journals', 'addledger', 'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // does it exist ?
    $journalinfo = xarModAPIFunc('labaccounting',
                            'journals',
                            'get',
                            array('journalid' => $journalid));

    if (!isset($journalinfo) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('ManageJournal', 1, 'Item', "$journalinfo[account_title]:All:$journalid")) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'labaccounting', xarVarPrepForStore($journalid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }
    
    $ledgerinfo = xarModAPIFunc('labaccounting',
                            'ledgers',
                            'get',
                            array('ledgerid' => $ledgerid));

    if (!isset($ledgerinfo) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();
    
    $journalXledger_table = $xartable['labaccounting_journalXledger'];

    $query = "INSERT INTO $journalXledger_table (
                    journalid,
                    ledgerid,
                    normalbalance)
                VALUES (?,?,?)";
            
    $bindvars = array($journalid,
                    $ledgerid,
                    $normalbalance);
                    
    $result = &$dbconn->Execute($query,$bindvars);
    
    if (!$result) return;

    return true;
}

?>