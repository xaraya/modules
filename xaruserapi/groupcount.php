<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * count number of items per category, or number of categories for each item
 * @param $args['groupby'] group entries by 'category' or by 'item'
 * @param $args['modid'] module?s ID
 * @param $args['itemid'] optional item ID that we are selecting on
 * @param $args['itemids'] optional array of item IDs that we are selecting on
 * @param $args['itemtype'] item type
 * @param $args['cids'] optional array of cids we're counting for (OR/AND)
 * @param $args['andcids'] true means AND-ing categories listed in cids
 * @param $args['groupcids'] the number of categories you want items grouped by
 * @return array number of items per category, or categories per item
 */
function categories_userapi_groupcount($args)
{
    // Get arguments from argument array
    extract($args);

    // Optional arguments
    if (!isset($groupby)) {
        $groupby = 'category';
    }

    // Security check
    if (!xarSecurityCheck('ViewCategoryLink')) return;

    // Get database setup
    $dbconn =& xarDBGetConn();

    // Get the field names and LEFT JOIN ... ON ... parts from categories
    // By passing on the $args, we can let leftjoin() create the WHERE for
    // the categories-specific columns too now
    $categoriesdef = xarModAPIFunc('categories','user','leftjoin',$args);

    // Collection of where-clause expressions.
    $where = array();

    // Filter by itemids.
    if (!empty($itemids) && is_array($itemids)) {
        $itemids = array_filter($itemids, 'is_numeric');
        if (!empty($itemids)) {
            $where[] = $categoriesdef['iid'] . ' in (' . implode(', ', $itemids) . ')';
        }
    }

    // Filter by single itemid.
    if (!empty($itemid) && is_numeric($itemid)) {
        $where[] = $categoriesdef['iid'] . '=' . $itemid;
    }

    // Filter by category.
    if (!empty($categoriesdef['where'])) {
        $where[] = $categoriesdef['where'];
    }

    if ($groupby == 'item') {
        $field = $categoriesdef['iid'];
    } elseif ($groupby == 'itemcategory') {
        $field = $categoriesdef['iid'] . ',' . $categoriesdef['cid'];
    } else {
        $field = $categoriesdef['cid'];
    }

    $sql = 'SELECT ' . $field . ', COUNT(*)';
    $sql .= ' FROM ' . $categoriesdef['table'];
    $sql .= $categoriesdef['more'];
    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' GROUP BY ' . $field;

    $result = $dbconn->Execute($sql);
    if (!$result) return;

    $count = array();
    while (!$result->EOF) {
        $fields = $result->fields;
        $result->MoveNext();

        // The last field is the count.
        $num = array_pop($fields);

        // If grouping by item and category, then the first field will
        // be the item ID.
        if ($groupby == 'itemcategory') $itemid = array_shift($fields);

        // Use multi-level array for multi-category grouping?
        $id = join('+', $fields);

        if ($groupby == 'itemcategory') {
            // If grouping by item and category, then introduce the item id level.
            $count[$itemid][$id] = (int)$num;
        } else {
            // Grouping by just category or item id.
            $count[$id] = (int)$num;
        }
    }

    $result->Close();

    return $count;
}

?>