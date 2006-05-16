<?php
/**
 * Delete an item
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage window
 * @link http://xaraya.com/index.php/release/3002.html
 * @author Johnny Robeson
 */
/**
 * Delete an item
 *
 * @param int $args[itemid] ID of the item
 * @return bool true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function window_adminapi_delete($args)
{
    extract($args);

    if (!isset($itemid) || !is_numeric($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'admin', 'delete', 'Window');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('window', 'user', 'get',
                    array('itemid' => $itemid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;


    if (!xarSecurityCheck('DeleteWindow', 1, 'Item', "$item[name]:All:$itemid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $windowtable = $xartable['window'];

    $query = "DELETE FROM $windowtable WHERE xar_id = ?";


    $result = &$dbconn->Execute($query,array($itemid));


    if (!$result) return;

    /*$item['module'] = 'window';
    $item['itemid'] = $itemid;
    xarModCallHooks('item', 'delete', $itemid, $item);
    */
    return true;
}
?>