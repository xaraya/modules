<?php
/**
 * Get all items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage window
 * @link http://xaraya.com/index.php/release/3002.html
 * @author window
 */

/**
 * Get all items
 *
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function window_userapi_getall($args)
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
            join(', ', $invalid), 'user', 'getall', 'Window');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();

    if (!xarSecurityCheck('ViewWindow')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $windowtable = $xartable['window'];

    $query = "SELECT xar_id,
                     xar_name,
                     xar_alias,
                     xar_reg_user_only,
                     xar_open_direct,
                     xar_use_fixed_title,
                     xar_auto_resize,
                     xar_vsize,
                     xar_hsize
              FROM $windowtable
              ORDER BY xar_name";

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1, array());
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($id, $name, $alias, $reg_user_only, $open_direct, $use_fixed_title, $auto_resize, $vsize, $hsize) = $result->fields;
        if (xarSecurityCheck('ViewWindow', 0, 'Item', "$name:All:$id")) {
            $items[] = array('id'              => $id,
                             'name'            => $name,
                             'alias'           => $alias,
                             'reg_user_only'   => $reg_user_only,
                             'open_direct'     => $open_direct,
                             'use_fixed_title' => $use_fixed_title,
                             'auto_resize'     => $auto_resize,
                             'vsize'           => $vsize,
                             'hsize'           => $hsize);
        }
    }

    $result->Close();
 
    return $items;
}
?>