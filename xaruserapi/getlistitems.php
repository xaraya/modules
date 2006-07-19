<?php
/**
 * Get list items
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
 * Get lists of values for a list.
 *
 * Use this function to open a list in another module
 *
 * @author Lists Module Development Team
 * @access public
 * @param $args['lid'] list ID (optional)
 * @param $args['list_name'] list name (optional)
 * @param $args['tid'] list type ID (optional)
 * @param $args['type_name'] list type name (optional)
 * @param $args['itemkey'] key for item list array [id]|code (optional)
 * @param $args['order_columns'] over-ride list and list type default ordering (optional)
 * @param $args['items_only'] return just the items, without the list details (default: false)
 * @param $args['lang_suffix'] if set, then any field ending in this value will be copied to same field without suffix (optional)
 * @return array of list items, or false on failure
 */
function lists_userapi_getlistitems($args)
{
    // Expand arguments.
    extract($args);

    // Security Check
    //if(!xarSecurityCheck('Readlists')) {return;}

    // Optional arguments
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = (-1);
    }

    if (empty($itemkey)) {
        $itemkey = 'id';
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table_lists = $xartable['lists_types'];
    $table_items = $xartable['lists_items'];

    // We need a list ID, so if one is not provided, get it from the list item.
    if (empty($lid) && !empty($iid)) {
        $query = 'SELECT xar_lid FROM '.$table_items.' WHERE xar_iid = ?';
        $result =& $dbconn->Execute($query, array((int)$iid));
        if ($result && !$result->EOF) {
            list($lid) = $result->fields;
            $args['lid'] = (int)$lid;
        } else {
            // The lid does not exist.
            // TODO: raise an error.
        }
    }

    // Set up check for language suffixes
    if (!empty($lang_suffix)) {
        $lang_length = (-1) * strlen($lang_suffix);
    } else {
        $lang_length = 0;
    }

    // Get the list(s).
    $lists = xarModAPIfunc('lists', 'user', 'getlists', $args);

    // Loop for each list.
    // Don't make any assumptions regarding what the key is.
    $list_ids = array();
    foreach($lists as $key => $list) {
        // Initialise.
        $items = array();
        $bind = array();
        $bind[] = (int)$list['lid'];
        if (!empty($iid)) {
            $bind[] = (int)$iid;
        }

        // Get links.
        // Use a left join to return links without a valid type (we
        // don't want to lose them).
        $query = '
            SELECT  xar_iid,
                    xar_lid,
                    xar_code,
                    xar_short_name,
                    xar_long_name,
                    xar_desc,
                    xar_order
            FROM ' . $table_items . '
            WHERE   xar_lid = ? ' . (!empty($iid) ? 'AND xar_iid = ? ' : '');
        //echo $query; var_dump($bind); die;

        $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1, $bind);
        if (!$result) {return;}

        for (; !$result->EOF; $result->MoveNext()) {
            list(
                $iid, $lid, $item_code,
                $item_short_name, $item_long_name, $item_desc, $item_order
            ) = $result->fields;

            $item = array(
                'iid' => (int)$iid,
                'lid' => (int)$lid,
                'item_code' => $item_code,
                'item_short_name' => $item_short_name,
                'item_long_name' => $item_long_name,
                'item_desc' => $item_desc,
                'item_order' => $item_order
            );

            // Now fetch and merge in any DD column values.
            /* NOW DONE IN GROUPS LOWER DOWN
            if (xarModIsHooked('dynamicdata', 'lists', $list['tid'])) {
                // Get the DD fields.
                // The list type is the itemtype, and the list item ID is itemid,
                // so all list items under a list type share the same DD fields.
                $dd_data = xarModAPIfunc(
                    'dynamicdata', 'user', 'getitem',
                    array('module'=>'lists', 'itemtype'=>$list['tid'], 'itemid'=>$iid)
                );
                if (is_array($dd_data)) {
                    foreach ($dd_data as $dd_name => $dd_value) {
                        if (!isset($item[$dd_name])) {
                            $item[$dd_name] = $dd_value;
                        }
                        // If the language suffix is set, then do some copying
                        // if the DD field uses the language suffix.
                        if ($lang_length <> 0 && $dd_value != '' && substr($dd_name, $lang_length) == $lang_suffix) {
                            $item[substr($dd_name, 0, $lang_length)] = &$item[$dd_name];
                        }
                    }
                }
            }
            */

            $index = (($itemkey == 'code') ? $item_code : (int)$iid);
            $items[$index] =& $item;
            unset($item);

            // Save the IDs for DD lookups later.
            $list_ids[$list['tid']][(int)$iid] = array('listkey'=>$key, 'itemkey'=>$index);
        }

        $lists[$key]['items'] =& $items;
        unset($items);

        $result->Close();
    }

    //var_dump($list_ids);
    // Get DD values (in groups).
    if (!empty($list_ids)) {
        foreach ($list_ids as $itemtype => $item_ids) {
            if (xarModIsHooked('dynamicdata', 'lists', $itemtype)) {
                $dd_data = xarModAPIfunc(
                    'dynamicdata', 'user', 'getitems',
                    array('module' => 'lists', 'itemtype' => $itemtype, 'itemids' => array_keys($item_ids))
                );
                if (is_array($dd_data)) {
                    foreach ($item_ids as $itemid => $keys) {
                        if (isset($dd_data[$itemid])) {
                            // Now this gets hairy. I hope it never goes wrong.
                            foreach ($dd_data[$itemid] as $dd_name => $dd_value) {
                                if (!isset($lists[$keys['listkey']]['items'][$keys['itemkey']][$dd_name])) {
                                    $lists[$keys['listkey']]['items'][$keys['itemkey']][$dd_name] = $dd_value;
                                }
                                // If the language suffix is set, then do some copying
                                // if the DD field uses the language suffix.
                                if ($lang_length <> 0 && $dd_value != '' && substr($dd_name, $lang_length) == $lang_suffix) {
                                    $lists[$keys['listkey']]['items'][$keys['itemkey']][substr($dd_name, 0, $lang_length)] = &$lists[$keys['listkey']]['items'][$keys['itemkey']][$dd_name];
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    // Sort the lists
    foreach($lists as $key => $list) {
        if (!empty($list['order_columns'])) {
            // Sort the array.
            // We are sorting the array so that DD fields can be included too.
            // Create a temporary function so that we can inject the column order string.
            $sortfunc = create_function(
                '$a,$b',
                'return _lists_userapi_getitems_uasort($a,$b,"' . (!empty($order_columns) ? $order_columns : $list['order_columns']) . '");'
            );

            uasort($lists[$key]['items'], $sortfunc);
        }
    }


    if (!empty($items_only)) {
        // Note: only returns the *last* list of items, or the *only* list
        // if there is just one list. It does not make sense to mix items
        // from different lists into one array, without separating them,
        // so we don't allow it.
        // FIXME: move this reassignment further up, then distinguish between 'getlistitems' and 'getlistsitems'.
        $first_list = reset($lists);
        return empty($first_list['items']) ? array() : $first_list['items'];
    } else {
        return $lists;
    }
}
/**
 * This function will sort an array by any columns, named in a CSV list, with +/-
 * indicating whether sorting should be ascending or descending.
 */
function _lists_userapi_getitems_uasort($a, $b, $c) {
    // Sorting is case-insensitive.
    // Loop for each field to compare.
    foreach(explode(',', $c) as $field) {
        // Determine direction of sorting.
        $dir = 1;
        // Direction can be specified by prefixing the field name with a '+' or '-'.
        if ($field{0} == '+') {$field = substr($field, 1);}
        if ($field{0} == '-') {$field = substr($field, 1); $dir = -1;}
        // Support nat-sorting by prefixing the field name with a '*'.
        if ($field{0} == '*') {$field = substr($field, 1); $nat = true;}
        // Break if the field does not exist.
        if (!isset($a[$field]) || !isset($b[$field])) {break;}
        // If identical, move on to the next field.
        if (($casecmp = strcasecmp($a[$field], $b[$field])) == 0) {continue;}
        // Not identical - one must be greater than the other.
        if (empty($nat)) {
            return $casecmp * ($dir);
        } else {
            return strnatcasecmp($a[$field], $b[$field]) * ($dir);
        }
    }
    // Items are equally positioned using the sorting rules.
    return 0;
}

?>