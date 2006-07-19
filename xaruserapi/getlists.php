<?php
/**
 * Get the lists
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Lists Module
 * @link http://xaraya.com/index.php/release/46.html
 * @author Jason Judge
 */
/**
 * Get all lists.
 *
 * @returns array
 * @param $args['lid'] list ID (optional)
 * @param $args['list_name'] list name (optional)
 * @param $args['tid'] list type ID (optional)
 * @param $args['type_name'] list type name (optional)
 * @param $args['listkey'] key for list array [id]|name|index (optional)
 * @param $args['column'] return a single column as the value (optional)
 * @return array of links, or false on failure (?)
 */
function lists_userapi_getlists($args)
{
    // Expand arguments.
    extract($args);

    //echo " GET LISTS: "; var_dump($args);

    // Optional arguments
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = (-1);
    }
    if (empty($listkey)) {
        $listkey = 'id';
    }
    //if (!isset($dd_flag)) {
    //    $dd_flag = true;
    //}

    // Security Check
    //if(!xarSecurityCheck('Readlists')) {return;}

    // Set up check for language suffixes
    if (!empty($lang_suffix)) {
        $lang_length = (-1) * strlen($lang_suffix);
    } else {
        $lang_length = 0;
    }

    // Database stuff.
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table_lists = $xartable['lists_types'];
    //$table_items = $xartable['lists_items'];

    // Extra where-clause conditions.
    $where = array();
    $bind = array();

    if (isset($type_name))
    {
        $where[] = '(alias_list_types.xar_name = ?)';
        $bind[] = $type_name;
    }

    if (isset($tid))
    {
        $where[] = '(alias_list_types.xar_tid = ?)';
        $bind[] = (int)$tid;
    }

    if (isset($list_name))
    {
        $where[] = '(alias_lists.xar_name = ?)';
        $bind[] = $list_name;
    }

    if (isset($lid))
    {
        $where[] = '(alias_lists.xar_tid = ?)';
        $bind[] = (int)$lid;
    }

    $where = implode(' AND ', $where);

    // Initialise.
    $lists = array();

    // Get list types.
    $query = '
        SELECT  alias_list_types.xar_tid as xar_tid,
                alias_list_types.xar_name as xar_type_name,
                alias_list_types.xar_desc xar_type_desc,
                alias_list_types.xar_order_columns,
                alias_list_types.xar_list_type_id,
                alias_lists.xar_tid as xar_lid,
                alias_lists.xar_name as xar_list_name,
                alias_lists.xar_desc as xar_list_desc,
                alias_lists.xar_order_columns
        FROM    ' . $table_lists . ' as alias_lists
        RIGHT JOIN ' . $table_lists . ' as alias_list_types
        ON      alias_list_types.xar_tid = alias_lists.xar_list_type_id
        WHERE   alias_list_types.xar_type = \'T\'
        AND     alias_lists.xar_type = \'L\' '
        . (!empty($where) ? ' AND ' . $where : '');

    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1, $bind);
    if (!$result) {return;}

    $item_ids = array();
    for (; !$result->EOF; $result->MoveNext()) {
        list(
            $tid, $type_name, $type_desc, $type_order_columns, $type_group_id,
            $lid, $list_name, $list_desc, $list_order_columns
        ) = $result->fields;

        //if(xarSecurityCheck('ReadLists', 0, 'All', $name.':'.$tid)) {
            $list = array(
                'tid' => (int)$tid,
                'type_name' => $type_name,
                'type_desc' => $type_desc,
                'type_order_columns' => $type_order_columns,
                'type_group_id' => (int)$type_group_id,
                'lid' => (int)$lid,
                'list_name' => $list_name,
                'list_desc' => $list_desc,
                'list_order_columns' => $list_order_columns,
                'order_columns' => !empty($list_order_columns) ? $list_order_columns : $type_order_columns
            );

            // Now fetch and merge in any DD column values.
            /* OLD STUFF DONE ITEM AT A TIME
            if ($dd_flag && xarModIsHooked('dynamicdata', 'lists', $type_group_id)) {
                // Get the DD fields.
                // The list type 'group id' is the itemtype, and the list item ID is itemid,
                // so all list items under a list type share the same DD fields.
                $dd_data = xarModAPIfunc(
                    'dynamicdata', 'user', 'getitem',
                    array('module'=>'lists', 'itemtype'=>$type_group_id, 'itemid'=>$lid)
                );

                if (is_array($dd_data)) {
                    foreach ($dd_data as $dd_name => $dd_value) {
                        if (!isset($list[$dd_name])) {
                            $list[$dd_name] = $dd_value;
                        }
                        // If the language suffix is set, then do some copying
                        // if the DD field uses the language suffix.
                        if ($lang_length <> 0 && $dd_value != '' && substr($dd_name, $lang_length) == $lang_suffix) {
                            $list[substr($dd_name, 0, $lang_length)] = &$list[$dd_name];
                        }
                    }
                }
            }
            */

            // If just a single column is requested as a return value, then set it.
            // TODO: validation.
            if (!empty($column)) {
                if ($column == 'digest') {
                    // Special format for drop-down lists.
                    $list = $list['type_name'] . ': ' . $list['list_name']
                        . ' (' . substr($list['list_desc'],0,30)
                        . (strlen($list['list_desc'])>30 ? '...' : '') . ')';
                } else {
                    $list = $list[$column];
                }
            }

            $index = ($listkey == 'name') ? $list_name : ($listkey == 'index' ? count($lists) : (int)$lid);
            $lists[$index] =& $list;
            unset($list);

            // Keep a record of IDs for the DD lookup later.
            $item_ids[$lid] = $index;
        //}
    }
    $result->Close();

    if (!empty($item_ids) && xarModIsHooked('dynamicdata', 'lists', $type_group_id)) {
        // Get the DD fields.
        // The list type 'group id' is the itemtype, and the list item ID is itemid,
        // so all list items under a list type share the same DD fields.
        $dd_data = xarModAPIfunc(
            'dynamicdata', 'user', 'getitems',
            array('module' => 'lists', 'itemtype' => $type_group_id, 'itemids' => array_keys($item_ids))
        );
        //var_dump($dd_data); var_dump($lists);

        if (is_array($dd_data)) {
            foreach($item_ids as $lid => $index) {
                if (isset($dd_data[$lid])) {
                    foreach ($dd_data[$lid] as $dd_name => $dd_value) {
                        if (!isset($lists[$index][$dd_name])) {
                            $lists[$index][$dd_name] = $dd_value;
                        }
                        // If the language suffix is set, then do some copying
                        // if the DD field uses the language suffix.
                        if ($lang_length <> 0 && $dd_value != '' && substr($dd_name, $lang_length) == $lang_suffix) {
                            $lists[$index][substr($dd_name, 0, $lang_length)] = &$lists[$index][$dd_name];
                        }
                    }
                }
            }
        }
    }

    return $lists;
}

?>