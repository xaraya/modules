<?php
/**
 * File: $Id:
 * 
 * Get all shops items
 * File: $Id:
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */

/**
 * get all shops
 *
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function xarcpshop_userapi_getall($args)
{
    extract($args);

    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'getall', 'xarCPShop');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecurityCheck('ViewxarCPShop')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table definitions you are
    // using - $table doesn't cut it in more complex modules
    $cpstorestable = $xartable['cpstores'];
    $query = "SELECT xar_storeid,
                   xar_name,
                   xar_nickname,
                   xar_toplevel
            FROM $cpstorestable
            ORDER BY xar_storeid";

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);

    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($storeid, $name, $nickname, $toplevel) = $result->fields;
        if (xarSecurityCheck('ViewxarCPShop', 0, 'Item', "$name:All:$storeid")) {
            $items[] = array('storeid' => $storeid,
                'storeid' => $storeid,
                'name' => $name,
                'nickname' => $nickname,
                'toplevel' => $toplevel);
        }
    }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close(); 
    // Return the items
    return $items;
} 

?>
