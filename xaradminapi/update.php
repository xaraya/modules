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
 * Update a shout
 *
 * @return array
 */
function shouter_adminapi_update($args)
{

    extract($args);

//    $invalid = array();
//    if (!isset($shoutid) || !is_numeric($shoutid)) {
//        $invalid[] = 'item ID';
//    }
//    if (!isset($name) || !is_string($name)) {
//        $invalid[] = 'name';
//    }
//    if (count($invalid) > 0) {
//        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
//            join(', ', $invalid), 'admin', 'update', 'Shouter');
//        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
//            new SystemException($msg));
//        return;
//    }

    $item = xarModAPIFunc('shouter', 'user', 'get',
                    array('shoutid' => $shoutid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // Security check
    if (!xarSecurityCheck('EditShouter', 1, 'Item', "$item[name]:All:$shoutid")) {
        return;
    }
    if (!xarSecurityCheck('EditShouter', 1, 'Item', "$name:All:$shoutid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $shoutertable = $xartable['shouter'];

    $query = "UPDATE $shoutertable
            SET name =?, shout = ?
            WHERE shout_id = ?";
    $bindvars = array((string)$name, (string)$shout, (int)$shoutid);
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    $item['module'] = 'shouter';
    $item['itemid'] = $shoutid;
    $item['name'] = $name;
    $item['shout'] = $shout;
    xarModCallHooks('item', 'update', $shoutid, $item);

    return true;
}
?>
