<?php

/**
 * count number of items per category, or number of categories for each item
 * @param $args['groupby'] group entries by 'category' or by 'item'
 * @param $args['modid'] module´s ID
 * @param $args['itemtype'] item type
 * @param $args['cids'] optional array of cids we're counting for (OR/AND)
 * @param $args['andcids'] true means AND-ing categories listed in cids
 * @param $args['groupcids'] the number of categories you want items grouped by
 * @returns array
 * @return number of items per category, or caterogies per item
 */
function categories_userapi_deepcount($args)
{
    extract($args);

    $count = array();

    // Get the non-zero counts.
    $catcount = xarModAPIfunc(
        'categories', 'user', 'groupcount',
        array('modid' => $modid, 'itemtype' => $itemtype)
    );

    $allcounts = $catcount;

    // Array of category IDs.
    $catlist = array_keys($catcount);

    // Get the ancestors (including self).
    $ancestors = xarModAPIfunc('categories', 'user', 'getancestors', array('cids'=>$catlist, 'return_itself'=>true));

    // For each non-zero category count, traverse the ancestors and add in the counts.
    $allcounts[0] = 0;
    foreach ($catcount as $cat => $count) {
        $nextcat = $ancestors[$cat]['parent'];
        while ($nextcat > 0) {
            if (!isset($allcounts[$nextcat])) {
                $allcounts[$nextcat] = $count;
            } else {
                $allcounts[$nextcat] += $count;
            }
            $nextcat = $ancestors[$nextcat]['parent'];
        }
        $allcounts[0] += $count;
    }

    return $allcounts;   
}

?>
