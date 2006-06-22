<?php
/**
 * Get an item
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
 * Get an item
 *
 * @access public
 *
 * @param int $args[itemid]
 * @param
 */
function window_userapi_get($args)
{

    extract($args);

    $bindvars = array();
    if (!empty($itemid)) {
        $where = "WHERE xar_id = ?";
        $bindvars[] = $itemid;
    } else {
        $wherelist = array();
        $fieldlist = array('name','alias');
        foreach ($fieldlist as $field) {
            if (isset($$field)) {
                $wherelist[] = "xar_$field = ?";
                $bindvars[] = $$field;
            }
        }
        if (count($wherelist) > 0) {
            $where = "WHERE " . join(' AND ',$wherelist);
        } else {
            $where = '';
        }
    }
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
              $where";
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }

    list($itemid, $name, $alias, $reg_user_only, $open_direct, $use_fixed_title, $auto_resize, $vsize, $hsize) = $result->fields;

    $result->Close();

    if (!xarSecurityCheck('ReadWindow', 1, 'Item', "$name:All:$itemid")) {
        return;
    }

    $item = array('itemid'          => $itemid,
                  'name'            => $name,
                  'alias'           => $alias,
                  'reg_user_only'   => $reg_user_only,
                  'open_direct'     => $open_direct,
                  'use_fixed_title' => $use_fixed_title,
                  'auto_resize'     => $auto_resize,
                  'vsize'           => $vsize,
                  'hsize'           => $hsize);

    return $item;
}
?>