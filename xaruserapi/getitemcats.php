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
 * Get an array of assigned category details for a specific item, limiting by a base cid if required.
 * Get categories for an item, optionally limiting to just one category branch (to be expanded to allow base categories by name).
 *
 * @param int    $args['basecid'] optional base cid under which the returned categories must lie
 * @param array  $args['basecids'] optional array of base cids under which the returned categories must lie
 * @param string $args['module'] name of the module; or
 * @param int    $args['modid'] module ID. If not given, the calling module will be used OPTIONAL
 * @param int    $args['itemtype'] item type
 * @param int    $args['itemid'] item ID; or
 * @param array  $args['itemids'] item IDs
 * @return array Details for the categories found supplied by the function 'getcatinfo'.
 * @TODO allow ordering of the results by name, description etc.
 */
function categories_userapi_getitemcats($args)
{
    // Get arguments from argument array
    extract($args);

    // Requires: module, itemtype, itemid (but not validated)

    // Default the module name.
    if (empty($modid) && empty($module)) {
        $module = xarModGetName();
    }

    // Group by categories by default.
    // An alternative is 'itemcategory' which groups by categories within items.
    if (!isset($args['groupby'])) {
        $args['groupby'] = 'category';
        $groupby = $args['groupby'];
    }

    // Get module ID if only a name provided.
    if (empty($modid) && !empty($module)) {
        $args['modid'] = xarModGetIDfromName($module);
    }

    // Get the list of assigned categories for this module item or items
    $catlist = xarModAPIfunc(
        'categories', 'user', 'groupcount', $args
    );

    // Throw back errors if an empty list.
    if (empty($catlist)) return array();

    // Flip the array (or arrays), so the cat IDs (which are the keys) are the values.
    if ($groupby == 'itemcategory') {
        foreach($catlist as $itemlist_key => $itemlist) $catlist[$itemlist_key] = array_keys($itemlist);
    } else {
        $catlist = array_keys($catlist);
    }

    if (!isset($basecids) || !is_array($basecids)) {
        $basecids = array();
    }

    if (isset($basecid)) {
        array_push($basecids, $basecid);
    }

    // Initialise the result array.
    $result = array();

    // Check whether we want to restrict the categories by one or more base categories.
    // TODO: when categories supports 'base' categories (category itemtypes?) then add
    // another (much simpler) section here.
    if (!empty($basecids)) {
        // Get the ancestors (including self) of these categories.
        // Included, is a list of descendants for each category.
        $ancestors = xarModAPIfunc(
            'categories', 'user', 'getancestors',
            array('cids' => $catlist, 'self' => true, 'descendants'=>'list')
        );

        $resultcids = array();

        foreach($basecids as $basecid) {
            // Check each category to see if the base is an ancestor.
            // If base category is an ancestor, then we want to look at it.
            if (isset($ancestors[$basecid]['descendants'])) {
                // The cats we want will be the insersection of the catlist for the item,
                // and the descendants of this base.
                $resultcids = array_merge($resultcids, array_intersect($ancestors[$basecid]['descendants'], $catlist));
            }
        }

        // If the intersect was not empty, then add the details of those
        // categories to the result list.
        if (!empty($resultcids)) {
            foreach ($resultcids as $cid) {
                if (!isset($result[$cid])) {
                    $result[$cid] = $ancestors[$cid];
                }
            }
        }
    } else {
        // Get the details for these categories, with no restrictions.
        // This is almost a 'passthrough'.
        // TODO: include the 'basecid' stuff directly in 'getcatinfo', or
        // leave getcatinfo to handle the raw database stuff and this to do
        // the specials?

        // If we are grouping by item ID and categories, then the cat info needs to be organised
        // into a two-level structure.
        if ($groupby == 'itemcategory') {
            // Get the list of unique cids.
            $unique_cids = array();
            if (!empty($catlist)) {
                foreach($catlist as $itemlist) {
                    $unique_cids = array_merge($unique_cids, $itemlist);
                }
                $unique_cids = array_unique($unique_cids);

                // Get each category once.
                $catinfo = xarModAPIfunc('categories', 'user', 'getcatinfo', array('cids' => $unique_cids));

                // Redistribute the categories into the items.
                $result = array();
                foreach($catlist as $itemid => $itemlist) {
                    foreach($itemlist as $itemcat) {
                        if (isset($catinfo[$itemcat])) $result[$itemid][$itemcat] = $catinfo[$itemcat];
                    }
                }
            }
        } else {
            // Just organised by category, so return the flat list.
            $result = xarModAPIfunc('categories', 'user', 'getcatinfo', array('cids' => $catlist));
        }
    }

    return $result;
}

?>