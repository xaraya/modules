<?php

/**
 * Place limits on categories for an item.
 * This will limit the total number of categories that can be added to an item,
 * and will reduce the categories if too many.
 * Future enhancements may allow different limits on different root categories, and 'nobase'
 * flags that can be set differently for different roots.
 *
 * @param module string Module name
 * @param maxcats integer Maximum total number of categories that can be selected for the item
 * @param itemtype integer Item type
 * @param itemid integer Item ID
 * @param nobase boolean Flag to indicate that the base category cannot be selected (remove it if is present)
 * @return No return value defined.
 */

function ievents_adminapi_limit_categories($args)
{
    extract($args);

    if (empty($module)) return;
    $modid = xarModGetIDFromName($module);

    if (empty($itemid)) return;
    if (empty($itemtype)) $itemtype = 0;
    if (empty($maxcats)) $maxcats = 0;

    $nobase = (empty($nobase) ? false : true);

    if (xarModIsHooked('categories', $module, $itemtype)) { // categories is hooked
        // Check the user has not selected too many categories.
        // Get the current cids for the item.
        // TODO: this should be a core feature of the categories module. Remove this
        // code when the categories module supports it directly, i.e. being able to
        // set limits on the number of categories selectable per base category, and per
        // hooked item.
        $itemcats = xarModAPIfunc('categories', 'user', 'groupcount',
            array('modid' => $modid, 'itemtype' => $itemtype, 'itemid' => $itemid)
        );

        if (!empty($itemcats)) {
            $itemcats = array_keys($itemcats);
            $cat_count_before = count($itemcats);

            // Get the base categories
            $catbases = xarModAPIfunc(
                'categories', 'user', 'getallcatbases',
                array('module' => $module, 'itemtype' => $itemtype, 'format' => 'cids')
            );

            // Remove any of the base categories, if they have been selected.
            // TODO: this should be a feature of the categories selection. When it is,
            // it can be removed from this module.
            if ($nobase) $itemcats = array_diff($itemcats, $catbases);

            // If too many cats, strip some off
            if ($maxcats > 0 && count($itemcats) > $maxcats) $itemcats = array_splice($itemcats, 0, $maxcats);

            // Update the cats if we need to reduce any.
            // Only do it if we have a item id (the create or update did not fail)
            if (count($itemcats) <> $cat_count_before) {
                xarModAPIfunc('categories', 'admin', 'linkcat',
                    array(
                        'modid' => $modid,
                        'itemtype' => $itemtype,
                        'iids' => array((int)$itemid),
                        'cids' => $itemcats,
                        'clean_first' => true,
                    )
                );
            }
        }
    } // categories is hooked

    return;
}

?>