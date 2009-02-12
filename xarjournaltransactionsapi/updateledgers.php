<?php
/**
 * XTasks Module - A task management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xTasks Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
 
function labaccounting_journaltransactionsapi_updateledgers($args)
{
    extract($args);

    $invalid = array();
    if (!isset($journaltransid) || !is_numeric($journaltransid)) {
        $invalid[] = 'Transaction ID';
    }
    if (!isset($journalid) || !is_numeric($journalid)) {
        $invalid[] = 'Journal ID';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'journaltransactions', 'updateledgers', 'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    if (!isset($creatorid) || !is_string($creatorid)) {
        $creatorid = xarSessionGetVar('uid');
    }

    $item = xarModAPIFunc('labaccounting',
                            'journaltransactions',
                            'get',
                            array('transactionid' => $journaltransid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditJournal', 1, 'Item', $item['journalid'])) {
        return;
    }
    
    $activeledgerlist = xarModAPIFunc('labaccounting', 'journals', 'getall_ledgers', array('journalid' => $journalid));
                                        
    if($activeledgerlist == false) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $ledgertransactions_table = $xartable['labaccounting_ledgertransactions'];

    foreach($ledgeramounts as $ledgerid => $amount) {
    
        $query = "UPDATE $ledgertransactions_table
                    SET amount = ?
                    WHERE journaltransid = ?
                    AND ledgerid = ?";
    
        $bindvars = array(
                         $amount,
                         $journaltransid,
                         $ledgerid);
                  
        $result = &$dbconn->Execute($query,$bindvars);
    
        if (!$result) return;
    
            $transactioninfo = xarModAPIFunc('labaccounting', 'journaltransactions', 'get', array('transactionid' => $journaltransid));
        
            if($transactioninfo == false) return;
            
            $nextId = $dbconn->GenId($ledgertransactions_table);
        
            $query = "INSERT INTO $ledgertransactions_table (
                          transactionid,
                           ledgerid,
                           journaltransid,
                           creatorid,
                           title,
                           details,
                           transnum,
                           amount,
                           status,
                           transdate)
                        VALUES (?,?,?, ?,?, ?,?,?, ?,?)";
                    
            $bindvars = array(
                            $nextId,
                            $ledgerid,
                            $journaltransid,
                            $creatorid,
                            $transactioninfo['title'],
                            $transactioninfo['details'],
                            $transactioninfo['transnum'],
                            $amount,
                            "Submitted",
                            $transactioninfo['transdate']);
            $result = &$dbconn->Execute($query,$bindvars);
            if (!$result) return;
        
            $transactionid = $dbconn->PO_Insert_ID($ledgertransactions_table, 'transactionid');
    
    
    
    
    }
    
    return true;
}
?>