<?php
/**
 * XProject Module - A simple project management module
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
function labaccounting_journaltransactionsapi_delete($args)
{
    extract($args);
    
    if (!isset($transactionid) || !is_numeric($transactionid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item ID', 'journaltransactions', 'delete', 'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // does it exist ?
    $item = xarModAPIFunc('labaccounting',
                            'journaltransactions',
                            'get',
                            array('transactionid' => $transactionid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('DeleteLedger', 1, 'Item', "$item[journalid]")) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'labaccounting', xarVarPrepForStore($journalid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $journaltransactions_table = $xartable['labaccounting_journaltransactions'];

    // does it have children ?
    $sql = "DELETE FROM $journaltransactions_table
            WHERE transactionid = ?";
    $bindvars = array($transactionid);
    $result = $dbconn->Execute($sql, $bindvars);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    return true;
}

?>
