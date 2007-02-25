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
 * Get all shouts
 *
 * @param int $startnum
 * @param int $numitems
 * @return array
 */
function shouter_userapi_getall($args)
{

    extract($args);

    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $invalid = array();
    if (!is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'getall', 'shouter');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();

    if (!xarSecurityCheck('ViewShouter')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $shoutertable = $xartable['shouter'];

    $query = "SELECT shout_id,
                     name,
                     date,
                     shout
              FROM $shoutertable
              ORDER BY shout_id DESC";

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    // Put items into result array
    for (; !$result->EOF; $result->MoveNext()) {
         list($shoutid, $name, $date, $shout) = $result->fields;
        if (xarSecurityCheck('ViewShouter', 0, 'Item', "$name:All:$shoutid")) {
            $items[] = array('shoutid' => $shoutid,
                             'name'    => $name,
                             'date'    => $date,
                             'shout'    => $shout);
        }
    }

    $result->Close();

    return $items;
}
?>
