<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.zwiggybo.com
 *
 * @subpackage shouter
 * @link http://xaraya.com/index.php/release/236.html
 * @author Neil Whittaker
 */

/**
 * Delete a shout
 *
 * @return bool
 */
function shouter_adminapi_delete($args)
{
    extract($args);

    if (!isset($shoutid) || !is_numeric($shoutid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'admin', 'delete', 'shouter');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('shouter', 'user', 'get',
                    array('shoutid' => $shoutid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('DeleteShouter', 1, 'Item', "$item[name]:All:$shoutid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $shoutertable = $xartable['shouter'];

    $query = "DELETE FROM $shoutertable WHERE shout_id = ?";

    $result = &$dbconn->Execute($query,array($shoutid));
    if (!$result) return;

    $item['module'] = 'shouter';
    $item['itemid'] = $shoutid;
    xarModCallHooks('item', 'delete', $shoutid, $item);

    return true;
}
?>
