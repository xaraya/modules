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
 * Create a new shout
 *
 * @return int
 */
function shouter_adminapi_create($args)
{
    extract($args);

    // Argument check
    $invalid = array();
//    if (!isset($name) || !is_string($name)) {
//        $invalid[] = 'name';
//    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create', 'Shouter');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }


    // Security check
// NOTE: UNCOMMENT THE 3 FOLLOWING LINES BEFORE RELEASING !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//    if (!xarSecurityCheck('AddShouter', 1, 'Item', "$name:All:All")) {
//        return;
//    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $shoutertable = $xartable['shouter'];

    // Get next ID in table
    $nextId = $dbconn->GenId($shoutertable);

    $query = "INSERT INTO $shoutertable (
              shout_id,
              name,
              date,
              shout)
            VALUES (?, ?, ?, ?)";

    $bindvars = array((int)$nextId, (string)$name, (int)$date, (string)$shout);

    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;
    $shoutid = $dbconn->PO_Insert_ID($shoutertable, 'shout_id');

    $item = $args;
    $item['module'] = 'shouter';
    $item['itemid'] = $shoutid;
    $item['name'] = $name;
    $item['date'] = $date;
    $item['shout'] = $shout;
    xarModCallHooks('item', 'create', $shoutid, $item);

    return $shoutid;
}
?>
