<?php

/**
 * count number of items per category, or number of categories for each item
 * @param $args['groupby'] group entries by 'category' or by 'item'
 * @param $args['modid'] module´s ID
 * @param $args['itemtype'] item type
 * @param $args['iids'] optional array of item IDs that we are selecting on
 * @param $args['cids'] optional array of cids we're counting for (OR/AND)
 * @param $args['andcids'] true means AND-ing categories listed in cids
 * @param $args['groupcids'] the number of categories you want items grouped by
 * @returns array
 * @return number of items per category, or caterogies per item
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
    if(!xarSecurityCheck('ViewCategoryLink')) return;

    // Get database setup
    list($dbconn) = xarDBGetConn();

    // Get the field names and LEFT JOIN ... ON ... parts from categories
    // By passing on the $args, we can let leftjoin() create the WHERE for
    // the categories-specific columns too now
    $categoriesdef = xarModAPIFunc('categories','user','leftjoin',$args);

    if ($groupby == 'item') {
        $field = $categoriesdef['iid'];
    } else {
        $field = $categoriesdef['cid'];
    }
    $sql = 'SELECT ' . $field . ', COUNT(*)';
    $sql .= ' FROM ' . $categoriesdef['table'];
    $sql .= $categoriesdef['more'];
    if (!empty($categoriesdef['where'])) {
        $sql .= ' WHERE ' . $categoriesdef['where'];
    }
    $sql .= ' GROUP BY ' . $field;

    $result = $dbconn->Execute($sql);
    if (!$result) return;

    $count = array();
    while (!$result->EOF) {
        $fields = $result->fields;
        $num = array_pop($fields);
// TODO: use multi-level array for multi-category grouping ?
        $id = join('+',$fields);
        $count[$id] = (int)$num;
        $result->MoveNext();
    }

    $result->Close();

    return $count;
}

?>
