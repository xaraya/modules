<?php
/**
 * Get points
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Userpoints Module
 * @link http://xaraya.com/index.php/release/782.html
 * @author Userpoints Module Development Team
 */
function userpoints_userapi_getpoints($args)
{
    extract($args);

    // map some old stuff to standard types
    switch ($paction) {
        case 'C':
        case 'create':
            $type = 'create';
            break;

        case 'U':
        case 'update':
            $type = 'update';
            break;

        case 'R':
        case 'remove':
        case 'delete':
            $type = 'delete';
            break;

        case 'F':
        case 'frontpage':
            $type = 'frontpage';
            break;

        case 'D':
        case 'display':
        default:
            $type = 'display';

    }

    // try different module variables depending on config and hooks
    $modname = $pmodule;
    if (!empty($itemtype)) {
        $points = xarModGetVar('userpoints', $type."points.$modname.$itemtype");
        if (!isset($points)) {
            $points = xarModGetVar('userpoints', $type.'points.'.$modname);
        }
    } else {
        $points = xarModGetVar('userpoints', $type.'points.'.$modname);
    }
    if (!isset($points)) {
        $points = xarModGetVar('userpoints', 'default'.$type);
    }

    return $points;

/*
// Get database setup

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pointstypestable = $xartable['pointstypes'];

    // Get item
    $query = "SELECT xar_uptid, xar_tpoints
            FROM $pointstypestable
            WHERE xar_module = '$pmodule'
            AND (xar_itemtype = $itemtype OR xar_itemtype = 0)
            AND xar_action = '$paction'";
     $result =& $dbconn->Execute($query);
    if (!$result) return;
    if ($result->EOF) {
        return false; //no points
    }
    while (!$result->EOF) {
        list($uptid, $tpoints) = $result->fields;
        $result->MoveNext();
        $data['uptid'] = $uptid;
        $data['tpoints'] = $tpoints;
    }

    return $data;
*/
}
?>
