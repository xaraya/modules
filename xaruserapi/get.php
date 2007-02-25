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
 * Get a shouts
 *
 * @param int $shoutid
 * @return array
 */
function shouter_userapi_get($args)
{
    extract($args);

    if (!isset($shoutid) || !is_numeric($shoutid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'shout ID', 'user', 'get', 'shouter');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $shoutertable = $xartable['shouter'];

    $query = "SELECT name, shout
              FROM $shoutertable
              WHERE shout_id = ?";
    $result = &$dbconn->Execute($query,array($shoutid));
    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This shout does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }

    list($name, $shout) = $result->fields;

    $result->Close();

    if (!xarSecurityCheck('ReadShouter', 1, 'Item', "$name:All:$shoutid")) {
        return;
    }

    $item = array('shoutid' => $shoutid,
                  'name'    => $name,
                  'shout'   => $shout);

    return $item;
}
?>
