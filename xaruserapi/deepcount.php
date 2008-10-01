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
 * @param $args['itemtype'] item type
 * @param $args['cids'] optional array of cids we're counting for (OR/AND)
 * @param $args['andcids'] true means AND-ing categories listed in cids
 * @param $args['groupcids'] the number of categories you want items grouped by
 * @return array number of items per category, or categories per item
 */
function categories_userapi_deepcount($args)
{
    extract($args);

    $count = array();

    // Get the non-zero counts.
    // These are the leaf nodes that we then extend back to the top ancestor(s).
    $catcount = xarModAPIfunc(
        'categories', 'user', 'groupcount', $args
    );

    // Throw back errors as an empty list.
    if (empty($catcount)) {return $count;}

    // Throw away all the muliple-category counts, just leaving the simple categories.
    // e.g. we want to count '123' and '456' separately, and not count the combined '123+456'.
    foreach($catcount as $catcount_key => $catcount_value) {
        if (!is_numeric($catcount_key)) unset($catcount[$catcount_key]);
    }

    $allcounts = $catcount;

    // Array of category IDs.
    $catlist = array_keys($catcount);

    // Get the ancestors (including self).
    $ancestors = xarModAPIfunc('categories', 'user', 'getancestors', array('cids'=>$catlist, 'self'=>true));

    // For each non-zero category count, traverse the ancestors and add on the counts.
    $allcounts[0] = 0;
    foreach ($catcount as $cat => $count) {
        $cat = (int)$cat;
        // Keep track of categories visited to avoid infinite loops.
        $done = array();
        $nextcat = $ancestors[$cat]['parent'];
        while ($nextcat > 0 && !isset($done[$nextcat])) {
            $done[$nextcat] = $nextcat;
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
