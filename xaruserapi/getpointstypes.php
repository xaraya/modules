<?php
/**
 * Get the types of points
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

function userpoints_userapi_getpointstypes()
{
    //get a list of points types.
    static $pointstypes = array();
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pointstypestable = $xartable['pointstypes'];

    // Get item
    $query = "SELECT xar_uptid,
                   xar_module,
                   xar_itemtype,
                   xar_action,
                   xar_tpoints
            FROM $pointstypestable
            ORDER BY xar_module, xar_itemtype, xar_action ASC";
     $result =& $dbconn->Execute($query);
    if (!$result) return;
    if ($result->EOF) {
        return $pointstypes;
    }
    while (!$result->EOF) {
        list($uptid, $module, $itemtype, $action, $tpoints) = $result->fields;

        $pointstypes[$uptid] = array('uptid' => $uptid,
                                    'module' => $module,
                                    'itemtype' => $itemtype,
                                    'action' => $action,
                                    'tpoints' => $tpoints);
        $result->MoveNext();
    }

    return $pointstypes;
}
?>