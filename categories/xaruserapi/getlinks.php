<?php

/**
 * get links
 * @param $args['cids'] array of ids of categories to get linkage for (OR/AND)
 * @param $args['iids'] array of ids of itens to get linkage for
 * @param $args['modid'] module´s ID
 * @param $args['itemtype'] item type (if any)
 * @param $args['reverse'] if set to 1 the return will have as keys the 'iids'
 *                         else the keys are the 'cids'
 * @param $args['andcids'] true means AND-ing categories listed in cids
 * @param $args['groupcids'] the number of categories you want items grouped by
 * @returns array
 * @return item array, or false on failure
 */
function categories_userapi_getlinks($args)
{
    // Get arguments from argument array
    extract($args);

    if (empty($reverse)) {
        $reverse = 0;
    }

    // Get database setup
    list($dbconn) = xarDBGetConn();

    // Get the field names and LEFT JOIN ... ON ... parts from categories
    // By passing on the $args, we can let leftjoin() create the WHERE for
    // the categories-specific columns too now
    $categoriesdef = xarModAPIFunc('categories','user','leftjoin',$args);

    // Get item IDs
    $sql = 'SELECT ' . $categoriesdef['cid'] . ', ' . $categoriesdef['iid'];
    $sql .= ' FROM ' . $categoriesdef['table'];
    $sql .= $categoriesdef['more'];
    if (!empty($categoriesdef['where'])) {
        $sql .= ' WHERE ' . $categoriesdef['where'];
    }

    $result = $dbconn->Execute($sql);
    if (!$result) return;

    // Makes the linkages array to be returned
    $answer = array();

    for(; !$result->EOF; $result->MoveNext())
    {
        $fields = $result->fields;
        $iid = array_pop($fields);
        if ($reverse == 1) {
            // the list of categories is in the N first fields here
            if (isset($cids) && count($cids) > 1 && $andcids) {
                $answer[$iid] = $fields;
            } elseif (isset($groupcids) && $groupcids > 1) {
                $answer[$iid] = $fields;
            // we get 1 category per record here
            } else {
                $answer[$iid][] = $fields[0];
            }
        } else {
// TODO: use multi-level array for multi-category grouping ?
            $cid = join('+',$fields);
            $answer[$cid][] = $iid;
        }
    }

    $result->Close();

    // Return Array with linkage
    return $answer;
}

?>
