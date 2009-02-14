<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function accessmethods_logapi_deleteall($args)
{
    extract($args);

    if (!isset($siteid) || !is_numeric($siteid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'siteid', 'log', 'deleteall', 'accessmethods');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $logtable = $xartable['accessmethods_log'];
    
    $sql = "DELETE FROM $logtable
            WHERE siteid = " . $siteid;
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // Let the calling process know that we have finished successfully
    return true;
}

?>
