<?php

// Get DD data for a set of pages
// pages: array of pages to fetch data for.

function xarpages_userapi_getpagedd($args)
{
    extract($args);

    // TODO: Check if we are hooked to DD and that there are pages to 
    // fetch for, before attempting to fetch fields.
    if (empty($pages)) {
        return;
    }

    $itemtypes = array();

    // Collect information.
    // Organize the item IDs into itemtypes.
    foreach ($pages as $key => $page) {
        if (!isset($itemtypes[$page['ptid']])) {
            $itemtypes[$page['ptid']] = array();
        }
        // The key is the item ID and the value is the key to the
        // source item records, which may or may not be the item ID.
        $itemtypes[$page['ptid']][$page['pid']] = $key;
    }

    $result = array();

    // Loop for each item type, fetching the item DD records for all items
    // within each item type in one go.
    foreach($itemtypes as $itemtype => $items) {
        // Continue to the next page type if this one isn't hooked.
        if (!xarModIsHooked('dynamicdata', 'xarpages', $itemtype)) {
            continue;
        }

        $dd_data = xarMod::apiFunc(
            'dynamicdata', 'user', 'getitems',
            array('module' => 'xarpages', 'itemtype' => $itemtype, 'itemids' => array_keys($items))
        );
        if (empty($dd_data)) $dd_data = array();
        
        // Copy the dd records into the result array, using the
        // same keys as the original items.
        foreach($dd_data as $dd_key => $dd_items) {
            $result[$itemtypes[$itemtype][$dd_key]] = $dd_items;
        }
    }

    return($result);
}

?>