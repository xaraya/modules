<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage labAccounting Module
 * @link http://xaraya.com/index.php/release/706.html
 * @author St.Ego
 */
function labaccounting_journalsapi_delete($args)
{
    extract($args);

    // if we're coming via a hook call
    if (isset($objectid)) {
    // TODO: cfr. hitcount delete stuff, once we enable item delete hooks
        // Return the extra info
        if (!isset($extrainfo)) {
            $extrainfo = array();
        }
        return $extrainfo;
    }
    
    if (!isset($journalid) || !is_numeric($journalid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item ID', 'journals', 'delete', 'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // does it exist ?
    $item = xarModAPIFunc('labaccounting',
                            'journals',
                            'get',
                            array('journalid' => $journalid));

    if (!isset($project) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('DeleteLedger', 1, 'Item', "$item[account_title]:All:$journalid")) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'labaccounting', xarVarPrepForStore($journalid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $journals_table = $xartable['labaccounting_journals'];

    // does it have children ?
    $sql = "DELETE FROM $journals_table
            WHERE journalid = ?";
    $bindvars = array($journalid);
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
