<?php
/**
 * Access Methods Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Access Methods Module
 * @link http://xaraya.com/index.php/release/333.html
 * @author St.Ego <webmaster@ivory-tower.net>
 */
function accessmethods_adminapi_delete($args)
{
    extract($args);

    if (!isset($siteid) || !is_numeric($siteid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'Access Method ID', 'admin', 'delete', 'accessmethods');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // does it exist ?
    $item = xarModAPIFunc('accessmethods',
                            'user',
                            'get',
                            array('siteid' => $siteid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('DeleteAccessMethods', 1, 'All', "$item[webmasterid]")) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'accessmethods', xarVarPrepForStore($projectid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $accessmethodstable = $xartable['accessmethods'];

    // does it have children ?
    $sql = "DELETE FROM $accessmethodstable
            WHERE siteid = " . $siteid;
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR: '. $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $item['module'] = 'accessmethods';
    $item['itemid'] = $siteid;
    xarModCallHooks('item', 'delete', $siteid, $item);

    // Let the calling process know that we have finished successfully
    return true;
}

?>
