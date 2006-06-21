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
 * @author Johnny Robeson
 */

/**
 * Get all items
 *
 * @param $args[numitems] the number of items to retrieve (default -1 = all)
 * @param $args[startnum] start with this item number (default 1)
 * @return array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function window_userapi_getall($args)
{
    extract($args);

    $bindvars = array();
	$wherelist = array();
	$fieldlist = array('name','alias','status');
	foreach ($fieldlist as $field) {
		if (isset($$field)) {
			$wherelist[] = "xar_$field = ?";
			$bindvars[] = $$field;
		}
	}
	if (count($wherelist) > 0) {
		$where = " WHERE " . join(' AND ',$wherelist) . " ";
	} else {
		$where = '';
	}

    if (!isset($startnum) || !is_numeric($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $numitems = -1;
    }

    $items = array();

    if (!xarSecurityCheck('ViewWindow')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $windowtable = $xartable['window'];

    $query = "SELECT xar_id,
                     xar_name,
                     xar_alias,
                     xar_label,
                     xar_description,
                     xar_reg_user_only,
                     xar_open_direct,
                     xar_use_fixed_title,
                     xar_auto_resize,
                     xar_vsize,
                     xar_hsize
              FROM $windowtable $where
              ORDER BY xar_name";

//    echo $query;exit;
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($itemid, $name, $alias, $label, $description, $reg_user_only, $open_direct, $use_fixed_title, $auto_resize, $vsize, $hsize) = $result->fields;
        if (xarSecurityCheck('ViewWindow', 0, 'Item', "$name:All:$itemid")) {
            $items[] = array('itemid'          => $itemid,
                             'name'            => $name,
                             'alias'           => $alias,
                             'label'           => $label,
                             'description'     => $description,
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